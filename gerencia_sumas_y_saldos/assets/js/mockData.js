// assets/js/mockData.js
// Dataset de ejemplo que dispara cada una de las alertas definidas en config.js
export const mock = {
  // Cuentas con saldos y variaciones
  accounts: [
    {
      cuenta: '1001', // cuenta crítica
      saldo: -5000000, // saldo negativo inicial (alerta)
      variacion: -20,
      estado: 'Negativo'
    },
    {
      cuenta: '2001', // cuenta crítica
      saldo: 150000000, // supera umbral crítico (alerta)
      variacion: 5,
      estado: 'Positivo'
    },
    {
      cuenta: '3002',
      saldo: 2500000,
      variacion: 0,
      estado: 'Balanceado'
    },
    {
      cuenta: '4003',
      saldo: -2000000, // saldo negativo final (alerta)
      variacion: -5,
      estado: 'Negativo'
    }
  ],
  // Asientos contables para validar balance debe/haber
  entries: [
    { id: 1, debe: 5000, haber: 5000 }, // balanceado
    { id: 2, debe: 7000, haber: 6000 } // diferencia debe/haber (alerta)
  ],
  // Movimientos inter‑empresa
  intercompany: [
    { id: 'IC01', cuentaOrigen: '1001', cuentaDestino: '2002', importe: 1200000 }
  ],
  // Proveedores
  providers: [
    { id: 'P001', saldo: 300000, vencidoDias: 45 }, // pendiente >30 días (alerta)
    { id: 'P002', saldo: 50000, vencidoDias: 10 }
  ],
  // Evolución de saldos para gráfico (fechas + valores acumulados)
  balanceEvolution: {
    labels: ['2026‑01‑01', '2026‑02‑01', '2026‑03‑01', '2026‑04‑01'],
    data: [5000000, 8000000, 12000000, 15000000]
  }
};
