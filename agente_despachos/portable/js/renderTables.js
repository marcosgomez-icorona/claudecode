// assets/js/renderTables.js
// Renderizado de tablas y modales para Despachos Pendientes de Facturación
import { CONFIG } from '../config.js';
import { getDetalleRemito } from './dataService.js';

/* ─── Helpers de formato ─── */

function fmtMoneda(val) {
  if (val == null || isNaN(val)) return '-';
  return '$ ' + Number(val).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function fmtFecha(iso) {
  if (!iso) return '-';
  const d = new Date(iso);
  if (isNaN(d.getTime())) return iso;
  return d.toLocaleDateString('es-AR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function fmtCantidad(val) {
  if (val == null || isNaN(val)) return '-';
  return Number(val).toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

function badgeClasificacion(clasif) {
  const map = {
    'APTO_PARA_PROGRAMAR': { label: 'Apto', cls: 'badge-apto' },
    'BLOQUEADO': { label: 'Bloqueado', cls: 'badge-bloq' },
    'PENDIENTE_VALIDACION': { label: 'Pendiente', cls: 'badge-pend' },
    'REQUIERE_APROBACION_HUMANA': { label: 'Requiere aprobación', cls: 'badge-aprob' }
  };
  const m = map[clasif];
  if (!m) return '';
  return `<span class="badge-clasif ${m.cls}">${m.label}</span>`;
}

function badgeProducto(producto) {
  const color = CONFIG.productColors[producto] || '#6B7280';
  return `<span class="badge-producto" style="background:${color}20;color:${color};border:1px solid ${color}40">${producto}</span>`;
}

/* ─── Tabla principal ─── */

export function renderTablaPendientes(data, containerId = 'tablaPendientes') {
  const container = document.getElementById(containerId);
  if (!container) return;

  if (!data || !data.length) {
    container.innerHTML = `
      <div class="empty-state">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#9BA3AF" stroke-width="1.5">
          <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
          <rect x="9" y="3" width="6" height="4" rx="1"/>
          <path d="M9 14l2 2 4-4"/>
        </svg>
        <p>${CONFIG.labels.emptyTable}</p>
      </div>`;
    return;
  }

  const tbody = data.map(item => {
    const totalItem = item.totalItem != null ? item.totalItem : (item.cantidad * item.precio);
    const tieneItems = item.totalItems > 1 || item.itemsCount > 1;

    return `
      <tr class="tr-pendiente" data-remito="${item.remito}">
        <td class="td-remito">
          <button class="btn-link-remito" onclick="window._verDetalle('${item.remito}')" title="Ver detalle">
            ${item.remito}
          </button>
        </td>
        <td class="td-fecha" data-order="${item.fecha || ''}">${fmtFecha(item.fecha)}</td>
        <td class="td-cliente">${item.cliente || '-'}</td>
        <td class="td-prod">${badgeProducto(item.producto)}</td>
        <td class="td-cantidad text-end">${fmtCantidad(item.cantidad)}</td>
        <td class="td-unidad">${item.unidad || item.unidad2 || '-'}</td>
        <td class="td-precio text-end">${fmtMoneda(item.precio)}</td>
        <td class="td-total text-end">${fmtMoneda(totalItem)}</td>
        <td class="td-transporte">${item.transportista ? item.transportista.substring(0, 25) : '-'}</td>
        <td class="td-clasif">${badgeClasificacion(item.clasificacionAgente)}</td>
        <td class="td-accion">
          <div class="acciones-cell">
            <button class="btn-icon" onclick="window._verDetalle('${item.remito}')" title="Ver detalle">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
            <button class="btn-icon" onclick="window._vincularFactura('${item.remito}')" title="Vincular con factura">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
              </svg>
            </button>
          </div>
        </td>
      </tr>`;
  }).join('');

  container.innerHTML = `
    <div class="table-wrapper">
      <table class="table-despachos" id="mainTable">
        <thead>
          <tr>
            <th>Remito</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Producto</th>
            <th class="text-end">Cantidad</th>
            <th>Unidad</th>
            <th class="text-end">Precio</th>
            <th class="text-end">Total</th>
            <th>Transporte</th>
            <th>Agente</th>
            <th class="td-accion">Acción</th>
          </tr>
        </thead>
        <tbody>${tbody}</tbody>
      </table>
    </div>
    <div class="table-footer">
      <span class="table-count">${data.length} remitos</span>
    </div>`;
}

/* ─── Modal de detalle ─── */

const MODAL_HTML = `
<div id="modalDetalle" class="modal-overlay" style="display:none">
  <div class="modal-container">
    <div class="modal-header">
      <h3 id="modalTitle">Detalle del Remito</h3>
      <button class="btn-close-modal" onclick="window._cerrarModal()">&times;</button>
    </div>
    <div class="modal-body" id="modalBody">
      <div class="modal-loading">Cargando detalle...</div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="window._cerrarModal()">Cerrar</button>
      <button class="btn btn-primary" id="btnVincularModal" onclick="window._vincularFactura()">Vincular con factura</button>
    </div>
  </div>
</div>`;

export function initModal() {
  if (!document.getElementById('modalDetalle')) {
    const div = document.createElement('div');
    div.innerHTML = MODAL_HTML;
    document.body.appendChild(div.firstElementChild);
  }
}

export function renderDetalleRemito(detalle) {
  const body = document.getElementById('modalBody');
  if (!body) return;

  const itemsHtml = (detalle.items || []).map(item => `
    <tr>
      <td>${item.orden || '-'}</td>
      <td>${item.producto || '-'}</td>
      <td class="text-end">${fmtCantidad(item.cantidad)}</td>
      <td>${item.unidad || item.unidad2 || '-'}</td>
      <td class="text-end">${fmtMoneda(item.precio)}</td>
      <td class="text-end">${fmtMoneda(item.total || item.cantidad * item.precio)}</td>
    </tr>`).join('') || '<tr><td colspan="6" class="empty-cell">Sin items</td></tr>';

  const total = detalle.totalImporte || (detalle.items || []).reduce((s, i) => s + (i.total || i.cantidad * i.precio || 0), 0);

  body.innerHTML = `
    <div class="detalle-grid">
      <div class="detalle-section">
        <h4>Datos del remito</h4>
        <div class="detalle-row"><span class="detalle-label">Remito</span><span class="detalle-value">${detalle.remito || '-'}</span></div>
        <div class="detalle-row"><span class="detalle-label">Fecha</span><span class="detalle-value">${fmtFecha(detalle.fecha)}</span></div>
        <div class="detalle-row"><span class="detalle-label">CUIT</span><span class="detalle-value">${detalle.cuit || '-'}</span></div>
        <div class="detalle-row"><span class="detalle-label">Guía</span><span class="detalle-value">${detalle.guia || detalle.numero_pesada || '-'}</span></div>
        <div class="detalle-row"><span class="detalle-label">Usuario</span><span class="detalle-value">${detalle.usuario || '-'}</span></div>
        <div class="detalle-row ${detalle.factura ? '' : 'detalle-warning'}">
          <span class="detalle-label">Factura</span>
          <span class="detalle-value">${detalle.factura || 'Pendiente'}</span>
        </div>
      </div>
      <div class="detalle-section">
        <h4>Cliente</h4>
        <div class="detalle-row"><span class="detalle-label">Razón Social</span><span class="detalle-value">${detalle.cliente || detalle.razonsocial || '-'}</span></div>
        <div class="detalle-row"><span class="detalle-label">Domicilio</span><span class="detalle-value">${detalle.domicilio || '-'}</span></div>
        <div class="detalle-row"><span class="detalle-label">Localidad</span><span class="detalle-value">${detalle.localidad || '-'}</span></div>
        <div class="detalle-row"><span class="detalle-label">Destino</span><span class="detalle-value">${detalle.destino || '-'}</span></div>
      </div>
      <div class="detalle-section">
        <h4>Transporte</h4>
        <div class="detalle-row"><span class="detalle-label">Transportista</span><span class="detalle-value">${detalle.transportista || '-'}</span></div>
        <div class="detalle-row"><span class="detalle-label">Chofer</span><span class="detalle-value">${detalle.chofer || detalle.choferCamion || '-'}</span></div>
        <div class="detalle-row"><span class="detalle-label">DNI</span><span class="detalle-value">${detalle.dniChofer || detalle.dni || '-'}</span></div>
        <div class="detalle-row"><span class="detalle-label">Patente</span><span class="detalle-value">${detalle.patente || '-'}</span></div>
        <div class="detalle-row"><span class="detalle-label">Chasis</span><span class="detalle-value">${detalle.chasis || '-'}</span></div>
      </div>
      ${detalle.clasificacionAgente ? `
      <div class="detalle-section">
        <h4>Clasificación Agente de Despachos</h4>
        <div class="detalle-row">
          <span class="detalle-label">Estado</span>
          <span class="detalle-value">${badgeClasificacion(detalle.clasificacionAgente)}</span>
        </div>
      </div>` : ''}
    </div>
    <div class="detalle-items">
      <h4>Items del remito</h4>
      <table class="table-despachos table-compact">
        <thead>
          <tr>
            <th>Orden</th>
            <th>Producto</th>
            <th class="text-end">Cantidad</th>
            <th>Unidad</th>
            <th class="text-end">Precio</th>
            <th class="text-end">Total</th>
          </tr>
        </thead>
        <tbody>${itemsHtml}</tbody>
        <tfoot>
          <tr class="tr-total">
            <td colspan="5" class="text-end">Total general</td>
            <td class="text-end">${fmtMoneda(total)}</td>
          </tr>
        </tfoot>
      </table>
    </div>
    ${detalle.observaciones ? `
    <div class="detalle-obs">
      <h4>Observaciones</h4>
      <p>${detalle.observaciones}</p>
    </div>` : ''}`;

  document.getElementById('modalTitle').textContent = `Remito ${detalle.remito}`;
  const btnVincular = document.getElementById('btnVincularModal');
  if (btnVincular) {
    btnVincular.onclick = () => window._vincularFactura(detalle.remito);
  }
}

export function mostrarModal() {
  const modal = document.getElementById('modalDetalle');
  if (modal) modal.style.display = 'flex';
}

export function ocultarModal() {
  const modal = document.getElementById('modalDetalle');
  if (modal) modal.style.display = 'none';
}
