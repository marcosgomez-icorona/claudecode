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
  populateMayorSelect();    // solo poblá el dropdown, no cargues datos todavía
  renderLibroDiario();
  cargarImputaciones();
  renderEgresos();
  renderIngresos();
  renderProveedoresSaldos();
  renderProveedoresPendientes();
  cargarInterempresas();
  renderAlertas();
  renderCharts();
}

function updateParams() {
  CONFIG.criticalThreshold = parseFloat(document.getElementById('criticalThreshold').value) || 10_000_000;
  CONFIG.variationThreshold = parseFloat(document.getElementById('variationThreshold').value) || 30;
  const sd = document.getElementById('startDate').value || '20250101';
  const ed = document.getElementById('endDate').value || '20260630';
  const badge = document.getElementById('badge-period');
  badge.textContent = `${sd.slice(8,10)}/${sd.slice(5,7)}/${sd.slice(0,4)} — ${ed.slice(8,10)}/${ed.slice(5,7)}/${ed.slice(0,4)}`;
  badge.title = 'Datos acumulados desde 2025-01-01. Usá los filtros de fecha para acotar el periodo.';
  // Mostrar filtro de empresa activo
  const empSel = document.getElementById('filter-empresa');
  const empVal = empSel ? empSel.value : '';
  if (empVal) {
    badge.textContent += ' | ' + empVal;
    badge.style.background = 'var(--corona-green-light)';
    badge.style.color = 'var(--corona-green-dark)';
    badge.style.fontWeight = '600';
  } else {
    badge.style.background = '';
    badge.style.color = '';
    badge.style.fontWeight = '';
  }
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
function populateMayorSelect() {
  // Popula el dropdown de cuentas desde STATE.data.sumasSaldos (datos reales)
  const ss = (STATE.filteredData || STATE.data).sumasSaldos || [];
  const sel = document.getElementById('select-cuenta');
  if (!sel) return;
  const cuentas = [...ss].sort(function(a,b){ return (a.codigo||'').localeCompare(b.codigo||''); });
  sel.innerHTML = '<option value="">Seleccionar una cuenta...</option>' +
    cuentas.map(function(r){
      var unidad = r.unidad ? ' [' + r.unidad + ']' : '';
      return '<option value="' + r.codigo + '">' + r.codigo + ' — ' + r.cuenta + unidad + '</option>';
    }).join('');

  // Inicializar fechas del Mayor (últimos 10 días por defecto)
  var now = new Date();
  var tenDaysAgo = new Date(now.getTime() - 10 * 86400000);
  var edY = now.getFullYear(), edM = String(now.getMonth() + 1).padStart(2, '0'), edD = String(now.getDate()).padStart(2, '0');
  var sdY = tenDaysAgo.getFullYear(), sdM = String(tenDaysAgo.getMonth() + 1).padStart(2, '0'), sdD = String(tenDaysAgo.getDate()).padStart(2, '0');
  var elSd = document.getElementById('mayor-startDate');
  var elEd = document.getElementById('mayor-endDate');
  if (elSd && !elSd.value) elSd.value = sdY + '-' + sdM + '-' + sdD;
  if (elEd && !elEd.value) elEd.value = edY + '-' + edM + '-' + edD;
}

// Cache de movimientos del Mayor para filtrado local
var STATE_MAYOR = { movimientos: [], codigo: '', saldoAcc: 0 };

async function cargarMayor(codigo) {
  const tbody = document.getElementById('mayor-body');
  var filtrosDiv = document.getElementById('mayor-filtros');

  if (!codigo) {
    if (filtrosDiv) filtrosDiv.style.display = 'none';
    tbody.innerHTML = '<tr><td colspan="6"><div class="empty-state"><h3>Seleccioná una cuenta</h3><p>Elegí una cuenta para ver sus movimientos</p></div></td></tr>';
    STATE_MAYOR = { movimientos: [], codigo: '', saldoAcc: 0 };
    return;
  }

  tbody.innerHTML = '<tr><td colspan="6"><div class="empty-state"><h3>Consultando... 🔄</h3><p>Cargando movimientos desde Google Sheets</p></div></td></tr>';
  if (filtrosDiv) filtrosDiv.style.display = 'none';

  // Leer fechas del filtro de Mayor
  var sdEl = document.getElementById('mayor-startDate');
  var edEl = document.getElementById('mayor-endDate');
  var sd = sdEl ? sdEl.value.replace(/-/g, '') : '';
  var ed = edEl ? edEl.value.replace(/-/g, '') : '';

  try {
    var params = { codigo: codigo.trim() };
    if (sd) params.startDate = sd;
    if (ed) params.endDate = ed;
    var result = await fetchWithFallback('/api/sumas-saldos/mayor', params);
    var data = result.data;

    if (!data.success || !data.movimientos || data.movimientos.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6"><div class="empty-state"><h3>Sin movimientos</h3><p>No hay registros para la cuenta ' + codigo + (sd ? ' en ' + sd + ' — ' + (ed || 'hoy') : '') + '</p></div></td></tr>';
      STATE_MAYOR = { movimientos: [], codigo: '', saldoAcc: 0 };
      return;
    }

    // Aplicar filtro de empresa si está activo
    var movs = data.movimientos;
    var empresa = (document.getElementById('filter-empresa') && document.getElementById('filter-empresa').value || '').trim();
    if (empresa) {
      movs = movs.filter(function(r){ return (r.unidad || '') === empresa; });
    }

    // Guardar en cache para filtrado local
    STATE_MAYOR = { movimientos: movs, codigo: codigo, saldoAcc: data.saldoAcumulado || 0 };

    // Mostrar barra de filtros
    if (filtrosDiv) filtrosDiv.style.display = '';

    // Actualizar contadores
    var totalEl = document.getElementById('mayor-total');
    var saldoEl = document.getElementById('mayor-saldo-total');
    if (totalEl) totalEl.textContent = data.total;
    if (saldoEl) saldoEl.textContent = fmtMoney(STATE_MAYOR.saldoAcc);

    // Renderizar con filtros locales (si los hay)
    filtrarMayorLocal();
    showToast(data.total + ' movimientos cargados para ' + codigo, 'ok');
  } catch(e) {
    if (filtrosDiv) filtrosDiv.style.display = 'none';
    tbody.innerHTML = '<tr><td colspan="6"><div class="empty-state"><h3 style="color:var(--accent-red);">Error al cargar</h3><p>' + e.message + '</p><p style="font-size:11px;margin-top:8px;">Verificá que el sync de movimientos se haya ejecutado.</p></div></td></tr>';
    STATE_MAYOR = { movimientos: [], codigo: '', saldoAcc: 0 };
  }
}

/* ─── Filtros locales del Mayor (cliente) ─── */
function filtrarMayorLocal() {
  var tbody = document.getElementById('mayor-body');
  if (!tbody || !STATE_MAYOR.movimientos.length) return;

  var fAsiento = (document.getElementById('mayor-filtro-asiento')?.value || '').toLowerCase().trim();
  var fDesc = (document.getElementById('mayor-filtro-descripcion')?.value || '').toLowerCase().trim();
  var fProv = (document.getElementById('mayor-filtro-proveedor')?.value || '').toLowerCase().trim();
  var algunFiltro = fAsiento || fDesc || fProv;

  var visible = 0;
  var saldoAcum = 0;
  var html = '';

  STATE_MAYOR.movimientos.forEach(function(r){
    // Recorrer en orden ASC para saldo (los datos vienen en orden DESC de la API)
    // No — ya vienen en orden DESC. Reconstruimos el HTML y aplicamos filtro.
    var match = true;
    if (fAsiento && (r.asiento || '').toString().toLowerCase().indexOf(fAsiento) === -1) match = false;
    if (fDesc && (r.descripcion || '').toString().toLowerCase().indexOf(fDesc) === -1) match = false;
    if (fProv && (r.proveedor || '').toString().toLowerCase().indexOf(fProv) === -1) match = false;
    if (!match) return;
    visible++;
  });

  // Reconstruir: desde el array original en orden ASC de fecha para calcular saldo,
  // luego invertir para mostrar más reciente primero.
  // Estado interno: los datos vienen de la API en orden DESC (más reciente primero).
  // Para calcular saldo acumulado, necesitamos orden ASC.
  var ordenAsc = STATE_MAYOR.movimientos.slice().reverse(); // ahora ASC
  var saldoAcc = 0;
  var filas = [];

  for (var i = 0; i < ordenAsc.length; i++) {
    var r = ordenAsc[i];
    saldoAcc += (r.debe || 0) - (r.haber || 0);

    // Aplicar filtro
    var match = true;
    if (fAsiento && (r.asiento || '').toString().toLowerCase().indexOf(fAsiento) === -1) match = false;
    if (fDesc && (r.descripcion || '').toString().toLowerCase().indexOf(fDesc) === -1) match = false;
    if (fProv && (r.proveedor || '').toString().toLowerCase().indexOf(fProv) === -1) match = false;
    if (!match) continue;

    filas.push({ r: r, saldoAtThisPoint: saldoAcc });
  }

  // Invertir: más reciente primero
  filas.reverse();

  html = filas.map(function(f){
    var r = f.r;
    return '<tr>' +
      '<td class="code">' + fmtDate(r.fecha) + '</td>' +
      '<td class="code">' + (r.asiento || '') + '</td>' +
      '<td>' + (r.descripcion || '') + (r.proveedor ? ' <span style="font-size:10px;color:var(--text-muted);">(' + r.proveedor + ')</span>' : '') + '</td>' +
      '<td class="num">' + (r.debe ? fmtMoney(r.debe) : '—') + '</td>' +
      '<td class="num">' + (r.haber ? fmtMoney(r.haber) : '—') + '</td>' +
      '<td class="num"><strong style="color:' + (f.saldoAtThisPoint >= 0 ? 'var(--corona-green-dark)' : 'var(--accent-red)') + '">' + fmtMoney(f.saldoAtThisPoint) + '</strong></td>' +
      '</tr>';
  }).join('');

  tbody.innerHTML = html || '<tr><td colspan="6"><div class="empty-state"><h3>Sin coincidencias</h3><p>Probá con otros filtros</p></div></td></tr>';

  // Actualizar contadores
  var visEl = document.getElementById('mayor-visible');
  if (visEl) {
    visEl.textContent = algunFiltro ? visible + ' (filtrado)' : visible;
    visEl.style.color = algunFiltro ? 'var(--accent-blue)' : '';
  }
}

function limpiarFiltrosMayor() {
  var asientoEl = document.getElementById('mayor-filtro-asiento');
  var descEl = document.getElementById('mayor-filtro-descripcion');
  var provEl = document.getElementById('mayor-filtro-proveedor');
  if (asientoEl) asientoEl.value = '';
  if (descEl) descEl.value = '';
  if (provEl) provEl.value = '';
  filtrarMayorLocal();
}

function filterMayor(v) { cargarMayor(v); }

/* ─── LIBRO DIARIO ─── */
async function cargarLibroDiario() {
  const tbody = document.getElementById('ld-body');
  if (!tbody) return;

  tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3>Consultando... 🔄</h3><p>Cargando libro diario desde SQL Server</p></div></td></tr>';

  var sd = document.getElementById('startDate').value.replace(/-/g, '');
  var ed = document.getElementById('endDate').value.replace(/-/g, '');

  try {
    var result = await fetchWithFallback(CONFIG.endpoints.libroDiario, {
      startDate: sd,
      endDate: ed
    });
    var data = result.data;

    if (!data.success || !data.items || data.items.length === 0) {
      tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3>Sin asientos</h3><p>No hay movimientos en el período ' + sd + ' — ' + ed + '</p></div></td></tr>';
      return;
    }

    // Aplicar filtro de empresa
    var items = data.items;
    var empresa = (document.getElementById('filter-empresa') && document.getElementById('filter-empresa').value || '').trim();
    if (empresa) {
      items = items.filter(function(r){ return (r.unidad || '') === empresa; });
    }

    tbody.innerHTML = items.map(function(r){
      return '<tr>' +
        '<td class="code">' + fmtDate(r.fecha) + '</td>' +
        '<td class="code">' + (r.asiento || '') + '</td>' +
        '<td>' + (r.descripcion || '') + '</td>' +
        '<td style="font-size:12px;">' + (r.ctaDebe || '') + '</td>' +
        '<td style="font-size:12px;">' + (r.ctaHaber || '') + '</td>' +
        '<td class="num"><strong>' + fmtMoney(r.importe) + '</strong></td>' +
        '<td class="code">' + (r.ref || '') + '</td>' +
        '</tr>';
    }).join('');

    showToast(data.total + ' líneas cargadas del libro diario', 'ok');
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3 style="color:var(--accent-red);">Error al cargar</h3><p>' + e.message + '</p><p style="font-size:11px;margin-top:8px;">Verificá la conexión con Node-RED.</p></div></td></tr>';
  }
}

// Legacy: mantiene compatibilidad con renderAll()
function renderLibroDiario() {
  cargarLibroDiario();
}

/* ─── IMPUTACIONES ─── */
var STATE_IMP = { items: [] };

async function cargarImputaciones() {
  const tbody = document.getElementById('imput-body');
  if (!tbody) return;
  tbody.innerHTML = '<tr><td colspan="9"><div class="empty-state"><h3>Consultando... 🔄</h3><p>Cargando imputaciones desde SQL Server</p></div></td></tr>';

  var sd = document.getElementById('startDate').value.replace(/-/g, '');
  var ed = document.getElementById('endDate').value.replace(/-/g, '');

  try {
    var result = await fetchWithFallback(CONFIG.endpoints.imputaciones, { startDate: sd, endDate: ed });
    var data = result.data;
    if (!data.success || !data.items || data.items.length === 0) {
      tbody.innerHTML = '<tr><td colspan="9"><div class="empty-state"><h3>Sin imputaciones</h3><p>No hay registros en ' + sd + ' — ' + ed + '</p></div></td></tr>';
      STATE_IMP.items = [];
      return;
    }
    STATE_IMP.items = data.items;
    filterImputaciones();
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="9"><div class="empty-state"><h3 style="color:var(--accent-red);">Error al cargar</h3><p>' + e.message + '</p></div></td></tr>';
  }
}

function renderImputaciones(tipo) {
  var items = STATE_IMP.items || [];
  // Si STATE_IMP está vacío, caer en STATE.data.imputaciones (legacy/demo)
  if (items.length === 0) items = (STATE.data && STATE.data.imputaciones) || [];

  // Filtro de empresa
  var empresa = (document.getElementById('filter-empresa') && document.getElementById('filter-empresa').value || '').trim();
  if (empresa) items = items.filter(function(r){ return (r.unidad || '') === empresa; });
  if (tipo) items = items.filter(function(r){ return r.tipo === tipo; });

  var tbody = document.getElementById('imput-body');
  if (!tbody) return;
  tbody.innerHTML = items.length ? items.map(function(r){
    var tipoCls = r.tipo === 'VENTA' ? 'badge-ok' : r.tipo === 'AJUSTE' ? 'badge-warn' : r.tipo === 'GASTO' ? 'badge-crit' : 'badge-info';
    return '<tr>' +
      '<td class="code">' + fmtDate(r.fecha) + '</td>' +
      '<td><span class="badge-status ' + tipoCls + '">' + (r.tipo || '') + '</span></td>' +
      '<td class="code">' + (r.comp || '') + '</td>' +
      '<td>' + (r.ente || '') + '</td>' +
      '<td><span style="font-size:11px; background:var(--surface-2); border:1px solid var(--border); border-radius:4px; padding:2px 6px;">' + (r.cc || '') + '</span></td>' +
      '<td class="num">' + fmtMoney(r.neto) + '</td>' +
      '<td class="num">' + (r.iva ? fmtMoney(r.iva) : '—') + '</td>' +
      '<td class="num"><strong>' + fmtMoney(r.total) + '</strong></td>' +
      '<td>' + estadoBadge(r.estado) + '</td>' +
      '</tr>';
  }).join('') : '<tr><td colspan="9"><div class="empty-state"><h3>Sin resultados</h3></div></td></tr>';
}

function filterImputaciones() {
  renderImputaciones(document.getElementById('filter-imputacion-tipo').value);
}

/* ─── EGRESOS ─── */
async function cargarEgresos() {
  const tbody = document.getElementById('ev-body');
  if (!tbody) return;

  tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3>Consultando... 🔄</h3><p>Cargando egresos desde SQL Server</p></div></td></tr>';

  var sd = document.getElementById('startDate').value.replace(/-/g, '');
  var ed = document.getElementById('endDate').value.replace(/-/g, '');

  try {
    var result = await fetchWithFallback(CONFIG.endpoints.egresos, { startDate: sd, endDate: ed });
    var data = result.data;
    if (!data.success || !data.items || data.items.length === 0) {
      tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3>Sin egresos</h3><p>No hay operaciones en ' + sd + ' — ' + ed + '</p></div></td></tr>';
      return;
    }
    var items = data.items;
    var empresa = (document.getElementById('filter-empresa') && document.getElementById('filter-empresa').value || '').trim();
    if (empresa) items = items.filter(function(r){ return (r.unidad || '') === empresa; });
    tbody.innerHTML = items.map(function(r){
      return '<tr>' +
        '<td class="code">' + fmtDate(r.fecha) + '</td>' +
        '<td class="code">' + (r.nro || '') + '</td>' +
        '<td>' + (r.beneficiario || '') + '</td>' +
        '<td>' + (r.concepto || '') + '</td>' +
        '<td><span style="font-size:11px; color:var(--text-secondary);">' + (r.medio || '') + '</span></td>' +
        '<td class="num"><strong>' + fmtMoney(r.importe) + '</strong></td>' +
        '<td>' + estadoBadge(r.estado) + '</td>' +
        '</tr>';
    }).join('');
    showToast(data.total + ' egresos cargados', 'ok');
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3 style="color:var(--accent-red);">Error al cargar</h3><p>' + e.message + '</p></div></td></tr>';
  }
}

function renderEgresos() { cargarEgresos(); }

/* ─── INGRESOS ─── */
async function cargarIngresos() {
  const tbody = document.getElementById('iv-body');
  if (!tbody) return;

  tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3>Consultando... 🔄</h3><p>Cargando ingresos desde SQL Server</p></div></td></tr>';

  var sd = document.getElementById('startDate').value.replace(/-/g, '');
  var ed = document.getElementById('endDate').value.replace(/-/g, '');

  try {
    var result = await fetchWithFallback(CONFIG.endpoints.ingresos, { startDate: sd, endDate: ed });
    var data = result.data;
    if (!data.success || !data.items || data.items.length === 0) {
      tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3>Sin ingresos</h3><p>No hay operaciones en ' + sd + ' — ' + ed + '</p></div></td></tr>';
      return;
    }
    var items = data.items;
    var empresa = (document.getElementById('filter-empresa') && document.getElementById('filter-empresa').value || '').trim();
    if (empresa) items = items.filter(function(r){ return (r.unidad || '') === empresa; });
    tbody.innerHTML = items.map(function(r){
      return '<tr>' +
        '<td class="code">' + fmtDate(r.fecha) + '</td>' +
        '<td class="code">' + (r.nro || '') + '</td>' +
        '<td>' + (r.origen || '') + '</td>' +
        '<td>' + (r.concepto || '') + '</td>' +
        '<td><span style="font-size:11px; color:var(--text-secondary);">' + (r.medio || '') + '</span></td>' +
        '<td class="num"><strong>' + fmtMoney(r.importe) + '</strong></td>' +
        '<td>' + estadoBadge(r.estado) + '</td>' +
        '</tr>';
    }).join('');
    showToast(data.total + ' ingresos cargados', 'ok');
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3 style="color:var(--accent-red);">Error al cargar</h3><p>' + e.message + '</p></div></td></tr>';
  }
}

function renderIngresos() { cargarIngresos(); }

/* ─── PROVEEDORES SALDOS ─── */
async function cargarProveedoresSaldos() {
  const tbody = document.getElementById('prov-saldos-body');
  if (!tbody) return;
  tbody.innerHTML = '<tr><td colspan="8"><div class="empty-state"><h3>Consultando... 🔄</h3></div></td></tr>';
  try {
    var result = await fetchWithFallback(CONFIG.endpoints.provSaldos, {});
    var data = result.data;
    if (!data.success || !data.items || data.items.length === 0) {
      tbody.innerHTML = '<tr><td colspan="8"><div class="empty-state"><h3>Sin proveedores</h3></div></td></tr>';
      return;
    }
    var items = data.items;
    var empresa = (document.getElementById('filter-empresa') && document.getElementById('filter-empresa').value || '').trim();
    if (empresa) items = items.filter(function(r){ return (r.unidad || '') === empresa; });
    tbody.innerHTML = items.map(function(r){
      return '<tr>' +
        '<td class="code">' + (r.cuit || '') + '</td>' +
        '<td><strong>' + (r.proveedor || '') + '</strong></td>' +
        '<td><span style="font-size:11px; color:var(--text-secondary);">' + (r.categoria || '') + '</span></td>' +
        '<td class="num"><strong>' + fmtMoney(r.saldo) + '</strong></td>' +
        '<td class="num" style="color:' + (r.vencido > 0 ? 'var(--accent-red)' : 'var(--text-muted)') + ';">' + (r.vencido > 0 ? fmtMoney(r.vencido) : '—') + '</td>' +
        '<td class="num">' + fmtMoney(r.aVencer) + '</td>' +
        '<td class="code">' + fmtDate(r.ultimoMov) + '</td>' +
        '<td>' + estadoBadge(r.estado) + '</td>' +
        '</tr>';
    }).join('');
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="8"><div class="empty-state"><h3 style="color:var(--accent-red);">Error</h3><p>' + e.message + '</p></div></td></tr>';
  }
}

function renderProveedoresSaldos() { cargarProveedoresSaldos(); }

/* ─── PROVEEDORES PENDIENTES ─── */
var STATE_PEND = { items: [] };

async function cargarProveedoresPendientes() {
  const tbody = document.getElementById('prov-pend-body');
  if (!tbody) return;
  tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3>Consultando... 🔄</h3></div></td></tr>';
  try {
    var result = await fetchWithFallback(CONFIG.endpoints.provPendientes, {});
    var data = result.data;
    if (!data.success || !data.items || data.items.length === 0) {
      tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3>Sin pendientes</h3></div></td></tr>';
      return;
    }
    STATE_PEND.items = data.items;
    renderProveedoresPendientes();
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3 style="color:var(--accent-red);">Error</h3><p>' + e.message + '</p></div></td></tr>';
  }
}

function renderProveedoresPendientes(estado) {
  var items = STATE_PEND.items || [];
  var empresa = (document.getElementById('filter-empresa') && document.getElementById('filter-empresa').value || '').trim();
  if (empresa) items = items.filter(function(r){ return (r.unidad || '') === empresa; });
  if (estado) items = items.filter(function(r){ return r.estado === estado; });

  var tbody = document.getElementById('prov-pend-body');
  if (!tbody) return;
  tbody.innerHTML = items.length ? items.map(function(r){
    return '<tr>' +
      '<td><strong>' + (r.proveedor || '') + '</strong></td>' +
      '<td class="code">' + (r.comp || '') + '</td>' +
      '<td class="code">' + fmtDate(r.fecha) + '</td>' +
      '<td class="code" style="color:' + (r.estado === 'VENCIDO' ? 'var(--accent-red)' : r.estado === 'A_VENCER' ? 'var(--accent-amber)' : 'var(--text-secondary)') + ';">' + fmtDate(r.venc) + '</td>' +
      '<td>' + diasBadge(r.dias) + '</td>' +
      '<td class="num"><strong>' + fmtMoney(r.importe) + '</strong></td>' +
      '<td><span class="badge-status ' + (r.estado === 'VENCIDO' ? 'badge-crit' : r.estado === 'A_VENCER' ? 'badge-warn' : 'badge-ok') + '">' + (r.estado === 'A_VENCER' ? 'A vencer' : r.estado === 'VIGENTE' ? 'Vigente' : 'Vencido') + '</span></td>' +
      '</tr>';
  }).join('') : '<tr><td colspan="7"><div class="empty-state"><h3>Sin resultados</h3></div></td></tr>';
}

function filterPendientes() {
  renderProveedoresPendientes(document.getElementById('filter-pend-estado').value);
}

/* ─── INTEREMPRESAS ─── */
var STATE_INTER = { items: [] };

async function cargarInterempresas() {
  const container = document.getElementById('inter-body');
  if (!container) return;
  container.innerHTML = '<div class="empty-state"><h3>Consultando... 🔄</h3><p>Cargando movimientos interempresas</p></div>';

  var sd = document.getElementById('startDate').value.replace(/-/g, '');
  var ed = document.getElementById('endDate').value.replace(/-/g, '');

  try {
    var result = await fetchWithFallback(CONFIG.endpoints.interempresas, { startDate: sd, endDate: ed });
    var data = result.data;
    if (!data.success || !data.items || data.items.length === 0) {
      container.innerHTML = '<div class="empty-state"><h3>Sin movimientos</h3><p>No hay transacciones interempresas en ' + sd + ' — ' + ed + '</p></div>';
      STATE_INTER.items = [];
      return;
    }
    STATE_INTER.items = data.items;
    renderInterempresas();
  } catch(e) {
    container.innerHTML = '<div class="empty-state"><h3 style="color:var(--accent-red);">Error al cargar</h3><p>' + e.message + '</p></div>';
  }
}

function renderInterempresas() {
  var items = STATE_INTER.items || [];
  // Si STATE_INTER está vacío, caer en STATE.data.interempresas (legacy/demo)
  if (items.length === 0) items = (STATE.data && STATE.data.interempresas) || [];

  // Filtro de empresa
  var empresa = (document.getElementById('filter-empresa') && document.getElementById('filter-empresa').value || '').trim();
  if (empresa) items = items.filter(function(r){ return (r.unidad || '') === empresa; });

  const container = document.getElementById('inter-body');
  if (!container) return;
  container.innerHTML = '';
  if (items.length === 0) {
    container.innerHTML = '<div class="empty-state"><h3>Sin movimientos</h3></div>';
    return;
  }
  items.forEach(function(r) {
    const row = document.createElement('div');
    row.className = 'flow-row';
    var arrowColor = r.tipo === 'entrada' ? 'var(--corona-green)' : 'var(--accent-red)';
    var amountColor = r.tipo === 'entrada' ? 'var(--corona-green-dark)' : 'var(--accent-red)';
    var prefix = r.tipo === 'entrada' ? '+' : '−';
    row.innerHTML =
      '<span class="flow-entity">' + (r.desde || '') + '</span>' +
      '<div class="flow-arrow">' +
        '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="' + arrowColor + '" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>' +
      '</div>' +
      '<span class="flow-entity">' + (r.hacia || '') + '</span>' +
      '<span style="flex:1; font-size:12px; color:var(--text-secondary); margin-left:12px;">' + (r.concepto || '') + '</span>' +
      '<span class="flow-amount" style="color:' + amountColor + ';">' + prefix + fmtMoney(r.importe) + '</span>' +
      '<span class="code" style="margin-left:12px; color:var(--text-muted);">' + fmtDate(r.fecha) + '</span>';
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
