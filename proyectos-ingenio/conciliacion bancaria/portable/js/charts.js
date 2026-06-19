/**
 * Charts.js — Configuración de Chart.js para dashboards Corona
 * Patrón portable
 */

const CHART_DEFAULTS = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: { font: { size: 10 }, boxWidth: 10 }
    }
  }
};

function crearChartEstados(canvasId, data) {
  const ctx = document.getElementById(canvasId).getContext('2d');
  return new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Conciliado', 'Solo Banco', 'Solo Calipso', 'Timing'],
      datasets: [{
        data: [data.conciliados || 0, data.soloBanco || 0, data.soloCalipso || 0, data.timing || 0],
        backgroundColor: ['#1D9E75', '#A32D2D', '#BA7517', '#185FA5'],
        borderWidth: 2,
        borderColor: '#fff'
      }]
    },
    options: {
      ...CHART_DEFAULTS,
      cutout: '60%'
    }
  });
}

function crearChartCriticidad(canvasId, data) {
  const ctx = document.getElementById(canvasId).getContext('2d');
  return new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Críticas', 'Altas', 'Medias'],
      datasets: [{
        data: [data.criticas || 0, data.altas || 0, data.medias || 0],
        backgroundColor: ['#A32D2D', '#BA7517', '#185FA5'],
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1 } }
      }
    }
  });
}

function crearChartTendencia(canvasId, labels, dataConciliados, dataPendientes) {
  const ctx = document.getElementById(canvasId).getContext('2d');
  return new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Conciliados',
          data: dataConciliados,
          borderColor: '#1D9E75',
          backgroundColor: 'rgba(29,158,117,0.1)',
          fill: true,
          tension: 0.4
        },
        {
          label: 'Pendientes',
          data: dataPendientes,
          borderColor: '#A32D2D',
          backgroundColor: 'rgba(163,45,45,0.1)',
          fill: true,
          tension: 0.4
        }
      ]
    },
    options: {
      ...CHART_DEFAULTS,
      plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, boxWidth: 10 } } },
      scales: {
        y: { beginAtZero: true, ticks: { font: { size: 10 } } },
        x: { ticks: { font: { size: 10 } } }
      }
    }
  });
}
