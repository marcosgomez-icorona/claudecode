/* APP — Estado global, navegación, movimientos, filtros empresa, init */

/* ════════════════════════════════════════════════
   ESTADO GLOBAL
════════════════════════════════════════════════ */
let STATE = {
  data: null,
  filteredData: null,
  currentSection: 'resumen',
  evolutionChartInstance: null,
  distributionChartInstance: null,
  evolutionChartType: 'line'
};

/* ════════════════════════════════════════════════
   NAVEGACIÓN
════════════════════════════════════════════════ */
const SECTION_META = {
  resumen:              { title: 'Resumen Gerencial',         desc: 'Dashboard ejecutivo — datos consolidados' },
  sumasSaldos:          { title: 'Sumas y Saldos',           desc: 'Balance de sumas y saldos por cuenta' },
  detalleCuenta:        { title: 'Mayor por Cuenta',         desc: 'Movimientos detallados por cuenta contable' },
  libroDiario:          { title: 'Libro Diario',             desc: 'Registro cronológico de asientos' },
  imputaciones:         { title: 'Imputaciones',             desc: 'Imputaciones contables del período' },
  ev:                   { title: 'Egresos de Valores',       desc: 'Pagos y salidas de fondos registrados' },
  iv:                   { title: 'Ingresos de Valores',      desc: 'Cobros y entradas de fondos registrados' },
  proveedoresSaldos:    { title: 'Saldos de Proveedores',    desc: 'Posición actual por proveedor' },
  proveedoresPendientes:{ title: 'Pendientes de Proveedores',desc: 'Facturas pendientes y fechas de vencimiento' },
  interempresas:        { title: 'Movimientos Interempresas',desc: 'Transacciones entre empresas del grupo' },
  alertas:              { title: 'Alertas y Recomendaciones',desc: 'Reglas de control y señales críticas' },
};

function showSection(id) {
  document.querySelectorAll('.section-view').forEach(el => el.classList.remove('active'));
  document.querySelectorAll('.nav-item-custom').forEach(el => el.classList.remove('active'));

  const target = document.getElementById('section-' + id);
  if (target) target.classList.add('active');

  document.querySelectorAll('.nav-item-custom').forEach(el => {
    if (el.getAttribute('onclick') && el.getAttribute('onclick').includes("'" + id + "'")) {
      el.classList.add('active');
    }
  });

  STATE.currentSection = id;
  const meta = SECTION_META[id] || {};
  document.getElementById('topbar-section-title').textContent = meta.title || id;
  document.getElementById('topbar-section-desc').textContent = meta.desc || '';
}

/* ════════════════════════════════════════════════
   MOVIMIENTOS POR CUENTA
════════════════════════════════════════════════ */
function verMovimientos(codigo, cuenta) {
  var modal = new bootstrap.Modal(document.getElementById('movimientosModal'));
  document.getElementById('modal-title').textContent = 'Movimientos: ' + codigo + ' - ' + (cuenta || '');
  document.getElementById('movimientos-loading').style.display = '';
  document.getElementById('movimientos-content').style.display = 'none';
  document.getElementById('movimientos-filtros').style.display = 'none';
  document.getElementById('movimientos-error').style.display = 'none';
  limpiarFiltrosMovimientos();
  modal.show();

  fetchWithFallback(CONFIG.endpoints.movimientos, { codigo: codigo.trim() })
    .then(function(result){
      var data = result.data;
      document.getElementById('movimientos-loading').style.display = 'none';
      if (!data.success || !data.movimientos || data.movimientos.length === 0) {
        document.getElementById('movimientos-error').style.display = '';
        document.getElementById('movimientos-error').textContent = 'Sin movimientos para esta cuenta.';
        return;
      }
      document.getElementById('movimientos-content').style.display = '';
      document.getElementById('movimientos-filtros').style.display = '';
      var tbody = document.getElementById('movimientos-body');
      tbody.innerHTML = '';
      data.movimientos.forEach(function(m){
        var tr = document.createElement('tr');
        tr.innerHTML = '<td class="code">' + (m.fecha || '') + '</td>' +
          '<td class="code">' + (m.asiento || '') + '</td>' +
          '<td>' + (m.descripcion || '') + '</td>' +
          '<td>' + (m.proveedor || '') + '</td>' +
          '<td class="num">' + (m.debe ? fmtMoney(m.debe) : '—') + '</td>' +
          '<td class="num">' + (m.haber ? fmtMoney(m.haber) : '—') + '</td>' +
          '<td class="num"><strong>' + fmtMoney(m.saldo) + '</strong></td>';
        tbody.appendChild(tr);
      });
      document.getElementById('mov-count').textContent = data.total;
      document.getElementById('mov-total-debe').textContent = fmtMoney(data.totalDebe);
      document.getElementById('mov-total-haber').textContent = fmtMoney(data.totalHaber);
      document.getElementById('mov-saldo').textContent = fmtMoney(data.saldoTotal);
    })
    .catch(function(err){
      document.getElementById('movimientos-loading').style.display = 'none';
      document.getElementById('movimientos-error').style.display = '';
      document.getElementById('movimientos-error').textContent = 'Error: ' + err.message;
    });
}

