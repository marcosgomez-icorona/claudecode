/* CONFIG — Parámetros y endpoints */

const CONFIG = {
  criticalThreshold: 10_000_000,
  variationThreshold: 30,
  startDate: '20250101',
  endDate: '20260630',
  currency: 'ARS',
  locale: 'es-AR',

  // Endpoints Node-RED con fallback
  API_PRIMARY:   'http://ingcorona.ddns.net:4040',
  API_FALLBACK:  'http://192.168.0.23:1880',
  API_TIMEOUT_MS: 45000,
  endpoints: {
    sumasSaldos:   '/api/sumas-saldos',
    movimientos:   '/api/sumas-saldos/movimientos',
    mayor:         '/api/sumas-saldos/mayor',
    libroDiario:   '/api/sumas-saldos/libro-diario',
    egresos:       '/api/sumas-saldos/egresos',
    ingresos:      '/api/sumas-saldos/ingresos',
    provSaldos:    '/api/sumas-saldos/proveedores-saldos',
    provPendientes:'/api/sumas-saldos/proveedores-pendientes',
    imputaciones:  '/api/sumas-saldos/imputaciones',
    interempresas: '/api/sumas-saldos/interempresas',
  },
};
