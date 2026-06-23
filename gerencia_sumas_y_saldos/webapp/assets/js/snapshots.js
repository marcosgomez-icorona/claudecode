/* SNAPSHOTS — Vista de variaciones entre períodos, comparación y alertas */

var STATE_VAR = { comparison: null, alerts: null, loaded: false };

async function loadVariaciones() {
  var kpiGrid = document.getElementById('var-kpi-grid');
  if (kpiGrid) kpiGrid.style.opacity = '0.6';
  try {
    var compResp = await fetch('data/latest-comparison.json');
    if (!compResp.ok) throw new Error('No se pudo cargar comparación');
    STATE_VAR.comparison = (await compResp.json()).comparison;
    var alertResp = await fetch('data/latest-alerts.json');
    if (alertResp.ok) STATE_VAR.alerts = (await alertResp.json()).alerts;
    STATE_VAR.loaded = true;
    renderVariaciones();
    if (kpiGrid) kpiGrid.style.opacity = '1';
  } catch(e) {
    console.warn('loadVariaciones:', e.message);
    if (kpiGrid) kpiGrid.style.opacity = '1';
    renderEmptyVariaciones();
  }
}

function renderEmptyVariaciones() {
  var tbody = document.getElementById('var-body');
  if (tbody) tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3>Sin datos de comparación</h3><p>Ejecutá el snapshot engine para generar comparaciones entre períodos.</p></div></td></tr>';
  ['var-periodo-actual','var-periodo-anterior','var-cambiadas','var-alertas-total','var-nuevas','var-ausentes'].forEach(function(id){
    var el = document.getElementById(id); if (el) el.textContent = '—';
  });
}

function renderVariaciones() {
  var comp = STATE_VAR.comparison;
  if (!comp) { renderEmptyVariaciones(); return; }
  var stats = comp.stats || {};
  var el;
  if (el = document.getElementById('var-periodo-actual')) el.textContent = comp.current_period || '';
  if (el = document.getElementById('var-periodo-actual-sub')) el.textContent = (stats.totalCurrentBalance || 0).toLocaleString('es-AR') + ' ARS';
  if (el = document.getElementById('var-periodo-anterior')) el.textContent = comp.previous_period || '';
  if (el = document.getElementById('var-periodo-anterior-sub')) el.textContent = (stats.totalPreviousBalance || 0).toLocaleString('es-AR') + ' ARS';
  if (el = document.getElementById('var-cambiadas')) el.textContent = (stats.changedAccounts || 0);
  if (el = document.getElementById('var-cambiadas-sub')) el.textContent = 'de ' + (stats.totalAccounts || 0) + ' cuentas';
  if (el = document.getElementById('var-nuevas')) el.textContent = (stats.newAccounts || 0);
  if (el = document.getElementById('var-ausentes')) el.textContent = (stats.missingAccounts || 0);
  if (STATE_VAR.alerts) {
    var crit=0,warn=0,info=0;
    STATE_VAR.alerts.forEach(function(a){ if(a.severity==='critical')crit++; else if(a.severity==='warning')warn++; else info++; });
    if (el = document.getElementById('var-alertas-total')) el.textContent = STATE_VAR.alerts.length;
    if (el = document.getElementById('var-alertas-sub')) el.textContent = crit + ' críticas / ' + warn + ' warnings / ' + info + ' info';
  }
  renderVariacionesTable();
  renderAlertasVariaciones();
}

function renderVariacionesTable() {
  var comp = STATE_VAR.comparison;
  if (!comp) return;
  var rows = comp.rows || [];
  var filterChange = (document.getElementById('filter-var-change')?.value || '').trim();
  var filterSearch = (document.getElementById('filter-var-search')?.value || '').toLowerCase().trim();
  var filtered = rows;
  if (filterChange) filtered = filtered.filter(function(r){ return r.change_type === filterChange; });
  if (filterSearch) filtered = filtered.filter(function(r){ return (r.account_code||'').toLowerCase().indexOf(filterSearch)!==-1 || (r.account_name||'').toLowerCase().indexOf(filterSearch)!==-1; });
  var tbody = document.getElementById('var-body');
  if (!tbody) return;
  if (!filtered.length) { tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3>Sin resultados</h3></div></td></tr>'; return; }
  tbody.innerHTML = filtered.map(function(r){
    var changeLabel = r.change_type==='NEW'?'Nueva':r.change_type==='MISSING'?'Ausente':r.change_type==='CHANGED'?'Cambió':'Sin cambio';
    var changeCls = r.change_type==='NEW'?'badge-ok':r.change_type==='MISSING'?'badge-crit':r.change_type==='CHANGED'?'badge-warn':'badge-info';
    var pctStr = r.balance_delta_percent!=null ? fmtPct(r.balance_delta_percent) : 'N/A';
    return '<tr><td class="code">'+(r.account_code||'')+'</td><td>'+(r.account_name||'')+'</td><td class="num">'+fmtMoney(r.previous_balance)+'</td><td class="num"><strong>'+fmtMoney(r.current_balance)+'</strong></td><td class="num" style="color:'+(r.balance_delta>0?'var(--corona-green-dark)':r.balance_delta<0?'var(--accent-red)':'var(--text-muted)')+';">'+(r.balance_delta>0?'+':'')+fmtMoney(r.balance_delta)+'</td><td>'+varPill(r.balance_delta_percent)+'</td><td><span class="badge-status '+changeCls+'">'+changeLabel+'</span></td></tr>';
  }).join('');
}

function filtrarVariaciones() { renderVariacionesTable(); }

function renderAlertasVariaciones() {
  var container = document.getElementById('var-alertas-body');
  if (!container) return;
  var alerts = STATE_VAR.alerts || [];
  var severity = (document.getElementById('filter-alert-var-severity')?.value || '').trim();
  if (severity) alerts = alerts.filter(function(a){ return a.severity === severity; });
  if (!alerts.length) { container.innerHTML = '<div class="empty-state"><h3>Sin alertas</h3></div>'; return; }
  var icons = {critical:'⛔',warning:'⚠',info:'ℹ'};
  container.innerHTML = alerts.map(function(a){
    return '<div class="alert-strip '+a.severity+'"><span class="alert-icon">'+(icons[a.severity]||'•')+'</span><div class="alert-body"><div class="alert-title">['+a.code+'] '+(a.account||a.account_name||'')+'</div><div class="alert-desc">'+(a.message||'')+'</div></div></div>';
  }).join('');
}

function filtrarAlertasVariaciones() { renderAlertasVariaciones(); }

// Interceptar showSection para carga lazy
var _origShowSection = showSection;
showSection = function(id) {
  _origShowSection(id);
  if (id === 'variaciones' && !STATE_VAR.loaded) loadVariaciones();
};
