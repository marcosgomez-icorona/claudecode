/**
 * Utils.js — Formateo y utilidades para dashboards Corona
 * Patrón portable — Sin dependencias externas
 */

function fmt$(n) {
  if (n == null || isNaN(n)) return '—';
  return '$ ' + Number(n).toLocaleString('es-AR', { maximumFractionDigits: 0 });
}

function fmtFecha(s) {
  if (!s || s.length < 8) return s || '—';
  return s.substring(6, 8) + '/' + s.substring(4, 6) + '/' + s.substring(0, 4);
}

function fmtNumero(n) {
  if (n == null || isNaN(n)) return '—';
  return Number(n).toLocaleString('es-AR');
}

function semaforoStatus(diferencia) {
  if (diferencia === 0) return { color: 'var(--c-green)', texto: 'CUADRADO', badge: 'ok' };
  if (diferencia < 500000) return { color: 'var(--c-amber)', texto: 'TIMING', badge: 'warn' };
  return { color: 'var(--c-red)', texto: 'PENDIENTE', badge: 'crit' };
}

function fmtTimestamp() {
  return new Date().toLocaleString('es-AR', { timeStyle: 'short', dateStyle: 'short' });
}

function showToast(message, type) {
  type = type || 'success';
  var icons = { success: '✅', error: '❌', warning: '⚠️' };
  var container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  var el = document.createElement('div');
  el.className = 'toast-corona ' + type;
  el.innerHTML = '<span class="toast-icon">' + (icons[type] || 'ℹ️') + '</span>' +
    '<span>' + message + '</span>' +
    '<button class="toast-close" onclick="this.parentElement.remove()">&times;</button>';
  container.appendChild(el);
  setTimeout(function() { if (el.parentElement) el.remove(); }, 5000);
  el.addEventListener('click', function() { el.remove(); });
}

function skeletonRows(cols, rows) {
  var html = '';
  for (var r = 0; r < rows; r++) {
    html += '<tr>';
    for (var c = 0; c < cols; c++) {
      html += '<td><span class="skeleton" style="width:' + (40 + Math.random() * 40) + '%"> </span></td>';
    }
    html += '</tr>';
  }
  return html;
}
