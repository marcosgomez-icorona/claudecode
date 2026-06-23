#!/usr/bin/env node
/**
 * Snapshot Engine — Sumas y Saldos
 * Extrae snapshots contables desde MSSQL, persiste como JSON,
 * compara períodos consecutivos y genera alertas por variación.
 *
 * Uso:
 *   node snapshot-engine.js extract 20260601 20260630
 *   node snapshot-engine.js list
 *   node snapshot-engine.js compare-latest
 *   node snapshot-engine.js api-snapshot-list
 *   node snapshot-engine.js api-comparison <id>
 *   node snapshot-engine.js api-alerts <comparisonId> [severity]
 */

const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

const DATA_DIR = path.join(__dirname, '..', 'data', 'snapshots');
const COMPARISONS_DIR = path.join(__dirname, '..', 'data', 'comparisons');
const ALERTS_DIR = path.join(__dirname, '..', 'data', 'alerts');

[DATA_DIR, COMPARISONS_DIR, ALERTS_DIR].forEach(d => {
  if (!fs.existsSync(d)) fs.mkdirSync(d, { recursive: true });
});

function uuid() { return crypto.randomUUID(); }
function parseAccountCode(fullCode) {
  const match = String(fullCode || '').match(/^([\d.]+)/);
  return match ? match[1] : String(fullCode || '').trim();
}

function buildSnapshotQuery(params) {
  const { fechaDesde, fechaHasta, cuentaDesde = '0', cuentaHasta = '9' } = params;
  return `
SELECT V_EZI_CUENTAS.CODIGO, V_EZI_CUENTAS.DESCRIPCION AS CUENTA,
    V_EZI_CUENTAS.CRubro, V_EZI_CUENTAS.NRubro,
    V_EZI_CUENTAS.CSubrubro1, V_EZI_CUENTAS.NSubrubro1,
    V_EZI_CUENTAS.CSubrubro2, V_EZI_CUENTAS.NSubrubro2,
    V_EZI_CUENTAS.CSubrubro3, V_EZI_CUENTAS.NSubrubro3,
    SUM(V_VALOR_.IMPORTE) AS DEBE_PERIODO,
    SUM(V_VALOR_1.IMPORTE) AS HABER_PERIODO,
    SUM(V_VALOR_.IMPORTE) - SUM(V_VALOR_1.IMPORTE) AS SALDO_PERIODO
FROM V_TRCONTABLE_
INNER JOIN V_ITEMCONTABLE_ ON V_TRCONTABLE_.ITEMSTRANSACCION_ID = V_ITEMCONTABLE_.BO_PLACE_ID
INNER JOIN V_VALOR_ ON V_ITEMCONTABLE_.DEBE_ID = V_VALOR_.ID
INNER JOIN V_VALOR_ AS V_VALOR_1 ON V_ITEMCONTABLE_.HABER_ID = V_VALOR_1.ID
INNER JOIN V_EZI_CUENTAS ON V_ITEMCONTABLE_.REFERENCIA_ID = V_EZI_CUENTAS.ID
WHERE SUBSTRING(V_TRCONTABLE_.FECHAAPLICACION, 1, 8) >= '${fechaDesde}'
  AND SUBSTRING(V_TRCONTABLE_.FECHAAPLICACION, 1, 8) <= '${fechaHasta}'
  AND V_EZI_CUENTAS.CODIGO BETWEEN '${cuentaDesde}' AND '${cuentaHasta}'
  AND V_TRCONTABLE_.ESTADO = 'C'
GROUP BY V_EZI_CUENTAS.CODIGO, V_EZI_CUENTAS.DESCRIPCION,
    V_EZI_CUENTAS.CRubro, V_EZI_CUENTAS.NRubro,
    V_EZI_CUENTAS.CSubrubro1, V_EZI_CUENTAS.NSubrubro1,
    V_EZI_CUENTAS.CSubrubro2, V_EZI_CUENTAS.NSubrubro2,
    V_EZI_CUENTAS.CSubrubro3, V_EZI_CUENTAS.NSubrubro3
ORDER BY V_EZI_CUENTAS.CODIGO`;
}

