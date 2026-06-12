document.addEventListener('DOMContentLoaded', async () => {
  const podium = document.querySelector('[data-podium]');
  const tableBody = document.querySelector('[data-ranking-body]');
  const areaBody = document.querySelector('[data-ranking-area-body]');

  if (tableBody) {
    try {
      const ranking = await CoronaApi.getRankingIndividual();
      renderIndividual(ranking || []);
    } catch (error) {
      tableBody.innerHTML = `<tr><td colspan="6" class="text-danger">${error.message}</td></tr>`;
    }
  }

  if (areaBody) {
    try {
      const ranking = await CoronaApi.getRankingAreas();
      renderAreas(ranking || []);
    } catch (error) {
      areaBody.innerHTML = `<tr><td colspan="5" class="text-danger">${error.message}</td></tr>`;
    }
  }

  function renderIndividual(items) {
    if (!items.length) {
      podium.innerHTML = '';
      tableBody.innerHTML = '<tr><td colspan="6">Todavia no hay ranking disponible.</td></tr>';
      return;
    }

    podium.innerHTML = items.slice(0, 3).map((item) => `
      <div class="col-md-4 podium-card micro-rise">
        <div class="card corona-card podium-card h-100">
          <div class="card-body text-center">
            <span class="podium-rank rank-${item.posicion}"><i class="bi ${item.posicion === 1 ? 'bi-trophy-fill' : 'bi-award-fill'}"></i></span>
            <h2 class="h5 mt-3">${item.nombre_apellido}</h2>
            <p class="text-muted mb-1">${item.area}</p>
            <strong>${item.puntaje_total} pts</strong>
          </div>
        </div>
      </div>
    `).join('');

    tableBody.innerHTML = items.map((item) => `
      <tr>
        <td><span class="position-badge">${item.posicion}</span></td>
        <td>${item.nombre_apellido}</td>
        <td>${item.area}</td>
        <td>${item.puntaje_total}</td>
        <td>${item.resultados_exactos || 0}</td>
        <td>${item.ganadores_correctos || 0}</td>
      </tr>
    `).join('');
  }

  function renderAreas(items) {
    if (!items.length) {
      areaBody.innerHTML = '<tr><td colspan="5">Todavia no hay ranking por area.</td></tr>';
      return;
    }

    areaBody.innerHTML = items.map((item) => `
      <tr>
        <td><span class="position-badge">${item.posicion}</span></td>
        <td><i class="bi bi-building me-2 text-danger"></i>${item.area}</td>
        <td>${item.cantidad_participantes}</td>
        <td>${Number(item.puntaje_promedio_top5).toFixed(2)}</td>
        <td>${item.puntaje_total_top5}</td>
      </tr>
    `).join('');
  }
});
