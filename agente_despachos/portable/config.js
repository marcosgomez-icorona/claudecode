/**
 * config.js — Backends Node-RED para Despachos Pendientes de Facturación
 * Patrón Dashboard Portable Corona
 *
 * MODO A (Node-RED local — mismo origen):
 *   Se sirve desde httpStatic de Node-RED → todos los backends con url: ''
 *
 * MODO B (LAN — acceso directo a IP):
 *   Se abre el HTML desde disco o Apache → configura url de Node-RED
 *
 * MODO C (Cloud — acceso externo):
 *   Se sube portable/ a hosting → apunta a Node-RED cloud vía DDNS
 *
 * El frontend itera los backends en orden. Si uno falla (timeout/error),
 * prueba el siguiente. Si todos fallan, usa mock data como último recurso.
 */

const CONFIG = {
  // Backends Node-RED en orden de preferencia
  backends: [
    // Modo A: mismo origen (cuando Node-RED sirve el HTML vía httpStatic)
    { name: 'Node-RED', url: '', timeout: 5000 },

    // Modo B: LAN — acceso directo al Node-RED del ingenio
    { name: 'LAN', url: 'http://192.168.0.23:1880', timeout: 5000 },

    // Modo C: Cloud — Node-RED accesible desde internet
    { name: 'Cloud', url: 'http://ingcorona.ddns.net:4040', timeout: 8000 }
  ],

  // API endpoints (relativos a la base del backend)
  endpoints: {
    pendientes:  '/api/despachos/pendientes',
    detalle:     '/api/despachos/pendientes',    // + /:remito
    resumen:     '/api/despachos/resumen',
    health:      '/api/despachos/health',
    facturar:    '/api/despachos/pendientes',    // + /:remito/facturar
    syncSheets:  '/api/despachos/sync-sheets',
    sheetsStatus:'/api/despachos/sheets-status'
  },

  // Filtros por defecto
  defaults: {
    daysBack: 30,
    pageSize: 25
  },

  // Colores por tipo de producto (design system Corona)
  productColors: {
    'AZUCAR COMUN TIPO A': '#1D9E75',
    'AZUCAR CRUDO': '#185FA5',
    'AZUCAR GRADO 4 EXPO CHILE': '#BA7517'
  },

  // Etiquetas para UI
  labels: {
    appTitle: 'Despachos Pendientes de Facturación',
    appSubtitle: 'Ingenio La Corona',
    emptyTable: 'No se encontraron despachos pendientes para el filtro seleccionado.',
    loadingMessage: 'Cargando datos…',
    errorMessage: 'Error al cargar los datos. Verificá la conexión con Node-RED.'
  }
};
