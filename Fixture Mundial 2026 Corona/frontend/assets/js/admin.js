document.addEventListener('DOMContentLoaded', async () => {
  const loginForm = document.querySelector('#adminLoginForm');
  const adminPanel = document.querySelector('#adminPanel');
  const lock = document.querySelector('#adminLock');
  const status = document.querySelector('#adminStatus');
  const matchForm = document.querySelector('#matchForm');
  const resultForm = document.querySelector('#resultForm');
  const recalcButton = document.querySelector('#recalcButton');
  const syncFixtureButton = document.querySelector('#syncFixtureButton');
  const matchSelect = document.querySelector('#result_partido_id');
  const matchTable = document.querySelector('[data-admin-matches]');
  let adminKey = '';
  let matches = [];

  loginForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    adminKey = new FormData(loginForm).get('admin_key');
    try {
      await CoronaApi.adminLogin({ admin_key: adminKey });
      lock.classList.add('d-none');
      adminPanel.classList.remove('d-none');
      await loadMatches();
    } catch (error) {
      CoronaUi.setMessage(status, 'danger', error.message);
    }
  });

  matchForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const payload = Object.fromEntries(new FormData(matchForm).entries());
    payload.admin_key = adminKey;
    try {
      await CoronaApi.saveMatch(payload);
      matchForm.reset();
      CoronaUi.setMessage(status, 'success', 'Partido guardado.');
      await loadMatches();
    } catch (error) {
      CoronaUi.setMessage(status, 'danger', error.message);
    }
  });

  resultForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const payload = Object.fromEntries(new FormData(resultForm).entries());
    payload.admin_key = adminKey;
    payload.goles_a_real = Number(payload.goles_a_real);
    payload.goles_b_real = Number(payload.goles_b_real);
    try {
      await CoronaApi.saveResult(payload);
      resultForm.reset();
      CoronaUi.setMessage(status, 'success', 'Resultado guardado y puntajes recalculados.');
      await loadMatches();
    } catch (error) {
      CoronaUi.setMessage(status, 'danger', error.message);
    }
  });

  recalcButton.addEventListener('click', async () => {
    try {
      await CoronaApi.recalculateScores({ admin_key: adminKey });
      CoronaUi.setMessage(status, 'success', 'Puntajes y rankings recalculados.');
    } catch (error) {
      CoronaUi.setMessage(status, 'danger', error.message);
    }
  });

  syncFixtureButton.addEventListener('click', async () => {
    syncFixtureButton.disabled = true;
    syncFixtureButton.textContent = 'Sincronizando...';
    try {
      const result = await CoronaApi.syncOfficialFixture({ admin_key: adminKey, force: true });
      CoronaUi.setMessage(status, 'success', `Fixture sincronizado. Equipos: ${result.teams || 0}. Partidos: ${result.matches || 0}. Resultados: ${result.results || 0}.`);
      await loadMatches();
    } catch (error) {
      CoronaUi.setMessage(status, 'danger', error.message);
    } finally {
      syncFixtureButton.disabled = false;
      syncFixtureButton.textContent = 'Sincronizar fixture';
    }
  });

  async function loadMatches() {
    matches = await CoronaApi.listMatches();
    matchSelect.innerHTML = '<option value="">Seleccionar partido</option>' + matches
      .map((match) => `<option value="${match.partido_id}">${CoronaUi.getTeamFlag(match.equipo_a)} ${match.equipo_a} vs ${CoronaUi.getTeamFlag(match.equipo_b)} ${match.equipo_b}</option>`)
      .join('');
    matchTable.innerHTML = matches.map((match) => `
      <tr>
        <td>${match.fase}</td>
        <td>${CoronaUi.formatMatchTeams(match.equipo_a, match.equipo_b)}</td>
        <td>${CoronaUi.formatDateTime(match.fecha_partido)}</td>
        <td>${CoronaUi.formatDateTime(match.fecha_limite_prediccion)}</td>
        <td>${match.estado}</td>
      </tr>
    `).join('') || '<tr><td colspan="5">Sin partidos cargados.</td></tr>';
  }
});
