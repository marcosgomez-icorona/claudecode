// alertsEngine.js – motor de alertas contables
// Exporta la función evaluateAlerts(data) que recibe el objeto mock (o datos reales) y devuelve un array de alertas.

/**
 * Formatea número a USD con separadores de miles.
 */
function fmtUSD(value) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
}

/**
 * Detecta registros duplicados (mismo número de documento, fecha y monto).
 */
function findDuplicates(records) {
  const map = {};
  const duplicates = [];
  records.forEach(rec => {
    const key = `${rec.document}|${rec.date}|${rec.amount}`;
    if (map[key]) duplicates.push(rec);
    else map[key] = true;
  });
  return duplicates;
}

/**
 * Detecta reversión con importe distinto.
 */
function findInconsistentReversals(records) {
  const reversals = [];
  const indexed = {};
  records.forEach(rec => {
    if (rec.type === 'reversal') {
      const original = indexed[rec.refId];
      if (original && original.amount !== rec.amount) {
        reversals.push({ original, reversal: rec });
      }
    } else {
      indexed[rec.id] = rec;
    }
  });
  return reversals;
}

/**
 * Genera alertas a partir de los datos.
 * data debe contener:
 *   - sumasSaldos: [{cuenta, saldo, variacion}]
 *   - movimientos: [{id, cuenta, amount, date, type, refId, document}]
 *   - proveedores: [{proveedor, saldoPend, ultimaFactura, vencidasDias}]
 */
export function evaluateAlerts(data) {
  const alerts = [];
  const cfg = data.config || {};
  // 1. Saldo crítico
  data.sumasSaldos?.forEach(r => {
    if (r.saldo < 0) {
      alerts.push({ severity: 'crítico', msg: `Saldo negativo en cuenta ${r.cuenta}: ${fmtUSD(r.saldo)}` });
    }
    if (Math.abs(r.saldo) > (cfg.umbSaldo || 100000000)) {
      alerts.push({ severity: 'alto', msg: `Saldo supera umbral en ${r.cuenta}: ${fmtUSD(r.saldo)}` });
    }
    if (Math.abs(r.variacion) > (cfg.umbVariacion || 30)) {
      alerts.push({ severity: 'advertencia', msg: `Variación alta (${r.variacion}%) en ${r.cuenta}` });
    }
  });

  // 2. Duplicados
  const dup = findDuplicates(data.movimientos || []);
  dup.forEach(r => {
    alerts.push({ severity: 'advertencia', msg: `Movimiento duplicado: ${r.document} ${fmtUSD(r.amount)} en ${r.cuenta}` });
  });

  // 3. Reversión incoherente
  const rev = findInconsistentReversals(data.movimientos || []);
  rev.forEach(p => {
    alerts.push({ severity: 'crítico', msg: `Reversión incoherente: ${p.original.id} vs ${p.reversal.id}` });
  });

  // 4. Interempresas (códigos de cuenta que empiezan con "IE-")
  data.movimientos?.forEach(m => {
    if (m.cuenta && m.cuenta.startsWith('IE-')) {
      alerts.push({ severity: 'info', msg: `Movimiento interempresa detectado en ${m.cuenta}` });
    }
  });

  // 5. Proveedores pendientes/vencidos
  data.proveedores?.forEach(p => {
    if (p.saldoPend > (cfg.umbProv || 50000)) {
      alerts.push({ severity: 'alto', msg: `Saldo pendiente alto (${fmtUSD(p.saldoPend)}) para ${p.proveedor}` });
    }
    if (p.vencidasDias > (cfg.diasVencidos || 30)) {
      alerts.push({ severity: 'crítico', msg: `Facturas vencidas ${p.vencidasDias} días para ${p.proveedor}` });
    }
  });

  return alerts;
}

// Exportar también la función de formateo para UI.
export { fmtUSD };
