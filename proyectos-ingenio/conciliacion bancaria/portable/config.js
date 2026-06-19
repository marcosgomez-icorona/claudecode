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
    { name: 'Cloud', url: 'http://ingcorona.ddns.net:4040', timeout: 5000 },
    { name: 'LAN',   url: 'http://192.168.0.23:1880',       timeout: 5000 }
  ]
};
