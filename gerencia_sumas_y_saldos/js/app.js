// app.js
// Front‑end logic for the Sumas y Saldos report.
// Handles CSV upload (PapaParse), DB query (fetch to ./db_query.js server),
// and updates UI: metric cards, accounts table, Chart.js chart, and traceability footer.

document.addEventListener('DOMContentLoaded', () => {
  // Generate or retrieve UUID for this session
  const uuidElem = document.getElementById('uuid');
  let sessionId = localStorage.getItem('gerenciaSessionId');
  if (!sessionId) {
    sessionId = crypto.randomUUID();
    localStorage.setItem('gerenciaSessionId', sessionId);
  }
  uuidElem.textContent = sessionId;
  // Set current date
  document.getElementById('date').textContent = new Date().toLocaleString();

  const fileInput = document.getElementById('fileInput');
  const processBtn = document.getElementById('processBtn');
  const loadDbBtn = document.getElementById('loadDbBtn');

  const criticalThresholdInput = document.getElementById('criticalThreshold');
  const variationThresholdInput = document.getElementById('variationThreshold');
  const startDateInput = document.getElementById('startDate');
  const endDateInput = document.getElementById('endDate');

  const metricsSection = document.getElementById('metricsSection');
  const tableSection = document.getElementById('tableSection');
  const chartSection = document.getElementById('chartSection');
  const alertSection = document.getElementById('alertSection');
  const alertBox = document.getElementById('alertBox');

  const totalBalanceElem = document.getElementById('totalBalance');
  const criticalCountElem = document.getElementById('criticalCount');
  const avgVariationElem = document.getElementById('avgVariation');
  const alertCountElem = document.getElementById('alertCount');

  // Helper: format numbers with thousands separators
  const fmt = (num) => Number(num).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

  // Render table rows
  function renderTable(data) {
    const tbody = document.querySelector('#accountsTable tbody');
    tbody.innerHTML = '';
    data.forEach(row => {
      const tr = document.createElement('tr');
      const variation = row.DEBE_PERIODO === 0 ? 0 : ((row.DEBE_PERIODO - row.HABER_PERIODO) / row.DEBE_PERIODO) * 100;
      const state = [];
      if (row.SALDO_PERIODO >= parseFloat(criticalThresholdInput.value)) state.push('Crítico');
      if (Math.abs(variation) >= parseFloat(variationThresholdInput.value)) state.push('Variación');
      const estado = state.join(', ') || 'OK';
      tr.innerHTML = `
        <td>${row.CODIGO} - ${row.CUENTA}</td>
        <td>${fmt(row.SALDO_PERIODO)}</td>
        <td>${fmt(variation)} %</td>
        <td>${estado}</td>
      `;
      tbody.appendChild(tr);
    });
  }

  // Render Chart.js bar chart (saldo por cuenta)
  function renderChart(data) {
    const ctx = document.getElementById('balanceChart').getContext('2d');
    const labels = data.map(r => r.CODIGO);
    const balances = data.map(r => r.SALDO_PERIODO);
    // Destroy previous chart if exists
    if (window.balanceChart) window.balanceChart.destroy();
    window.balanceChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Saldo Período',
          data: balances,
          backgroundColor: '#00bcd4',
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
      }
    });
  }

  // Compute and display metrics
  function updateMetrics(data) {
    const criticalThreshold = parseFloat(criticalThresholdInput.value);
    const variationThreshold = parseFloat(variationThresholdInput.value);
    let totalBalance = 0;
    let criticalCount = 0;
    let variationSum = 0;
    let alertCount = 0;
    data.forEach(row => {
      totalBalance += row.SALDO_PERIODO;
      if (row.SALDO_PERIODO >= criticalThreshold) criticalCount++;
      const variation = row.DEBE_PERIODO === 0 ? 0 : ((row.DEBE_PERIODO - row.HABER_PERIODO) / row.DEBE_PERIODO) * 100;
      variationSum += variation;
      if (Math.abs(variation) >= variationThreshold) alertCount++;
    });
    const avgVariation = data.length ? variationSum / data.length : 0;
    totalBalanceElem.textContent = fmt(totalBalance);
    criticalCountElem.textContent = criticalCount;
    avgVariationElem.textContent = fmt(avgVariation) + ' %';
    alertCountElem.textContent = alertCount;
    // Show alerts section if any alerts
    if (alertCount > 0) {
      alertBox.textContent = `${alertCount} cuenta(s) supera(n) el umbral de variación (${variationThreshold}%).`;
      alertSection.style.display = 'block';
    } else {
      alertSection.style.display = 'none';
    }
    metricsSection.style.display = 'flex';
  }

  // CSV processing
  processBtn.addEventListener('click', () => {
    const file = fileInput.files[0];
    if (!file) {
      alert('Seleccione un archivo CSV primero.');
      return;
    }
    Papa.parse(file, {
      header: true,
      dynamicTyping: true,
      skipEmptyLines: true,
      complete: (results) => {
        const data = results.data.map(r => ({
          CODIGO: r.CODIGO,
          CUENTA: r.CUENTA,
          DEBE_PERIODO: r.DEBE_PERIODO,
          HABER_PERIODO: r.HABER_PERIODO,
          SALDO_PERIODO: r.SALDO_PERIODO
        })).filter(r => r.CODIGO !== undefined);
        updateMetrics(data);
        renderTable(data);
        renderChart(data);
        tableSection.style.display = 'block';
        chartSection.style.display = 'block';
      },
      error: (err) => {
        console.error('Parse error:', err);
        alert('Error al leer el CSV.');
      }
    });
  });

  // DB loading
  loadDbBtn.addEventListener('click', async () => {
    const start = startDateInput.value.trim();
    const end = endDateInput.value.trim();
    if (!start || !end) {
      alert('Ingrese fechas de inicio y fin.');
      return;
    }
    try {
      const response = await fetch('http://localhost:3000/query', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ startDate: start, endDate: end })
      });
      if (!response.ok) throw new Error(`Server responded ${response.status}`);
      const data = await response.json();
      // Expected fields match the SELECT alias names
      updateMetrics(data);
      renderTable(data);
      renderChart(data);
      tableSection.style.display = 'block';
      chartSection.style.display = 'block';
    } catch (e) {
      console.error('DB fetch error:', e);
      alert('Error al consultar la base de datos. Verifique que el servicio db_query.js esté corriendo.');
    }
  });
});
