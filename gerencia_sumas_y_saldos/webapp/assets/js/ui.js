/* UI — Render functions para todas las secciones */

/* ════════════════════════════════════════════════
   RENDER ALL
════════════════════════════════════════════════ */
function renderAll() {
  if (!STATE.data) return;
  STATE.filteredData = getFilteredData();
  updateParams();
  renderKPIs();
  renderTopAccounts();
  renderSumasSaldos();
  renderMayor();
  renderLibroDiario();
  renderImputaciones();
  renderEgresos();
  renderIngresos();
  renderProveedoresSaldos();
  renderProveedoresPendientes();
  renderInterempresas();
  renderAlertas();
  renderCharts();
}

function updateParams() {
  CONFIG.criticalThreshold = parseFloat(document.getElementById('criticalThreshold').value) || 10_000_000;
  CONFIG.variationThreshold = parseFloat(document.getElementById('variationThreshold').value) || 30;
  const sd = document.getElementById('startDate').value || '20260601';
  const ed = document.getElementById('endDate').value || '20260630';
  document.getElementById('badge-period').textContent = `${sd.slice(6,8)}/${sd.slice(4,6)}/${sd.slice(0,4)} — ${ed.slice(6,8)}/${ed.slice(4,6)}/${ed.slice(0,4)}`;
}

/* ─── KPIs ─── */
function renderKPIs() {
  try {
    const ss = (STATE.filteredData ? STATE.filteredData.sumasSaldos : STATE.data.sumasSaldos) || [];
    const saldoTotal = ss.reduce((acc, r) => acc + (r.debeAcum - r.haberAcum), 0);
    const critCount = ss.filter(r => r.estado === 'crit').length;
    const alertCount = ss.filter(r => r.estado !== 'ok').length;
    const avgVar = ss.length ? ss.reduce((a,r) => a + Math.abs(r.variacion), 0) / ss.length : 0;

    const ev = STATE.data.egresos || [];
    const iv = STATE.data.ingresos || [];
    const totalEg = ev.reduce((a,r) => a + (r.importe || 0), 0);
    const totalIv = iv.reduce((a,r) => a + (r.importe || 0), 0);

    if ($('kpi-saldo-total')) $('kpi-saldo-total').textContent = fmtMoney(saldoTotal);
    if ($('kpi-saldo-delta')) $('kpi-saldo-delta').textContent = 'Saldo neto acumulado';
    if ($('kpi-crit-count')) $('kpi-crit-count').textContent = critCount;
    if ($('kpi-crit-desc')) $('kpi-crit-desc').textContent = `Umbral: ${fmtMoney(CONFIG.criticalThreshold)}`;
    if ($('kpi-alert-count')) $('kpi-alert-count').textContent = alertCount;
    if ($('kpi-alert-desc')) $('kpi-alert-desc').textContent = `${critCount} críticas, ${alertCount - critCount} advertencias`;
    if ($('kpi-avg-var')) $('kpi-avg-var').textContent = fmtPct(avgVar);
    if ($('kpi-var-desc')) $('kpi-var-desc').textContent = 'Variación media absoluta';
    if ($('kpi-egresos')) $('kpi-egresos').textContent = fmtMoney(totalEg);
    if ($('kpi-ingresos')) $('kpi-ingresos').textContent = fmtMoney(totalIv);
    if ($('kpi-ev-total')) $('kpi-ev-total').textContent = fmtMoney(totalEg);
    if ($('kpi-ev-count')) $('kpi-ev-count').textContent = ev.length;
    if ($('kpi-iv-total')) $('kpi-iv-total').textContent = fmtMoney(totalIv);
    if ($('kpi-iv-count')) $('kpi-iv-count').textContent = iv.length;
  } catch (e) { console.warn('renderKPIs:', e.message); }
}

