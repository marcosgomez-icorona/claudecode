/**
 * App.js — Lógica principal del Dashboard de Conciliación Bancaria
 * Patrón portable Corona: fetch → render → auto-refresh
 */

let ACTIVE_BACKEND = null;
let ACTIVE_BACKEND_LABEL = '';
let STATE = { resumen: null, pendientes: null, detalle: null };
let CHARTS = {};

// ── API Fetch con failover ──

async function apiFetch(endpoint) {
  for (const backend of CONFIG.backends) {
    try {
      const controller = new AbortController();
      const id = setTimeout(() => controller.abort(), backend.timeout);
      const res = await fetch(backend.url + endpoint, { signal: controller.signal });
      clearTimeout(id);
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();
      ACTIVE_BACKEND = backend.url;
      ACTIVE_BACKEND_LABEL = backend.name;
      return data;
    } catch (e) {
      console.warn('[Conciliacion] Backend ' + backend.name + ' no responde:', e.message);
    }
  }
  throw new Error('Todos los backends fallaron - ' + endpoint);
}

// ── Indicador de backend ──

function updateBackendIndicator() {
  var el = document.getElementById('backend-indicator');
  if (!el) return;
  if (ACTIVE_BACKEND_LABEL === 'Cloud') {
    el.innerHTML = '🟢 Cloud (' + ACTIVE_BACKEND + ')';
    el.style.color = 'var(--corona-green-dark)';
    el.style.background = 'var(--corona-green-light)';
  } else if (ACTIVE_BACKEND_LABEL === 'LAN') {
    el.innerHTML = '🟡 LAN (' + ACTIVE_BACKEND + ')';
    el.style.color = 'var(--accent-amber)';
    el.style.background = 'var(--accent-amber-light)';
  } else {
    el.innerHTML = '🔴 Offline';
    el.style.color = 'var(--accent-red)';
    el.style.background = 'var(--accent-red-light)';
  }
}

// ── Error handler ──

function showError(section, error) {
  console.error('[Conciliacion] Error en', section, error);
  updateBackendIndicator();
  var bodies = {
    resumen: 'resumen-body',
    pendientes: 'pendientes-body',
    detalle: 'detalle-body',
    gastos: 'gastos-body'
  };
  var id = bodies[section];
  if (id) {
    document.getElementById(id).innerHTML =
      '<tr><td colspan="8"><div class="empty"><h3>⚠ Sin conexión</h3>' +
      '<p>No se pudo conectar a ningún backend Node-RED. Verificá que al menos uno esté activo.</p>' +
      '<button class="btn btn-primary" onclick="refreshAll()">Reintentar</button></div></td></tr>';
  }
}

// ── Navegación ──

