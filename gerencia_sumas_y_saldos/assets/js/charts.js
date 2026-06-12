// assets/js/charts.js
// Módulo para renderizar el gráfico de evolución de saldos
// Utiliza Chart.js (cargado previamente en index.html)

export function renderBalanceChart(labels, data) {
  const ctx = document.getElementById('balanceChart').getContext('2d');
  // Destruir chart previo si existe
  if (window.balanceChart) {
    window.balanceChart.destroy();
  }
  window.balanceChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Saldo acumulado',
          data: data,
          borderColor: 'rgba(75, 192, 192, 1)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          tension: 0.3,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'top' },
        tooltip: { mode: 'index', intersect: false },
      },
      scales: {
        x: { display: true, title: { display: true, text: 'Fecha' } },
        y: {
          display: true,
          title: { display: true, text: 'Saldo (USD)' },
          ticks: { callback: (value) => `$${value.toLocaleString()}` },
        },
      },
    },
  });
}
