// assets/js/app.js
// Main orchestrator for the Sumas y Saldos dashboard
import { getDashboardData } from './dataService.js';
import { renderAccountsTable } from './renderTables.js';
import { renderBalanceChart } from './charts.js';
import { evaluateAlerts, fmtUSD } from './alertsEngine.js';
import { CONFIG } from './config.js';

/** Helper to show a section and hide others */
function showSection(id) {
  document.querySelectorAll('section').forEach(s => s.style.display = 'none');
  const target = document.getElementById(id);
  if (target) target.style.display = '';
}

/** Update KPI cards */
function updateMetrics(data) {
  const total = data.sumasSaldos?.reduce((a, r) => a + (r.saldo || 0), 0) || 0;
  document.getElementById('totalBalance').textContent = fmtUSD(total);
  const crit = data.sumasSaldos?.filter(r => CONFIG.criticalAccounts.includes(r.cuenta)).length || 0;
  document.getElementById('criticalCount').textContent = crit;
  const variations = data.sumasSaldos?.filter(r => Math.abs(r.variacion) > (CONFIG.umbVariacion || 30)).length || 0;
  document.getElementById('avgVariation').textContent = variations;
}

/** Render alerts in the alert box */
function renderAlerts(alerts) {
  const box = document.getElementById('alertBox');
  if (!alerts.length) {
    box.className = 'alert alert-success';
    box.textContent = 'No hay alertas.';
    return;
  }
  box.className = 'alert alert-warning';
  box.innerHTML = alerts.map(a => `<div>${a.severity.toUpperCase()}: ${a.msg}</div>`).join('');
}

/** Initialise the dashboard */
async function init() {
  const raw = await getDashboardData();
  // Normalise mock shape to what the engine expects
  const data = {
    sumasSaldos: raw.accounts,
    movimientos: raw.intercompany, // reuse for inter‑empresa detection
    proveedores: raw.providers,
    config: CONFIG
  };

  // Metrics
  document.getElementById('metricsSection').style.display = '';
  updateMetrics(data);

  // Table
  document.getElementById('tableSection').style.display = '';
  renderAccountsTable(data.sumasSaldos);

  // Chart
  document.getElementById('chartSection').style.display = '';
  renderBalanceChart(raw.balanceEvolution.labels, raw.balanceEvolution.data);

  // Alerts
  const alerts = evaluateAlerts(data);
  document.getElementById('alertSection').style.display = '';
  renderAlerts(alerts);

  // Navigation handling
  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      const targetId = link.getAttribute('href').substring(1);
      showSection(targetId);
      document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
      link.classList.add('active');
    });
  });

  // Show default view (resumen)
  showSection('resumen');
}

// Run on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