function showSection(id) {
  document.querySelectorAll('.section-view').forEach(e => e.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(e => e.classList.remove('active'));
  var t = document.getElementById('section-' + id);
  if (t) t.classList.add('active');
  document.querySelectorAll('.nav-item').forEach(e => {
    if (e.getAttribute('onclick') && e.getAttribute('onclick').includes("'" + id + "'"))
      e.classList.add('active');
  });
  STATE._current = id;

  var titles = {
    resumen: ['Resumen Ejecutivo', 'Conciliación Bancaria — Multi-banco'],
    pendientes: ['Pendientes de Conciliación', 'Ítems sin conciliar — Requieren acción'],
    detalle: ['Detalle por Banco', 'Cruce banco vs Calipso'],
    gastos: ['Gastos Bancarios', 'Gastos e impuestos detectados en extractos'],
    graficos: ['Gráficos', 'Visualización de datos de conciliación']
  };
  var tInfo = titles[id] || [id, ''];
  document.getElementById('section-title').textContent = tInfo[0];
  document.getElementById('section-desc').textContent = tInfo[1];

  if (id === 'resumen') cargarResumen();
  else if (id === 'pendientes') cargarPendientes();
  else if (id === 'detalle') cargarDetalle();
  else if (id === 'gastos') cargarGastos();
  else if (id === 'graficos') cargarGraficos();
}

async function refreshAll() {
  ACTIVE_BACKEND = null;
  ACTIVE_BACKEND_LABEL = '';
  updateBackendIndicator();
  showSection(STATE._current || 'resumen');
}

// ── Carga de secciones ──

async function cargarResumen() {
  try {
    var d = await apiFetch('/api/conciliacion/resumen');
    STATE.resumen = d;
    var row = (d.data || [])[0] || {};
    var sema = semaforoStatus(row.diferencia);

    document.getElementById('resumen-kpis').innerHTML =
      '<div class="kpi-card green"><div class="kpi-label">Total Banco</div><div class="kpi-value mono">' + fmt$(row.totalBanco) + '</div><div class="kpi-delta">Extracto bancario</div></div>' +
      '<div class="kpi-card blue"><div class="kpi-label">Total Calipso</div><div class="kpi-value mono">' + fmt$(row.totalCalipso) + '</div><div class="kpi-delta">Depurado (sin reversiones)</div></div>' +
      '<div class="kpi-card ' + (row.diferencia === 0 ? 'green' : 'red') + '"><div class="kpi-label">Diferencia</div><div class="kpi-value mono">' + fmt$(row.diferencia) + '</div><div class="kpi-delta">' + (row.diferencia === 0 ? '✅ Cuadrado' : '⚠ Requiere revisión') + '</div></div>' +
      '<div class="kpi-card ' + (row.pendientes > 0 ? 'red' : 'green') + '"><div class="kpi-label">Pendientes</div><div class="kpi-value">' + (row.pendientes || 0) + '</div><div class="kpi-delta">Ítems sin conciliar</div></div>' +
      '<div class="kpi-card ' + (sema.badge === 'ok' ? 'green' : sema.badge === 'warn' ? 'amber' : 'red') + '"><div class="kpi-label">Estado</div><div class="kpi-value" style="font-size:18px;"><span class="semaforo"><span class="semaforo-dot" style="background:' + sema.color + '"></span>' + sema.texto + '</span></div><div class="kpi-delta">' + (row.diferencia === 0 ? '✅ Conciliación exitosa' : '⚠ Acción requerida') + '</div></div>';

    document.getElementById('resumen-body').innerHTML = (d.data || []).map(function(r) {
      var semaRow = semaforoStatus(r.diferencia);
      return '<tr><td><strong>' + r.banco + '</strong></td><td class="code">' + r.periodo + '</td>' +
        '<td class="num">' + fmt$(r.totalBanco) + '</td><td class="num">' + fmt$(r.totalCalipso) + '</td>' +
        '<td class="num"><strong style="color:' + (r.diferencia === 0 ? 'var(--corona-green-dark)' : 'var(--accent-red)') + '">' + fmt$(r.diferencia) + '</strong></td>' +
        '<td><span class="badge-' + semaRow.badge + '">' + semaRow.texto + '</span></td>' +
        '<td class="num">' + r.pendientes + '</td></tr>';
    }).join('');
  } catch (e) {
    showError('resumen', e);
  }
}

async function cargarPendientes() {
  try {
    var d = await apiFetch('/api/conciliacion/pendientes');
    STATE.pendientes = d;
    document.getElementById('pendientes-kpis').innerHTML =
      '<div class="kpi-card red"><div class="kpi-label">🔴 Críticas</div><div class="kpi-value">' + (d.criticas || 0) + '</div><div class="kpi-delta">> $10M o gastos sin registrar</div></div>' +
      '<div class="kpi-card amber"><div class="kpi-label">🟠 Altas</div><div class="kpi-value">' + (d.altas || 0) + '</div><div class="kpi-delta">> $1M</div></div>' +
      '<div class="kpi-card blue"><div class="kpi-label">🟡 Medias</div><div class="kpi-value">' + (d.medias || 0) + '</div><div class="kpi-delta">Resto de pendientes</div></div>' +
      '<div class="kpi-card ' + (d.total > 0 ? 'red' : 'green') + '"><div class="kpi-label">Total Pendientes</div><div class="kpi-value">' + (d.total || 0) + '</div><div class="kpi-delta">' + (d.total === 0 ? '✅ Todo conciliado' : '⚠ Requieren acción') + '</div></div>';
    renderPendientes();
  } catch (e) {
    showError('pendientes', e);
  }
}

function renderPendientes() {
  var d = STATE.pendientes;
  if (!d) return;
  var filtro = document.getElementById('filter-pend-crit') ? document.getElementById('filter-pend-crit').value : '';
  var data = filtro ? (d.data || []).filter(function(r) { return r.criticidad === filtro; }) : (d.data || []);
  document.getElementById('pendientes-body').innerHTML = data.length
    ? data.map(function(r) {
        var rowClass = r.criticidad === 'CRITICA' ? 'pendiente-row-critica' : r.criticidad === 'ALTA' ? 'pendiente-row-alta' : '';
        var badgeClass = r.criticidad === 'CRITICA' ? 'badge-crit' : r.criticidad === 'ALTA' ? 'badge-warn' : 'badge-ok';
        return '<tr class="' + rowClass + '"><td><strong>' + r.banco + '</strong></td><td class="code">' + fmtFecha(r.fecha) + '</td>' +
          '<td>' + r.descripcion + '</td><td>' + r.origen + '</td>' +
          '<td class="num"><strong>' + fmt$(r.importe) + '</strong></td><td>' + r.tipo + '</td>' +
          '<td><span class="' + badgeClass + '">' + r.criticidad + '</span></td><td style="font-size:11px;color:var(--text-secondary);">' + (r.observacion || '') + '</td></tr>';
      }).join('')
    : '<tr><td colspan="8"><div class="empty"><h3>Sin pendientes</h3><p>Todos los movimientos están conciliados ✅</p></div></td></tr>';
}

async function cargarDetalle() {
  try {
    var d = await apiFetch('/api/conciliacion/detalle');
    STATE.detalle = d;
    document.getElementById('detalle-kpis').innerHTML =
      '<div class="kpi-card green"><div class="kpi-label">Conciliados</div><div class="kpi-value">' + (d.conciliados || 0) + '</div></div>' +
      '<div class="kpi-card red"><div class="kpi-label">Solo Banco</div><div class="kpi-value">' + (d.soloBanco || 0) + '</div></div>' +
      '<div class="kpi-card amber"><div class="kpi-label">Solo Calipso</div><div class="kpi-value">' + (d.soloCalipso || 0) + '</div></div>' +
      '<div class="kpi-card blue"><div class="kpi-label">Timing</div><div class="kpi-value">' + (d.timing || 0) + '</div></div>';

    document.getElementById('detalle-body').innerHTML = (d.data || []).slice(0, 500).map(function(r) {
      return '<tr><td class="code">' + fmtFecha(r.fecha) + '</td><td>' + r.tipo + '</td><td>' + r.descripcion + '</td>' +
        '<td class="num">' + (r.banco ? fmt$(r.banco) : '—') + '</td><td class="num">' + (r.calipso ? fmt$(r.calipso) : '—') + '</td>' +
        '<td class="num"><strong style="color:' + (r.dif > 100 ? 'var(--accent-red)' : 'var(--text-muted)') + '">' + fmt$(r.dif) + '</strong></td>' +
        '<td><span class="badge-' + (r.estado === 'CONCILIADO' ? 'ok' : r.estado === 'TIMING' ? 'warn' : 'crit') + '">' + r.estado + '</span></td></tr>';
    }).join('');
  } catch (e) {
    showError('detalle', e);
  }
}

async function cargarGastos() {
  try {
    var d = await apiFetch('/api/conciliacion/detalle');
    var gastos = (d.data || []).filter(function(r) {
      var t = (r.tipo || '').toLowerCase();
      var desc = (r.descripcion || '').toLowerCase();
      return t.indexOf('gasto') >= 0 || t.indexOf('impuesto') >= 0 ||
             desc.indexOf('iva') >= 0 || desc.indexOf('iibb') >= 0 || desc.indexOf('comision') >= 0;
    });
    document.getElementById('gastos-body').innerHTML = gastos.length
      ? gastos.map(function(r) {
          return '<tr><td class="code">' + fmtFecha(r.fecha) + '</td><td>' + r.descripcion + '</td><td>' + r.tipo + '</td>' +
            '<td class="num"><strong>' + fmt$(Math.abs(r.banco || r.calipso || 0)) + '</strong></td>' +
            '<td><span class="badge-' + (r.estado === 'CONCILIADO' ? 'ok' : 'crit') + '">' + r.estado + '</span></td></tr>';
        }).join('')
      : '<tr><td colspan="5"><div class="empty"><h3>Sin gastos detectados</h3></div></td></tr>';
  } catch (e) {
    showError('gastos', e);
  }
}

async function cargarGraficos() {
  try {
    if (!STATE.detalle) STATE.detalle = await apiFetch('/api/conciliacion/detalle');
    var d = STATE.detalle;
    if (!d) return;

    if (CHARTS.estados) CHARTS.estados.destroy();
    CHARTS.estados = crearChartEstados('chartEstados', d);

    if (!STATE.pendientes) STATE.pendientes = await apiFetch('/api/conciliacion/pendientes');
    var pd = STATE.pendientes || {};
    if (CHARTS.criticidad) CHARTS.criticidad.destroy();
    CHARTS.criticidad = crearChartCriticidad('chartCriticidad', pd);
  } catch (e) {
    console.error('[Conciliacion] Error en graficos:', e);
    updateBackendIndicator();
    document.querySelectorAll('#section-graficos .chart-wrap').forEach(function(el) {
      el.innerHTML = '<div class="empty"><h3>⚠ Error al cargar</h3><p>No se pudieron cargar los datos para los gráficos.</p></div>';
    });
  }
}

// ── Google Sheets Sync (patrón portable Corona) ──

async function syncToSheets() {
  var btn = document.getElementById('btn-sync');
  if (btn) { btn.textContent = '⏳ Sincronizando...'; btn.disabled = true; }
  try {
    var data = await apiFetch('/api/conciliacion/sync-sheets');
    alert('✅ Sincronización completada: ' + (data.registros || 0) + ' registros actualizados.');
  } catch (e) {
    alert('❌ Error al sincronizar: ' + e.message);
  } finally {
    if (btn) { btn.textContent = '📤 Sync Google Sheets'; btn.disabled = false; }
  }
}

// ── Init ──

document.addEventListener('DOMContentLoaded', function() {
  showSection('resumen');
  // Auto-refresh cada 120 segundos
  setInterval(function() {
    if (STATE._current) showSection(STATE._current);
  }, 120000);
});
