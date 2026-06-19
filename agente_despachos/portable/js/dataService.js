/**
 * dataService.js — Capa de datos con failover multi-backend
 * Patrón Dashboard Portable Corona
 *
 * Intenta cada backend definido en CONFIG.backends en orden.
 * Si todos fallan, usa mock data como último recurso.
 */
import { CONFIG } from '../config.js';
import { mock, mockDetalle } from './mockData.js';

/* ─── Detección de modo ─── */
const isFileProtocol = window.location.protocol === 'file:';
const isSameOrigin = !isFileProtocol && (!window.location.host || window.location.port === '1880');

/**
 * apiFetch con failover multi-backend
 * Itera CONFIG.backends en orden. El primero que responde OK gana.
 *
 * @param {string} path - Ruta del endpoint (ej: '/api/despachos/pendientes?days=30')
 * @param {object} [options] - Opciones de fetch (method, headers, body)
 * @returns {Promise<object>} - JSON parseado
 */
async function apiFetch(path, options = {}) {
  const errors = [];

  for (const backend of CONFIG.backends) {
    // En protocolo file://, saltamos backends con url vacía (no hay mismo origen)
    if (isFileProtocol && !backend.url) continue;

    try {
      const controller = new AbortController();
      const timer = setTimeout(() => controller.abort(), backend.timeout);

      const res = await fetch(backend.url + path, {
        ...options,
        signal: controller.signal,
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          ...(options.headers || {})
        }
      });

      clearTimeout(timer);

      if (!res.ok) {
        const text = await res.text().catch(() => '');
        throw new Error(`HTTP ${res.status}: ${text || res.statusText}`);
      }

      const data = await res.json();
      // Éxito — registrar backend usado (debug)
      console.log('[Despachos] Backend ' + backend.name + ' respondió OK — ' + path);
      return data;

    } catch (err) {
      const reason = err.name === 'AbortError'
        ? `Timeout (${backend.timeout}ms)`
        : err.message;
      console.warn('[Despachos] Backend ' + backend.name + ' falló: ' + reason);
      errors.push({ backend: backend.name, error: reason });
    }
  }

  // Todos los backends fallaron → fallback a mock si estamos en file:// o development
  if (isFileProtocol || isSameOrigin || window.location.hostname === 'localhost') {
    console.warn('[Despachos] Todos los backends fallaron — usando mock data');
    return null; // el caller decide si usa mock
  }

  // Error final con detalle de cada backend
  const detail = errors.map(e => `  • ${e.backend}: ${e.error}`).join('\n');
  throw new Error('Todos los backends fallaron:\n' + detail);
}

/**
 * Obtiene la lista de despachos pendientes de facturación
 * @param {number} days - Cantidad de días hacia atrás
 */
export async function getPendientes(days = CONFIG.defaults.daysBack) {
  const path = `${CONFIG.endpoints.pendientes}?days=${days}`;
  const result = await apiFetch(path);

  if (result !== null) return result;

  // Fallback a mock
  await new Promise(r => setTimeout(r, 300 + Math.random() * 400));
  return mock;
}

/**
 * Obtiene detalle completo de un remito
 * @param {string} remitoId - Número de remito
 */
export async function getDetalleRemito(remitoId) {
  const path = `${CONFIG.endpoints.detalle}/${encodeURIComponent(remitoId)}`;
  const result = await apiFetch(path);

  if (result !== null) return result;

  // Fallback a mock
  await new Promise(r => setTimeout(r, 200));
  return { ...mockDetalle, remito: remitoId };
}

/**
 * Obtiene resumen de KPIs
 * @param {number} days - Días hacia atrás
 */
export async function getResumen(days = CONFIG.defaults.daysBack) {
  const path = `${CONFIG.endpoints.resumen}?days=${days}`;
  const result = await apiFetch(path);

  if (result !== null) return result;

  // Fallback a mock
  await new Promise(r => setTimeout(r, 200));
  return mock.resumen;
}

/**
 * Vincula un remito con una factura (operación restringida)
 * @param {string} remitoId - Número de remito
 * @param {string} factura - Número de factura
 */
export async function vincularFactura(remitoId, factura) {
  const path = `${CONFIG.endpoints.facturar}/${encodeURIComponent(remitoId)}/facturar`;
  const result = await apiFetch(path, {
    method: 'POST',
    body: JSON.stringify({ factura })
  });

  if (result !== null) return result;

  // Mock: simular éxito
  await new Promise(r => setTimeout(r, 500));
  return { success: true, remito: remitoId, factura };
}