/* ─── TOP ACCOUNTS TABLE ─── */
function renderTopAccounts() {
  const ss = ((STATE.filteredData || STATE.data).sumasSaldos || []).slice().sort((a,b) => Math.abs(b.debeAcum - b.haberAcum) - Math.abs(a.debeAcum - a.haberAcum)).slice(0, 10);
  const tbody = document.getElementById('top-accounts-body');
  tbody.innerHTML = ss.map(r => `
    <tr>
      <td class="code"><a href="#" onclick="verMovimientos('${r.codigo}','${r.cuenta}');return false;" style="color:var(--accent-blue);text-decoration:none;">${r.codigo}</a></td>
      <td>${r.cuenta}</td>
      <td class="num">${fmtMoney(r.debeAcum)}</td>
      <td class="num">${fmtMoney(r.haberAcum)}</td>
      <td class="num"><strong>${fmtMoney(r.debeAcum - r.haberAcum)}</strong></td>
      <td>${varPill(r.variacion)}</td>
      <td>${estadoBadge(r.estado)}</td>
    </tr>`).join('');
}

/* ─── SUMAS Y SALDOS ─── */
function renderSumasSaldos() {
  const ss = (STATE.filteredData || STATE.data).sumasSaldos || [];
  const tbody = document.getElementById('ss-body');
  tbody.innerHTML = ss.map(r => `
    <tr>
      <td class="code"><a href="#" onclick="verMovimientos('${r.codigo}','${r.cuenta}');return false;" style="color:var(--accent-blue);text-decoration:none;">${r.codigo}</a></td>
      <td>${r.cuenta}</td>
      <td class="num" style="color:${(r.saldoInicial || 0) >= 0 ? 'var(--text-primary)' : 'var(--accent-red)'}">${fmtMoney(r.saldoInicial)}</td>
      <td class="num">${fmtMoney(r.debeAcum)}</td>
      <td class="num">${fmtMoney(r.haberAcum)}</td>
      <td class="num">${fmtMoney(Math.max(0, r.debeAcum - r.haberAcum))}</td>
      <td class="num">${fmtMoney(Math.max(0, r.haberAcum - r.debeAcum))}</td>
      <td>${varPill(r.variacion)}</td>
      <td>${estadoBadge(r.estado)}</td>
    </tr>`).join('');

  const totDebe = ss.reduce((a,r) => a + r.debeAcum, 0);
  const totHaber = ss.reduce((a,r) => a + r.haberAcum, 0);
  document.getElementById('total-debe').textContent = fmtMoney(totDebe);
  document.getElementById('total-haber').textContent = fmtMoney(totHaber);
  const diff = totDebe - totHaber;
  const diffEl = document.getElementById('total-diff');
  diffEl.textContent = fmtMoney(diff);
  diffEl.style.color = Math.abs(diff) < 1 ? 'var(--corona-green-dark)' : 'var(--accent-red)';
}

/* ─── MAYOR ─── */
function renderMayor(cuenta) {
  const items = STATE.data.mayor || [];
  const filtered = cuenta ? items.filter(r => r.cuenta === cuenta) : items;
  const tbody = document.getElementById('mayor-body');

  // Obtener saldo inicial desde STATE.data.sumasSaldos
  var saldoInicial = 0;
  if (cuenta && STATE.data.sumasSaldos) {
    var match = STATE.data.sumasSaldos.find(function(r){ return r.codigo === cuenta; });
    if (match) saldoInicial = match.saldoInicial || 0;
  }

  // Fecha de inicio formateada
  var sd = document.getElementById('startDate').value || '20260601';
  var startDateFormatted = sd.replace(/-/g, '');
  var fechaFormateada = fmtDate(startDateFormatted);

  var html = '';

  // Primera fila: Saldo Inicial (destacada)
  html += '<tr style="background:var(--corona-green-light);">' +
    '<td class="code" style="color:var(--text-muted);">—</td>' +
    '<td class="code" style="color:var(--text-muted);">—</td>' +
    '<td><em style="color:var(--corona-green-dark);">Saldo Inicial al ' + fechaFormateada + '</em></td>' +
    '<td class="num">—</td>' +
    '<td class="num">—</td>' +
    '<td class="num"><strong style="color:' + (saldoInicial >= 0 ? 'var(--corona-green-dark)' : 'var(--accent-red)') + '">' + fmtMoney(saldoInicial) + '</strong></td>' +
    '</tr>';

  // Movimientos con saldo acumulado desde el inicial
  var saldoAcc = saldoInicial;
  html += filtered.map(function(r){
    saldoAcc += (r.debe || 0) - (r.haber || 0);
    return '<tr>' +
      '<td class="code">' + fmtDate(r.fecha) + '</td>' +
      '<td class="code">' + (r.asiento || '') + '</td>' +
      '<td>' + (r.descripcion || '') + '</td>' +
      '<td class="num">' + (r.debe ? fmtMoney(r.debe) : '—') + '</td>' +
      '<td class="num">' + (r.haber ? fmtMoney(r.haber) : '—') + '</td>' +
      '<td class="num"><strong style="color:' + (saldoAcc >= 0 ? 'var(--corona-green-dark)' : 'var(--accent-red)') + '">' + fmtMoney(saldoAcc) + '</strong></td>' +
      '</tr>';
  }).join('');

  tbody.innerHTML = html || '<tr><td colspan="6"><div class="empty-state"><h3>Sin movimientos</h3></div></td></tr>';

  // Populate select
  const sel = document.getElementById('select-cuenta');
  const cuentas = [...new Set(items.map(r => r.cuenta))];
  sel.innerHTML = '<option value="">Todas las cuentas</option>' + cuentas.map(c => `<option value="${c}">${c}</option>`).join('');
}