const ALERT_RULES = [
  { code: 'BALANCE_CRITICAL', severity: 'critical', threshold: 500_000_000 },
  { code: 'VARIATION_HIGH', severity: 'warning', threshold: 30 },
  { code: 'NEW_ACCOUNT', severity: 'info' },
  { code: 'MISSING_ACCOUNT', severity: 'warning' },
  { code: 'BALANCE_FLIP', severity: 'warning' },
  { code: 'ZERO_MOVEMENT', severity: 'info' }
];

function transformMSSQLRows(rows) {
  return rows.map(r => ({
    account_code_full: String(r.CODIGO || ''),
    account_code: parseAccountCode(r.CODIGO),
    account_name: String(r.CUENTA || ''),
    rubro_code: String(r.CRubro || ''), rubro_name: String(r.NRubro || ''),
    subrubro1_code: String(r.CSubrubro1 || ''), subrubro1_name: String(r.NSubrubro1 || ''),
    subrubro2_code: String(r.CSubrubro2 || ''), subrubro2_name: String(r.NSubrubro2 || ''),
    subrubro3_code: String(r.CSubrubro3 || ''), subrubro3_name: String(r.NSubrubro3 || ''),
    debit_period: parseFloat(r.DEBE_PERIODO) || 0,
    credit_period: parseFloat(r.HABER_PERIODO) || 0,
    balance_period: parseFloat(r.SALDO_PERIODO) || 0
  }));
}

function saveSnapshot(data) {
  const id = uuid();
  const filePath = path.join(DATA_DIR, `${id}.json`);
  const snapshot = {
    id, source_type: 'MCP_CALIPSO', query_version: 'snapshot_period_v1',
    period_from: data.period_from, period_to: data.period_to,
    mode: 'mensual', requested_by: 'snapshot-engine',
    row_count: (data.rows || []).length,
    total_debit: (data.rows || []).reduce((a, r) => a + r.debit_period, 0),
    total_credit: (data.rows || []).reduce((a, r) => a + r.credit_period, 0),
    total_balance: (data.rows || []).reduce((a, r) => a + r.balance_period, 0),
    status: 'COMPLETED', created_at: new Date().toISOString(),
    rows: (data.rows || []).map((r, i) => ({ row_number: i + 1, ...r }))
  };
  fs.writeFileSync(filePath, JSON.stringify(snapshot, null, 2), 'utf-8');
  console.log(`Snapshot guardado: ${filePath} (${snapshot.row_count} cuentas)`);
  return snapshot;
}

function loadSnapshot(id) {
  const filePath = path.join(DATA_DIR, `${id}.json`);
  if (!fs.existsSync(filePath)) throw new Error(`Snapshot no encontrado: ${id}`);
  return JSON.parse(fs.readFileSync(filePath, 'utf-8'));
}

function listSnapshots() {
  if (!fs.existsSync(DATA_DIR)) return [];
  return fs.readdirSync(DATA_DIR).filter(f => f.endsWith('.json')).map(f => {
    const s = JSON.parse(fs.readFileSync(path.join(DATA_DIR, f), 'utf-8'));
    return { id: s.id, period_from: s.period_from, period_to: s.period_to,
      row_count: s.row_count, total_debit: s.total_debit, total_credit: s.total_credit,
      total_balance: s.total_balance, status: s.status, created_at: s.created_at };
  }).sort((a, b) => b.created_at.localeCompare(a.created_at));
}

