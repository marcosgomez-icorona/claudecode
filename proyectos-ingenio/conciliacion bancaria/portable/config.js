/**
 * Config.js — Backends Node-RED para Dashboard de Conciliación Bancaria
 * Patrón Dashboard Portable Corona (MODO A — Node-RED como servidor web)
 *
 * MODO A: url vacía = mismo origen
 * Node-RED sirve el HTML y los endpoints desde el mismo proceso
 * NO requiere CORS porque es mismo origen
 */

const CONFIG = {
  backends: [
    { name: 'Node-RED', url: '', timeout: 3000 }
  ]
};
