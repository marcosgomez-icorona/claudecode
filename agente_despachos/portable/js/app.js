/**
 * app.js — Orquestador principal del Dashboard Despachos Pendientes de Facturación
 * Patrón Dashboard Portable Corona — v1.0.0
 *
 * Funciona en 3 modos:
 *   A — Node-RED httpStatic (mismo origen, url: '')
 *   B — LAN (fetch a 192.168.0.23:1880)
 *   C — Cloud (fetch a ingcorona.ddns.net:4040)
 *
 * Si todos los backends fallan, usa mock data automáticamente.
 */
import { CONFIG } from '../config.js';
import { getPendientes, getDetalleRemito, vincularFactura } from './dataService.js';
import { renderTablaPendientes, initModal, renderDetalleRemito, mostrarModal, ocultarModal } from './renderTables.js';

/* ─── Estado global ─── */
const state = {
  data: [],
  filtered: [],
  daysBack: CONFIG.defaults.daysBack,
  orderBy: null,
  orderDir: 'asc'
};

/* ─── Helpers de formato ─── */

function fmtMoneda(val) {
  if (val == null || isNaN(val)) return '-';
  return '$ ' + Number(val).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function fmtCantidad(val) {
  if (val == null || isNaN(val)) return '-';
  return Number(val).toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

/* ─── KPIs ─── */

function actualizarKPIs(resumen) {
  const r = resumen || {};
  setKPI('kpiRemitos',   r.totalRemitos || 0, 'Remitos pendientes');
  setKPI('kpiClientes',  r.totalClientes || 0, 'Clientes');
  setKPI('kpiBolsas',    fmtCantidad(r.totalBolsas || 0), 'Bolsas');
  setKPI('kpiToneladas', fmtCantidad(r.totalToneladas || 0), 'Toneladas');
  setKPI('kpiImporte',   fmtMoneda(r.totalImporte || 0), 'Total facturable');

  // Chips de productos
  const prodContainer = document.getElementById('kpiProductos');
  if (prodContainer && r.productos) {
    const entries = Object.entries(r.productos).sort((a, b) => b[1] - a[1]);
    prodContainer.innerHTML = entries.map(([prod, cant]) =>
      `<div class="chip-producto">
        <span class="chip-nombre">${prod}</span>
        <span class="chip-cant">${fmtCantidad(cant)}</span>
      </div>`).join('');
  }
}

function setKPI(id, value, label) {
  const el = document.getElementById(id);
  if (!el) return;
  el.innerHTML = `<span class="kpi-value">${value}</span><span class="kpi-label">${label}</span>`;
}

/* ─── Fecha de actualización ─── */

function actualizarFecha() {
  const el = document.getElementById('fechaActualizacion');
  if (!el) return;
  const now = new Date();
  el.textContent = now.toLocaleString('es-AR', {
    day: '2-digit', month: 'long', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  });
}

/* ─── Filtros ─── */

function initFiltros() {
  const selDays = document.getElementById('filterDays');
  const btnFilter = document.getElementById('btnFilter');
  const inputSearch = document.getElementById('filterSearch');

  if (selDays) {
    selDays.value = state.daysBack;
    selDays.addEventListener('change', () => {
      state.daysBack = parseInt(selDays.value) || 30;
    });
    selDays.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') cargarDatos();
    });
  }

  if (btnFilter) {
    btnFilter.addEventListener('click', () => cargarDatos());
  }

  if (inputSearch) {
    inputSearch.addEventListener('input', () => aplicarFiltroLocal(inputSearch.value));
  }
}

function aplicarFiltroLocal(searchTerm) {
  const term = (searchTerm || '').toLowerCase().trim();
  if (!term) {
    state.filtered = [...state.data];
  } else {
    state.filtered = state.data.filter(item =>
      (item.remito || '').toLowerCase().includes(term) ||
      (item.cliente || '').toLowerCase().includes(term) ||
      (item.producto || '').toLowerCase().includes(term) ||
      (item.cuit || '').includes(term) ||
      (item.transportista || '').toLowerCase().includes(term)
    );
  }
  renderTablaPendientes(state.filtered);
}

/* ─── Carga de datos ─── */

function mostrarLoading() {
  const container = document.getElementById('tablaPendientes');
  if (!container) return;
  container.innerHTML = `
    <div class="loading-state">
      <div class="spinner"></div>
      <p>${CONFIG.labels.loadingMessage}</p>
    </div>`;
}

function mostrarError(err) {
  const container = document.getElementById('tablaPendientes');
  if (!container) return;
  container.innerHTML = `
    <div class="error-state">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#A32D2D" stroke-width="1.5">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      <p>${CONFIG.labels.errorMessage}</p>
      <p class="error-detail">${err.message}</p>
      <button class="btn btn-primary" onclick="window._reintentar()">Reintentar</button>
    </div>`;
}

async function cargarDatos() {
  mostrarLoading();

  try {
    const data = await getPendientes(state.daysBack);

    state.data = data.data || [];
    state.filtered = [...state.data];

    // KPIs vienen en data.resumen
    actualizarKPIs(data.resumen);

    // Tabla
    renderTablaPendientes(state.filtered);

    // Fecha
    actualizarFecha();

  } catch (err) {
    console.error('[Despachos] Error cargando datos:', err);
    mostrarError(err);
  }
}

/* ─── Acciones ─── */

async function verDetalle(remitoId) {
  try {
    const detalle = await getDetalleRemito(remitoId);
    renderDetalleRemito(detalle);
    mostrarModal();
  } catch (err) {
    alert('Error al cargar detalle: ' + err.message);
  }
}

async function vincular(remitoId) {
  const remito = remitoId || '';
  const factura = prompt(`Ingresá el número de factura para vincular al remito ${remito}:`);
  if (!factura || factura.trim() === '') return;

  try {
    const result = await vincularFactura(remito, factura.trim());
    if (result && result.success) {
      alert(`Remito ${remito} vinculado con factura ${factura.trim()} correctamente.`);
      ocultarModal();
      cargarDatos();
    } else {
      alert('No se pudo vincular. Verificá los datos e intentá de nuevo.');
    }
  } catch (err) {
    alert('Error al vincular factura: ' + err.message);
  }
}

/* ─── Exposición global para onclick en HTML ─── */

window._verDetalle = verDetalle;
window._vincularFactura = vincular;
window._cerrarModal = ocultarModal;
window._reintentar = cargarDatos;

/* ─── Inicialización ─── */

function init() {
  initModal();
  initFiltros();
  actualizarFecha();
  cargarDatos();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
