document.addEventListener('DOMContentLoaded', async () => {
  const countdown = document.querySelector('[data-countdown]');
  const matchesContainer = document.querySelector('[data-next-matches]');

  try {
    const data = await CoronaApi.getHomeData();
    CoronaUi.startCountdown(data.config && data.config.fecha_inicio_mundial ? data.config.fecha_inicio_mundial : window.CORONA_CONFIG.mundialStartDate, countdown);
    renderMatches(data.matches || []);
  } catch (error) {
    CoronaUi.startCountdown(window.CORONA_CONFIG.mundialStartDate, countdown);
    renderMatches([]);
  }

  function renderMatches(matches) {
    const nextMatches = matches.slice(0, 4);
    if (!nextMatches.length) {
      CoronaUi.renderEmpty(matchesContainer, 'Todavia no hay partidos cargados.');
      return;
    }

    matchesContainer.innerHTML = nextMatches.map((match) => `
      <div class="col-md-6 col-xl-3 micro-rise">
        <div class="card corona-card h-100">
          <div class="card-body">
            <span class="icon-circle-sm icon-soft-sky mb-2"><i class="bi bi-flag-fill"></i></span>
            <span class="badge badge-gold mb-2 ms-2">${match.fase || 'Partido'}</span>
            <h3 class="h6 fw-bold">${CoronaUi.formatMatchTeams(match.equipo_a, match.equipo_b)}</h3>
            <p class="text-muted small mb-1"><i class="bi bi-clock me-1"></i>${CoronaUi.formatDateTime(match.fecha_partido)}</p>
            <p class="small mb-0"><i class="bi bi-lock me-1"></i>Cierre: ${CoronaUi.formatDateTime(match.fecha_limite_prediccion)}</p>
          </div>
        </div>
      </div>
    `).join('');
  }
});