function filterMayor(v) { renderMayor(v); }

/* ─── LIBRO DIARIO ─── */
function renderLibroDiario() {
  const items = STATE.data.libroDiario || [];
  document.getElementById('ld-body').innerHTML = items.map(r => `
    <tr>
      <td class="code">${fmtDate(r.fecha)}</td>
      <td class="code">${r.asiento}</td>
      <td>${r.descripcion}</td>
      <td style="font-size:12px;">${r.ctaDebe}</td>
      <td style="font-size:12px;">${r.ctaHaber}</td>
      <td class="num"><strong>${fmtMoney(r.importe)}</strong></td>
      <td class="code">${r.ref}</td>
    </tr>`).join('');
}

/* ─── IMPUTACIONES ─── */
function renderImputaciones(tipo) {
  const items = (STATE.data.imputaciones || []).filter(r => !tipo || r.tipo === tipo);
  document.getElementById('imput-body').innerHTML = items.map(r => `
    <tr>
      <td class="code">${fmtDate(r.fecha)}</td>
      <td><span class="badge-status ${r.tipo==='VENTA'?'badge-ok':r.tipo==='AJUSTE'?'badge-warn':'badge-info'}">${r.tipo}</span></td>
      <td class="code">${r.comp}</td>
      <td>${r.ente}</td>
      <td><span style="font-size:11px; background:var(--surface-2); border:1px solid var(--border); border-radius:4px; padding:2px 6px;">${r.cc}</span></td>
      <td class="num">${fmtMoney(r.neto)}</td>
      <td class="num">${r.iva ? fmtMoney(r.iva) : '—'}</td>
      <td class="num"><strong>${fmtMoney(r.total)}</strong></td>
      <td>${estadoBadge(r.estado)}</td>
    </tr>`).join('');
}

function filterImputaciones() {
  renderImputaciones(document.getElementById('filter-imputacion-tipo').value);
}

/* ─── EGRESOS ─── */
function renderEgresos() {
  const items = STATE.data.egresos || [];
  document.getElementById('ev-body').innerHTML = items.map(r => `
    <tr>
      <td class="code">${fmtDate(r.fecha)}</td>
      <td class="code">${r.nro}</td>
      <td>${r.beneficiario}</td>
      <td>${r.concepto}</td>
      <td><span style="font-size:11px; color:var(--text-secondary);">${r.medio}</span></td>
      <td class="num"><strong>${fmtMoney(r.importe)}</strong></td>
      <td>${estadoBadge(r.estado)}</td>
    </tr>`).join('');
}

/* ─── INGRESOS ─── */
function renderIngresos() {
  const items = STATE.data.ingresos || [];
  document.getElementById('iv-body').innerHTML = items.map(r => `
    <tr>
      <td class="code">${fmtDate(r.fecha)}</td>
      <td class="code">${r.nro}</td>
      <td>${r.origen}</td>
      <td>${r.concepto}</td>
      <td><span style="font-size:11px; color:var(--text-secondary);">${r.medio}</span></td>
      <td class="num"><strong>${fmtMoney(r.importe)}</strong></td>
      <td>${estadoBadge(r.estado)}</td>
    </tr>`).join('');
}

