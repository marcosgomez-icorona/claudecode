/**
 * Charts.js — Configuración de Chart.js para dashboards Corona
 * Patrón portable
 */

const CHART_DEFAULTS = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'bottom', labels: { font: { size: 10 }, boxWidth: 10, padding: 12 } }
  }
};

function crearChartEstados(canvasId, data) {
  var ctx = document.getElementById(canvasId);
  if (!ctx) return null;
  ctx = ctx.getContext('2d');
  return new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Conciliado', 'Solo Banco', 'Solo Calipso', 'Timing'],
      datasets: [{
        data: [data.conciliados || 0, data.soloBanco || 0, data.soloCalipso || 0, data.timing || 0],
        backgroundColor: ['#1D9E75', '#A32D2D', '#BA7517', '#185FA5'],
        borderWidth: 2,
        borderColor: '#fff',
        hoverOffset: 8
      }]
    },
    options: Object.assign({}, CHART_DEFAULTS, { cutout: '62%' })
  });
}

function crearChartCriticidad(canvasId, data) {
  var ctx = document.getElementById(canvasId);
  if (!ctx) return null;
  ctx = ctx.getContext('2d');
  return new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Críticas', 'Altas', 'Medias'],
      datasets: [{
        data: [data.criticas || 0, data.altas || 0, data.medias || 0],
        backgroundColor: ['#A32D2D', '#BA7517', '#185FA5'],
        borderRadius: 4,
        borderSkipped: false
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } },
        x: { ticks: { font: { size: 10 } } }
      }
    }
  });
}

function crearChartCalidad(canvasId, data) {
  var ctx = document.getElementById(canvasId);
  if (!ctx) return null;
  ctx = ctx.getContext('2d');
  var cal = data.calNiveles || {};
  return new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['N1 — Comprobante', 'N2 — Exacto', 'N3 — ±1% 1d', 'N4 — ±1% 3d', 'Sin Match'],
      datasets: [{
        data: [cal.n1 || 0, cal.n2 || 0, cal.n3 || 0, cal.n4 || 0, cal.sinMatch || 0],
        backgroundColor: ['#1D9E75', '#5DCAA5', '#BA7517', '#185FA5', '#A32D2D'],
        borderRadius: 4,
        borderSkipped: false
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } },
        y: { ticks: { font: { size: 9 } } }
      }
    }
  });
}