function compareSnapshots(current, previous) {
  const curMap = new Map(); (current.rows || []).forEach(r => curMap.set(r.account_code_full, r));
  const prevMap = new Map(); (previous.rows || []).forEach(r => prevMap.set(r.account_code_full, r));
  const allKeys = new Set([...curMap.keys(), ...prevMap.keys()]);
  const rows = [];
  for (const key of allKeys) {
    const cur = curMap.get(key); const prev = prevMap.get(key);
    let ct = 'UNCHANGED';
    if (cur && !prev) ct = 'NEW';
    else if (!cur && prev) ct = 'MISSING';
    else if (cur.balance_period !== prev.balance_period) ct = 'CHANGED';
    const cd=cur?cur.debit_period:0, cc=cur?cur.credit_period:0, cb=cur?cur.balance_period:0;
    const pd=prev?prev.debit_period:0, pc=prev?prev.credit_period:0, pb=prev?prev.balance_period:0;
    let pct = null;
    if (pb !== 0) pct = ((cb-pb)/Math.abs(pb))*100;
    else if (cb !== 0) pct = 100.0;
    const s = cur || prev;
    rows.push({ account_code_full: s.account_code_full, account_code: s.account_code,
      account_name: s.account_name, rubro_code: s.rubro_code, rubro_name: s.rubro_name,
      current_debit: cd, current_credit: cc, current_balance: cb,
      previous_debit: pd, previous_credit: pc, previous_balance: pb,
      debit_delta: cd-pd, credit_delta: cc-pc, balance_delta: cb-pb,
      balance_delta_percent: pct, change_type: ct });
  }
  rows.sort((a,b) => Math.abs(b.balance_delta) - Math.abs(a.balance_delta));
  return {
    rows,
    stats: {
      totalAccounts: rows.length,
      newAccounts: rows.filter(r=>r.change_type==='NEW').length,
      missingAccounts: rows.filter(r=>r.change_type==='MISSING').length,
      changedAccounts: rows.filter(r=>r.change_type==='CHANGED').length,
      unchangedAccounts: rows.filter(r=>r.change_type==='UNCHANGED').length,
      totalCurrentBalance: rows.reduce((a,r)=>a+r.current_balance,0),
      totalPreviousBalance: rows.reduce((a,r)=>a+r.previous_balance,0),
      totalBalanceDelta: rows.reduce((a,r)=>a+r.balance_delta,0),
      topVariations: rows.slice(0,10).map(r=>({account_code:r.account_code,account_name:r.account_name,balance_delta:r.balance_delta,balance_delta_percent:r.balance_delta_percent,change_type:r.change_type}))
    }
  };
}

function generateAlerts(comparison, snapshotId, comparisonId) {
  const alerts = [];
  for (const row of (comparison.rows || [])) {
    const absBal = Math.abs(row.current_balance);
    const pct = Math.abs(row.balance_delta_percent || 0);
    if (absBal > 500_000_000) alerts.push({severity:'critical',code:'BALANCE_CRITICAL',snapshot_id:snapshotId,comparison_id:comparisonId,account_name:row.account_name,message:`Saldo crítico: ${absBal.toLocaleString('es-AR')} ARS`,current_balance:row.current_balance,absolute_delta:row.balance_delta,percent_delta:row.balance_delta_percent,status:'OPEN',created_at:new Date().toISOString()});
    if (pct > 30 && row.change_type==='CHANGED') alerts.push({severity:'warning',code:'VARIATION_HIGH',snapshot_id:snapshotId,comparison_id:comparisonId,account_name:row.account_name,message:`Variación ${pct.toFixed(1)}%`,current_balance:row.current_balance,previous_balance:row.previous_balance,absolute_delta:row.balance_delta,percent_delta:row.balance_delta_percent,status:'OPEN',created_at:new Date().toISOString()});
    if (row.change_type==='NEW') alerts.push({severity:'info',code:'NEW_ACCOUNT',snapshot_id:snapshotId,comparison_id:comparisonId,account_name:row.account_name,message:`Cuenta nueva: ${row.current_balance.toLocaleString('es-AR')} ARS`,current_balance:row.current_balance,status:'OPEN',created_at:new Date().toISOString()});
    if (row.change_type==='MISSING') alerts.push({severity:'warning',code:'MISSING_ACCOUNT',snapshot_id:snapshotId,comparison_id:comparisonId,account_name:row.account_name,message:`Cuenta ausente. Saldo anterior: ${row.previous_balance.toLocaleString('es-AR')} ARS`,previous_balance:row.previous_balance,status:'OPEN',created_at:new Date().toISOString()});
    if (row.previous_balance!==0 && row.current_balance!==0 && (row.previous_balance>0)!==(row.current_balance>0)) alerts.push({severity:'warning',code:'BALANCE_FLIP',snapshot_id:snapshotId,comparison_id:comparisonId,account_name:row.account_name,message:`Cambio de signo: ${row.previous_balance.toLocaleString('es-AR')} → ${row.current_balance.toLocaleString('es-AR')}`,current_balance:row.current_balance,previous_balance:row.previous_balance,status:'OPEN',created_at:new Date().toISOString()});
    if (row.current_debit===0 && row.current_credit===0 && row.change_type!=='MISSING') alerts.push({severity:'info',code:'ZERO_MOVEMENT',snapshot_id:snapshotId,comparison_id:comparisonId,account_name:row.account_name,message:'Sin movimiento en el período',status:'OPEN',created_at:new Date().toISOString()});
  }
  const sevOrder = {critical:0,warning:1,info:2};
  alerts.sort((a,b)=>(sevOrder[a.severity]||9)-(sevOrder[b.severity]||9));
  return alerts;
}

