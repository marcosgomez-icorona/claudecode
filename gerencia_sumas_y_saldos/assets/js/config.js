// assets/js/config.js
// Parámetros de negocio y umbrales de alertas para el tablero de Sumas y Saldos

export const CONFIG = {
  // Umbrales monetarios (USD)
  criticalThreshold: 100_000_000, // $100 M

  // Activación de alertas
  negativeInitialAlert: true,
  negativeFinalAlert: true,
  thresholdAlert: true,
  balanceDiffAlert: true,
  unbalancedEntryAlert: true,
  intercompanyAlert: true,
  providerOverdueAlert: true,
  daysOverdue: 30,

  // Cuentas críticas (ejemplo)
  criticalAccounts: ['1001', '2001', '3001'],

  // Palabras clave sensibles para detección en descripciones
  sensitiveKeywords: [
    'reversión',
    'anulado',
    'diferencia',
    'ajuste',
    'canje',
    'compensación',
    'caja',
    'transferencia',
    'interempresa',
    'préstamo',
    'anticipo'
  ]
};