/* ─── PROVEEDORES SALDOS ─── */
function renderProveedoresSaldos() {
  const items = STATE.data.proveedoresSaldos || [];
  document.getElementById('prov-saldos-body').innerHTML = items.map(r => `
    <tr>
      <td class="code">${r.cuit}</td>
      <td><strong>${r.proveedor}</strong></td>
      <td><span style="font-size:11px; color:var(--text-secondary);">${r.categoria}</span></td>
      <td class="num"><strong>${fmtMoney(r.saldo)}</strong></td>
      <td class="num" style="color:${r.vencido>0?'var(--accent-red)':'var(--text-muted)'};">${r.vencido > 0 ? fmtMoney(r.vencido) : '—'}</td>
      <td class="num">${fmtMoney(r.aVencer)}</td>
      <td class="code">${fmtDate(r.ultimoMov)}</td>
      <td>${estadoBadge(r.estado)}</td>
    </tr>`).join('');
}

/* ─── PROVEEDORES PENDIENTES ─── */
function renderProveedoresPendientes(estado) {
  const items = (STATE.data.proveedoresPendientes || []).filter(r => !estado || r.estado === estado);
  document.getElementById('prov-pend-body').innerHTML = items.map(r => `
    <tr>
      <td><strong>${r.proveedor}</strong></td>
      <td class="code">${r.comp}</td>
      <td class="code">${fmtDate(r.fecha)}</td>
      <td class="code" style="color:${r.estado==='VENCIDO'?'var(--accent-red)':r.estado==='A_VENCER'?'var(--accent-amber)':'var(--text-secondary)'};">${fmtDate(r.venc)}</td>
      <td>${diasBadge(r.dias)}</td>
      <td class="num"><strong>${fmtMoney(r.importe)}</strong></td>
      <td><span class="badge-status ${r.estado==='VENCIDO'?'badge-crit':r.estado==='A_VENCER'?'badge-warn':'badge-ok'}">${r.estado==='A_VENCER'?'A vencer':r.estado==='VIGENTE'?'Vigente':'Vencido'}</span></td>
    </tr>`).join('');
}

function filterPendientes() {
  renderProveedoresPendientes(document.getElementById('filter-pend-estado').value);
}

/* ─── INTEREMPRESAS ─── */
function renderInterempresas() {
  const items = STATE.data.interempresas || [];
  const container = document.getElementById('inter-body');
  container.innerHTML = '';
  items.forEach(r => {
    const row = document.createElement('div');
    row.className = 'flow-row';
    row.innerHTML = `
      <span class="flow-entity">${r.desde}</span>
      <div class="flow-arrow">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="${r.tipo==='entrada'?'var(--corona-green)':'var(--accent-red)'}" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </div>
      <span class="flow-entity">${r.hacia}</span>
      <span style="flex:1; font-size:12px; color:var(--text-secondary); margin-left:12px;">${r.concepto}</span>
      <span class="flow-amount" style="color:${r.tipo==='entrada'?'var(--corona-green-dark)':'var(--accent-red)'};">${r.tipo==='entrada'?'+':'−'}${fmtMoney(r.importe)}</span>
      <span class="code" style="margin-left:12px; color:var(--text-muted);">${fmtDate(r.fecha)}</span>
    `;
    container.appendChild(row);
  });
}

/* ─── ALERTAS ─── */
function renderAlertas(severity) {
  const d = STATE.data;
  const alerts = [];

  // Cuentas críticas
  (d.sumasSaldos || []).filter(r => r.estado === 'crit').forEach(r => {
    alerts.push({ type: 'critical', title: `Cuenta crítica: ${r.cuenta}`, desc: `Saldo: ${fmtMoney(r.debeAcum - r.haberAcum)} | Variación: ${fmtPct(r.variacion)}` });
  });

  // Proveedores vencidos
  (d.proveedoresPendientes || []).filter(r => r.estado === 'VENCIDO').forEach(r => {
    alerts.push({ type: 'critical', title: `Pago vencido: ${r.proveedor}`, desc: `${r.comp} — ${fmtMoney(r.importe)} — Vencido hace ${r.dias} día(s)` });
  });

  // A vencer
  (d.proveedoresPendientes || []).filter(r => r.estado === 'A_VENCER').forEach(r => {
    alerts.push({ type: 'warning', title: `Próximo vencimiento: ${r.proveedor}`, desc: `${r.comp} — ${fmtMoney(r.importe)} — Vence en ${Math.abs(r.dias)} día(s)` });
  });

  // Variaciones altas warn
  (d.sumasSaldos || []).filter(r => r.estado === 'warn').forEach(r => {
    alerts.push({ type: 'warning', title: `Variación elevada: ${r.cuenta}`, desc: `Variación ${fmtPct(r.variacion)} supera el umbral configurado` });
  });

  // Info: sync
  alerts.push({ type: 'info', title: 'Próxima sincronización', desc: 'El flujo Node-RED ejecutará la sincronización con Google Sheets en la próxima ventana de 8 h.' });

  const icons = { critical: '⚠', warning: '○', info: 'ℹ' };
  const filtered = severity ? alerts.filter(a => a.type === severity) : alerts;

  document.getElementById('alertas-body').innerHTML = filtered.map(a => `
    <div class="alert-strip ${a.type}">
      <span class="alert-icon">${icons[a.type]}</span>
      <div class="alert-body">
        <div class="alert-title">${a.title}</div>
        <div class="alert-desc">${a.desc}</div>
      </div>
    </div>`).join('');

  document.getElementById('kpi-alert-count').textContent = alerts.filter(a => a.type !== 'info').length;
}