/* ════════════════════════════════════════════════
   FILTROS EN MODAL DE MOVIMIENTOS
════════════════════════════════════════════════ */
function filtrarMovimientos() {
  var fecha = (document.getElementById('filtro-fecha').value || '').toLowerCase().trim();
  var asiento = (document.getElementById('filtro-asiento').value || '').toLowerCase().trim();
  var descripcion = (document.getElementById('filtro-descripcion').value || '').toLowerCase().trim();
  var proveedor = (document.getElementById('filtro-proveedor').value || '').toLowerCase().trim();

  var rows = document.querySelectorAll('#movimientos-body tr');
  var visible = 0;
  rows.forEach(function(row){
    var text = row.textContent.toLowerCase();
    var match = true;
    if (fecha && text.indexOf(fecha) === -1) match = false;
    if (asiento && text.indexOf(asiento) === -1) match = false;
    if (descripcion && text.indexOf(descripcion) === -1) match = false;
    if (proveedor && text.indexOf(proveedor) === -1) match = false;
    row.style.display = match ? '' : 'none';
    if (match) visible++;
  });

  var countEl = document.getElementById('mov-count');
  var algunFiltro = fecha || asiento || descripcion || proveedor;
  if (countEl && algunFiltro) {
    countEl.textContent = visible + ' (filtrado)';
    countEl.style.color = 'var(--accent-blue)';
  } else if (countEl) {
    countEl.style.color = '';
  }
}

function limpiarFiltrosMovimientos() {
  document.getElementById('filtro-fecha').value = '';
  document.getElementById('filtro-asiento').value = '';
  document.getElementById('filtro-descripcion').value = '';
  document.getElementById('filtro-proveedor').value = '';
  var rows = document.querySelectorAll('#movimientos-body tr');
  rows.forEach(function(row){ row.style.display = ''; });
  var countEl = document.getElementById('mov-count');
  if (countEl) countEl.style.color = '';
}

/* ════════════════════════════════════════════════
   FILTRO POR EMPRESA / UNIDAD DE NEGOCIO
════════════════════════════════════════════════ */
function populateEmpresaFilter() {
  var sel = document.getElementById('filter-empresa');
  if (!sel) return;
  var empresas = {};
  var ss = STATE.data && STATE.data.sumasSaldos || [];
  ss.forEach(function(r){
    var u = (r.unidad || '').toString().trim();
    if (u) empresas[u] = true;
  });
  var keys = Object.keys(empresas).sort();
  sel.innerHTML = '<option value="">Todas las empresas</option>';
  keys.forEach(function(k){
    sel.innerHTML += '<option value="' + k.replace(/"/g,'&quot;') + '">' + k + '</option>';
  });
}

function filterByEmpresa() {
  renderAll();
}

function getFilteredData() {
  var data = STATE.data;
  if (!data) return data;
  var empresa = (document.getElementById('filter-empresa') && document.getElementById('filter-empresa').value || '').trim();
  if (!empresa) return data;

  var filtered = {
    sumasSaldos: (data.sumasSaldos || []).filter(function(r){
      return (r.unidad || '').toString().trim() === empresa;
    }),
    mayor: data.mayor || [],
    libroDiario: data.libroDiario || [],
    imputaciones: data.imputaciones || [],
    egresos: data.egresos || [],
    ingresos: data.ingresos || [],
    proveedoresSaldos: data.proveedoresSaldos || [],
    proveedoresPendientes: data.proveedoresPendientes || [],
    interempresas: data.interempresas || [],
    evolucion: data.evolucion || { labels: [], caja: [], ventas: [], costos: [] },
    alertas: data.alertas || [],
    _metadata: data._metadata
  };
  return filtered;
}

/* ════════════════════════════════════════════════
   INIT
════════════════════════════════════════════════ */
(async function init() {
  document.getElementById('uuid').textContent = crypto.randomUUID().slice(0,8).toUpperCase();
  document.getElementById('date-footer').textContent = new Date().toLocaleDateString('es-AR', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' });
  document.getElementById('badge-period').textContent = 'Conectando...';

  // Fechas por defecto al mes actual
  var now = new Date();
  var y = now.getFullYear();
  var m = String(now.getMonth() + 1).padStart(2, '0');
  var lastDay = new Date(y, now.getMonth() + 1, 0).getDate();
  document.getElementById('startDate').value = y + '-' + m + '-01';
  document.getElementById('endDate').value = y + '-' + m + '-' + String(lastDay).padStart(2, '0');

  // Detectar si se abrió como archivo local (file://) — CORS no funciona
  if (window.location.protocol === 'file:') {
    console.warn('index.html abierto como file:// — CORS puede bloquear fetch a Node-RED');
    showToast('Para conectar con Node-RED, serví este archivo con un servidor HTTP local (Live Server, python -m http.server)', 'warn');
  }

  // Auto-cargar desde API (con fallback automático a demo)
  await loadFromAPI();
})();