function saveComparison(comparison) {
  const filePath = path.join(COMPARISONS_DIR, `${comparison.id}.json`);
  fs.writeFileSync(filePath, JSON.stringify(comparison, null, 2), 'utf-8');
  console.log(`Comparación guardada: ${filePath}`);
  return comparison;
}

function saveAlerts(alerts, comparisonId) {
  const filePath = path.join(ALERTS_DIR, `${comparisonId}.json`);
  fs.writeFileSync(filePath, JSON.stringify(alerts, null, 2), 'utf-8');
  console.log(`Alertas guardadas: ${filePath} (${alerts.length} alertas)`);
  return alerts;
}

function getSnapshotListAPI() {
  return { success: true, total: listSnapshots().length, snapshots: listSnapshots(), timestamp: new Date().toISOString() };
}

function getComparisonAPI(comparisonId) {
  const fp = path.join(COMPARISONS_DIR, `${comparisonId}.json`);
  if (!fs.existsSync(fp)) return { success: false, error: `Comparación no encontrada: ${comparisonId}` };
  return { success: true, comparison: JSON.parse(fs.readFileSync(fp,'utf-8')), timestamp: new Date().toISOString() };
}

function getAlertsAPI(comparisonId, severity) {
  const fp = path.join(ALERTS_DIR, `${comparisonId}.json`);
  if (!fs.existsSync(fp)) return { success: false, error: `Alertas no encontradas: ${comparisonId}` };
  let alerts = JSON.parse(fs.readFileSync(fp,'utf-8'));
  if (severity) alerts = alerts.filter(a => a.severity === severity);
  return { success: true, total: alerts.length,
    bySeverity: { critical: alerts.filter(a=>a.severity==='critical').length, warning: alerts.filter(a=>a.severity==='warning').length, info: alerts.filter(a=>a.severity==='info').length },
    alerts, timestamp: new Date().toISOString() };
}

function main() {
  const args = process.argv.slice(2);
  const cmd = args[0];
  if (!cmd) { console.log('Snapshot Engine — Sumas y Saldos\nComandos: list | compare-latest | api-snapshot-list | api-comparison <id> | api-alerts <id> [severity]'); return; }
  switch(cmd) {
    case 'list': { const s=listSnapshots(); s.forEach(x=>console.log(`  ${x.id.slice(0,8)}... | ${x.period_from}→${x.period_to} | ${x.row_count} cuentas`)); break; }
    case 'compare-latest': { const s=listSnapshots(); if(s.length<2){console.error('Need 2+ snapshots');process.exit(1);} const cur=loadSnapshot(s[0].id),prev=loadSnapshot(s[1].id); const {rows,stats}=compareSnapshots(cur,prev); const c={id:uuid(),current_snapshot_id:cur.id,previous_snapshot_id:prev.id,current_period:`${cur.period_from}→${cur.period_to}`,previous_period:`${prev.period_from}→${prev.period_to}`,status:'COMPLETED',row_count:rows.length,stats,rows,created_at:new Date().toISOString()}; saveComparison(c); const a=generateAlerts(c,cur.id,c.id); saveAlerts(a,c.id); console.log(`Comparación: ${stats.totalAccounts} cuentas | Alertas: ${a.length}`); break; }
    case 'api-snapshot-list': console.log(JSON.stringify(getSnapshotListAPI(),null,2)); break;
    case 'api-comparison': console.log(JSON.stringify(getComparisonAPI(args[1]),null,2)); break;
    case 'api-alerts': console.log(JSON.stringify(getAlertsAPI(args[1],args[2]),null,2)); break;
    default: console.error(`Unknown: ${cmd}`); process.exit(1);
  }
}

module.exports = { buildSnapshotQuery, transformMSSQLRows, saveSnapshot, loadSnapshot, listSnapshots, compareSnapshots, generateAlerts, saveComparison, saveAlerts, getSnapshotListAPI, getComparisonAPI, getAlertsAPI, ALERT_RULES };

if (require.main === module) main();