function filterAlertas() {
  renderAlertas(document.getElementById('filter-alert-severity').value);
}

/* ─── CHARTS ─── */
function renderCharts() {
  const ev = STATE.data.evolucion || {};

  // Evolution chart
  if (STATE.evolutionChartInstance) STATE.evolutionChartInstance.destroy();
  const evCtx = document.getElementById('evolutionChart').getContext('2d');
  STATE.evolutionChartInstance = new Chart(evCtx, {
    type: STATE.evolutionChartType,
    data: {
      labels: ev.labels || [],
      datasets: [
        { label: 'Caja y Bancos', data: ev.caja || [], borderColor: '#1D9E75', backgroundColor: 'rgba(29,158,117,0.1)', borderWidth: 2, pointRadius: 3, tension: 0.4, fill: STATE.evolutionChartType === 'line' },
        { label: 'Ventas', data: ev.ventas || [], borderColor: '#185FA5', backgroundColor: 'rgba(24,95,165,0.1)', borderWidth: 2, pointRadius: 3, tension: 0.4, borderDash: STATE.evolutionChartType === 'line' ? [4,2] : [], fill: false },
        { label: 'Costos', data: ev.costos || [], borderColor: '#A32D2D', backgroundColor: 'rgba(163,45,45,0.1)', borderWidth: 2, pointRadius: 3, tension: 0.4, borderDash: STATE.evolutionChartType === 'line' ? [2,2] : [], fill: false },
      ]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { ticks: { callback: v => '$' + (v/1_000_000).toFixed(0) + 'M', font: { size: 11 }, color: '#9BA3AF' }, grid: { color: 'rgba(0,0,0,0.04)' } },
        x: { ticks: { font: { size: 11 }, color: '#9BA3AF' }, grid: { display: false } }
      }
    }
  });

  // Distribution donut
  if (STATE.distributionChartInstance) STATE.distributionChartInstance.destroy();
  const ss = (STATE.filteredData || STATE.data).sumasSaldos || [];
  const grupos = {};
  ss.forEach(r => {
    const g = r.codigo ? r.codigo[0] : '?';
    const names = {'1':'Activo','2':'Pasivo','3':'Patrimonio','4':'Ingresos','5':'Egresos'};
    const name = names[g] || 'Otros';
    grupos[name] = (grupos[name] || 0) + Math.abs(r.debeAcum - r.haberAcum);
  });

  const dCtx = document.getElementById('distributionChart').getContext('2d');
  STATE.distributionChartInstance = new Chart(dCtx, {
    type: 'doughnut',
    data: {
      labels: Object.keys(grupos),
      datasets: [{ data: Object.values(grupos), backgroundColor: ['#1D9E75','#A32D2D','#185FA5','#BA7517','#5F5E5A'], borderWidth: 2, borderColor: '#ffffff' }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      cutout: '65%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: { font: { size: 11 }, padding: 12, boxWidth: 10, color: '#5A6170' }
        }
      }
    }
  });
}

function toggleChartType() {
  STATE.evolutionChartType = STATE.evolutionChartType === 'line' ? 'bar' : 'line';
  if (STATE.data) renderCharts();
}
