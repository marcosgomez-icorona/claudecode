// assets/js/dataService.js
// Capa de datos: llama a Node-RED API o usa mock data para desarrollo
import { CONFIG } from './config.js';
import { mock, mockDetalle } from './mockData.js';

const USE_MOCK = !CONFIG.api.baseUrl && !window.location.href.includes('node-red');

/**
 * Fetch wrapper con timeout y manejo de errores
 */
async function apiFetch(url, options = {}) {
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), 15000);

  try {
    const res = await fetch(url, {
      ...options,
      signal: controller.signal,
      headers: {
        'Accept': 'application/json',
        ...(options.headers || {})
      }
    });
    if (!res.ok) {
      const text = await res.text().catch(() => '');
      throw new Error(`HTTP ${res.status}: ${text || res.statusText}`);
    }
    return await res.json();
  } catch (err) {
    if (err.name === 'AbortError') throw new Error('Timeout: el servidor no respondió en 15s');
    throw err;
  } finally {
    clearTimeout(timeout);
  }
}

/**
 * Obtiene la lista de despachos pendientes de facturación
 * @param {number} days - Cantidad de días hacia atrás
 */
export async function getPendientes(days = CONFIG.defaults.daysBack) {
  if (USE_MOCK) {
    // Simular delay de red
    await new Promise(r => setTimeout(r, 300 + Math.random() * 400));
    return mock;
  }

  const baseUrl = CONFIG.api.baseUrl || '';
  const url = `${baseUrl}${CONFIG.api.endpoints.pendientes}?days=${days}`;
  return await apiFetch(url);
}

/**
 * Obtiene detalle completo de un remito
 * @param {string} remitoId - Número de remito
 */
export async function getDetalleRemito(remitoId) {
  if (USE_MOCK) {
    await new Promise(r => setTimeout(r, 200));
    return { ...mockDetalle, remito: remitoId };
  }

  const baseUrl = CONFIG.api.baseUrl || '';
  const url = `${baseUrl}${CONFIG.api.endpoints.detalle}/${encodeURIComponent(remitoId)}`;
  return await apiFetch(url);
}

/**
 * Obtiene resumen de KPIs
 */
export async function getResumen(days = CONFIG.defaults.daysBack) {
  if (USE_MOCK) {
    await new Promise(r => setTimeout(r, 200));
    return mock.resumen;
  }

  const baseUrl = CONFIG.api.baseUrl || '';
  const url = `${baseUrl}${CONFIG.api.endpoints.resumen}?days=${days}`;
  return await apiFetch(url);
}

/**
 * Vincula un remito con una factura (operación restringida)
 * @param {string} remitoId - Número de remito
 * @param {string} factura - Número de factura
 */
export async function vincularFactura(remitoId, factura) {
  if (USE_MOCK) {
    await new Promise(r => setTimeout(r, 500));
    return { success: true, remito: remitoId, factura };
  }

  const baseUrl = CONFIG.api.baseUrl || '';
  const url = `${baseUrl}${CONFIG.api.endpoints.pendientes}/${encodeURIComponent(remitoId)}/facturar`;
  return await apiFetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ factura })
  });
}
