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

function fmtFechaInversa(s) {
  if (!s || s.length < 10) return s || '—';
  return s.replace(/-/g, '/');
}

function fmtPorcentaje(n) {
  if (n == null || isNaN(n)) return '—';
  return n.toFixed(1) + '%';
}

function fmtNumero(n) {
  if (n == null || isNaN(n)) return '—';
  return Number(n).toLocaleString('es-AR');
}

function debounce(fn, ms) {
  let timer;
  return function() {
    clearTimeout(timer);
    timer = setTimeout(fn, ms);
  };
}

function semaforoStatus(diferencia) {
  if (diferencia === 0) return { color: 'var(--corona-green)', texto: 'CUADRADO', badge: 'ok' };
  if (diferencia < 500000) return { color: 'var(--accent-amber)', texto: 'TIMING', badge: 'warn' };
  return { color: 'var(--accent-red)', texto: 'PENDIENTE', badge: 'crit' };
}
