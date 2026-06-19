/**
 * Config.js — Backends Node-RED para Dashboard de Conciliación Bancaria
 * Patrón Sumas y Saldos (funcional en producción)
 *
 * API_PRIMARY intenta Cloud primero, API_FALLBACK cae a LAN local
 * TIMEOUT 45s para consultas pesadas
 */

const CONFIG = {
  API_PRIMARY:   'http://ingcorona.ddns.net:4040',
  API_FALLBACK:  'http://192.168.0.23:1880',
  API_TIMEOUT_MS: 45000,
  endpoints: {
    resumen:    '/api/conciliacion/resumen',
    pendientes: '/api/conciliacion/pendientes',
    detalle:    '/api/conciliacion/detalle',
  }
};
