/* CONFIG — Parámetros y endpoints */

const CONFIG = {
  criticalThreshold: 10_000_000,
  variationThreshold: 30,
  startDate: '20260601',
  endDate: '20260630',
  currency: 'ARS',
  locale: 'es-AR',

  // Endpoints Node-RED con fallback
  API_PRIMARY:   'http://ingcorona.ddns.net:4040',
  API_FALLBACK:  'http://192.168.0.23:1880',
  API_TIMEOUT_MS: 15000,
  endpoints: {
    sumasSaldos:   '/api/sumas-saldos',
    movimientos:   '/api/sumas-saldos/movimientos',
  },
};
