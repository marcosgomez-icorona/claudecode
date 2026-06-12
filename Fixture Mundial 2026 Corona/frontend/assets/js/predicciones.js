document.addEventListener('DOMContentLoaded', initPredictionsPage);

const groupColors = ['#20c997', '#ff4d4f', '#fd7e14', '#3b82f6', '#a855f7', '#65c900', '#ec4899', '#06b6d4', '#8b5cf6', '#0ea5e9', '#f59e0b', '#10b981'];
const flagMap = {
  Argentina: '🇦🇷', Argelia: '🇩🇿', Alemania: '🇩🇪', Australia: '🇦🇺', Austria: '🇦🇹',
  Belgica: '🇧🇪', Brasil: '🇧🇷', Canada: '🇨🇦', Catar: '🇶🇦', Colombia: '🇨🇴',
  'Corea del Sur': '🇰🇷', Croacia: '🇭🇷', Ecuador: '🇪🇨', Egipto: '🇪🇬', Escocia: '🏴',
  Espana: '🇪🇸', Francia: '🇫🇷', Ghana: '🇬🇭', Haiti: '🇭🇹', Inglaterra: '🏴',
  Iran: '🇮🇷', Iraq: '🇮🇶', Japon: '🇯🇵', Jordania: '🇯🇴', Marruecos: '🇲🇦',
  Mexico: '🇲🇽', Noruega: '🇳🇴', 'Nueva Zelanda': '🇳🇿', Panama: '🇵🇦', Paraguay: '🇵🇾',
  'Paises Bajos': '🇳🇱', Portugal: '🇵🇹', 'RD Congo': '🇨🇩', 'Republica Checa': '🇨🇿',
  Senegal: '🇸🇳', Sudafrica: '🇿🇦', Suecia: '🇸🇪', Suiza: '🇨🇭', Tunez: '🇹🇳',
  Turquia: '🇹🇷', Uruguay: '🇺🇾', Uzbekistan: '🇺🇿', 'Costa de Marfil': '🇨🇮',
  'Estados Unidos': '🇺🇸', 'Arabia Saudita': '🇸🇦', 'Bosnia y Herzegovina': '🇧🇦',
  'Cabo Verde': '🇨🇻', Curazao: '🇨🇼'
};

const countryCodeMap = {
  Argentina: 'AR', Argelia: 'DZ', Alemania: 'DE', Australia: 'AU', Austria: 'AT',
  Belgica: 'BE', Brasil: 'BR', Canada: 'CA', Catar: 'QA', Colombia: 'CO',
  'Corea del Sur': 'KR', Croacia: 'HR', Ecuador: 'EC', Egipto: 'EG', Escocia: 'GB',
  Espana: 'ES', Francia: 'FR', Ghana: 'GH', Haiti: 'HT', Inglaterra: 'GB',
  Iran: 'IR', Iraq: 'IQ', Japon: 'JP', Jordania: 'JO', Marruecos: 'MA',
  Mexico: 'MX', Noruega: 'NO', 'Nueva Zelanda': 'NZ', Panama: 'PA', Paraguay: 'PY',
  'Paises Bajos': 'NL', Portugal: 'PT', 'RD Congo': 'CD', 'Republica Checa': 'CZ',
  Senegal: 'SN', Sudafrica: 'ZA', Suecia: 'SE', Suiza: 'CH', Tunez: 'TN',
  Turquia: 'TR', Uruguay: 'UY', Uzbekistan: 'UZ', 'Costa de Marfil': 'CI',
  'Estados Unidos': 'US', 'Arabia Saudita': 'SA', 'Bosnia y Herzegovina': 'BA',
  'Cabo Verde': 'CV', Curazao: 'CW'
};

const teamCodeMap = {
  Argentina: 'ARG', Argelia: 'ALG', Alemania: 'GER', Australia: 'AUS', Austria: 'AUT',
  Belgica: 'BEL', Brasil: 'BRA', Canada: 'CAN', Catar: 'QAT', Colombia: 'COL',
  'Corea del Sur': 'KOR', Croacia: 'CRO', Ecuador: 'ECU', Egipto: 'EGY', Escocia: 'SCO',
  Espana: 'ESP', Francia: 'FRA', Ghana: 'GHA', Haiti: 'HAI', Inglaterra: 'ENG',
  Iran: 'IRN', Iraq: 'IRQ', Japon: 'JPN', Jordania: 'JOR', Marruecos: 'MAR',
  Mexico: 'MEX', Noruega: 'NOR', 'Nueva Zelanda': 'NZL', Panama: 'PAN', Paraguay: 'PAR',
  'Paises Bajos': 'NED', Portugal: 'POR', 'RD Congo': 'COD', 'Republica Checa': 'CZE',
  Chequia: 'CZE', Senegal: 'SEN', Sudafrica: 'RSA', Suecia: 'SWE', Suiza: 'SUI', Tunez: 'TUN',
  Turquia: 'TUR', Uruguay: 'URU', Uzbekistan: 'UZB', 'Costa de Marfil': 'CIV',
  'Estados Unidos': 'USA', 'Arabia Saudita': 'KSA', 'Bosnia y Herzegovina': 'BIH',
  'Cabo Verde': 'CPV', Curazao: 'CUW'
};

const state = {
  groups: [],
  activeParticipant: null,
  participantPredictions: [],
  selectedGroupId: null,
  activeMatch: null,
  modalScoreA: 0,
  modalScoreB: 0
};

async function initPredictionsPage() {
  bindPredictionEvents();
  CoronaUi.startCountdown(window.CORONA_CONFIG.mundialStartDate, document.querySelector('[data-countdown]'));

  try {
    const matches = await CoronaApi.listMatches();
    state.groups = buildGroupsFromMatches(matches);
    if (!state.groups.length) {
      state.groups = buildMockGroups(matches);
    }
  } catch (error) {
    state.groups = buildMockGroups([]);
    showPredictionMessage('warning', 'Usando datos de ejemplo porque no se pudo cargar el fixture.');
  }

  if (!state.groups.length) {
    state.groups = buildMockGroups([]);
  }

  renderEmailValidationView();
}

function bindPredictionEvents() {
  document.querySelector('#participantValidationForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    const email = document.querySelector('#participantEmailInput').value.trim();
    await validateParticipantEmail(email);
  });
  document.querySelector('#backToGroupsButton').addEventListener('click', goBackToGroups);
  document.querySelector('#cancelGroupButton').addEventListener('click', goBackToGroups);
  document.querySelector('#autoPredictionButton').addEventListener('click', applyAutoPrediction);
  document.querySelector('#saveGroupButton').addEventListener('click', () => saveGroupPrediction(state.selectedGroupId));
  document.querySelector('#cancelMatchButton').addEventListener('click', cancelMatchPrediction);
  document.querySelector('#clearMatchButton').addEventListener('click', clearMatchPrediction);
  document.querySelector('#saveMatchButton').addEventListener('click', saveMatchPrediction);
  document.querySelectorAll('[data-score-action]').forEach((button) => {
    button.addEventListener('click', () => updateModalScore(button.dataset.side, button.dataset.scoreAction));
  });
  document.querySelector('#matchPredictionModal').addEventListener('click', (event) => {
    if (event.target.id === 'matchPredictionModal') cancelMatchPrediction();
  });
}

function renderEmailValidationView() {
  document.querySelector('#emailValidationView').classList.remove('d-none');
  document.querySelector('#groupsView').classList.add('d-none');
  document.querySelector('#groupPredictionView').classList.add('d-none');
  document.querySelector('#participantEmailInput').focus();
}

async function validateParticipantEmail(email) {
  const message = document.querySelector('#participantValidationMessage');
  CoronaUi.clearMessage(message);

  if (!email) {
    CoronaUi.setMessage(message, 'warning', 'Ingresa tu email registrado.');
    return;
  }
  if (!CoronaUi.isEmail(email)) {
    CoronaUi.setMessage(message, 'warning', 'Ingresa un email valido.');
    return;
  }

  const button = document.querySelector('#validateParticipantButton');
  button.disabled = true;
  button.textContent = 'Validando...';
  try {
    const participant = await CoronaApi.getParticipantByEmail({ email });
    setActiveParticipant(participant);
    await loadParticipantPredictions(participant);
    CoronaUi.setMessage(message, 'success', 'Participante encontrado.');
    renderGroupsView();
  } catch (error) {
    clearActiveParticipant();
    CoronaUi.setMessage(message, 'danger', `${error.message} Verifica el correo o registrate antes de predecir.`);
  } finally {
    button.disabled = false;
    button.textContent = 'Continuar';
  }
}

function setActiveParticipant(participant) {
  state.activeParticipant = {
    participante_id: participant.participante_id,
    nombre_apellido: participant.nombre_apellido,
    email: participant.email,
    area: participant.area
  };
}

function clearActiveParticipant() {
  state.activeParticipant = null;
  state.participantPredictions = [];
  state.groups.forEach((group) => {
    group.matches.forEach((match) => { match.prediction = null; });
  });
}

async function loadParticipantPredictions(participant) {
  state.participantPredictions = [];
  state.groups.forEach((group) => {
    group.matches.forEach((match) => { match.prediction = null; });
  });

  try {
    const predictions = await CoronaApi.getPredictionsByParticipant({ email: participant.email });
    state.participantPredictions = predictions || [];
    applyParticipantPredictions(state.participantPredictions);
  } catch (error) {
    showPredictionMessage('warning', 'No se pudieron cargar predicciones previas. Podes continuar y guardar nuevas predicciones.');
  }
}

function applyParticipantPredictions(predictions) {
  predictions.forEach((prediction) => {
    state.groups.forEach((group) => {
      const match = group.matches.find((item) => item.backendId === prediction.partido_id);
      if (match) {
        match.prediction = {
          goalsA: Number(prediction.goles_a_predicho),
          goalsB: Number(prediction.goles_b_predicho)
        };
      }
    });
  });
}

function renderGroupsView() {
  if (!state.activeParticipant) {
    renderEmailValidationView();
    return;
  }
  document.querySelector('#emailValidationView').classList.add('d-none');
  document.querySelector('#groupsView').classList.remove('d-none');
  document.querySelector('#groupPredictionView').classList.add('d-none');
  renderActiveParticipantSummary();
  renderGroupCards();
  updateGroupsProgress();
}

function renderActiveParticipantSummary() {
  const html = `
    <span class="participant-active-badge"><i class="bi bi-person-check"></i>Prediciendo como</span>
    <strong>${state.activeParticipant.nombre_apellido}</strong>
    <span class="text-muted">Area: ${state.activeParticipant.area}</span>
    <button class="change-participant-link" id="changeParticipantButton" type="button">Cambiar email</button>
  `;
  document.querySelector('#activeParticipantSummary').innerHTML = html;
  document.querySelector('#changeParticipantButton').addEventListener('click', () => {
    clearActiveParticipant();
    renderEmailValidationView();
  });
}

function renderGroupCards() {
  const grid = document.querySelector('#groupsGrid');
  grid.innerHTML = state.groups.map((group) => {
    const table = recalculateGroupTable(group.id);
    const completed = isGroupComplete(group);
    return `
      <button class="group-card ${completed ? 'predicted' : ''}" style="--group-color:${group.color}" data-group-id="${group.id}" type="button">
        <span class="group-card-header">
          <span class="group-badge"><i class="bi bi-flag-fill me-1"></i>${group.id}</span>
          <span class="group-status ${completed ? '' : 'pending'}">${completed ? '✓ Predicho' : 'Pendiente'}</span>
        </span>
        ${table.map((team, index) => `
          <span class="team-position-row">
            <span class="team-position">${index + 1}°</span>
            ${CoronaUi.formatTeamFlag(team.name)}
            <span class="team-name fw-semibold">${team.name}</span>
            <i class="bi ${index < 2 ? 'bi-arrow-up-circle-fill text-info' : index === 2 ? 'bi-star-fill text-warning' : 'bi-x-circle text-muted'} ms-auto team-row-icon" aria-hidden="true"></i>
          </span>
        `).join('')}
      </button>
    `;
  }).join('');

  grid.querySelectorAll('[data-group-id]').forEach((card) => {
    card.addEventListener('click', () => openGroupPrediction(card.dataset.groupId));
  });
}

function openGroupPrediction(groupId) {
  if (!state.activeParticipant) {
    renderEmailValidationView();
    return;
  }
  state.selectedGroupId = groupId;
  document.querySelector('#groupsView').classList.add('d-none');
  document.querySelector('#groupPredictionView').classList.remove('d-none');
  renderGroupPrediction(groupId);
}

function renderGroupPrediction(groupId) {
  const group = getGroup(groupId);
  document.querySelector('#groupDetailBadge').textContent = group.id;
  document.querySelector('#groupDetailBadge').style.setProperty('--group-color', group.color);
  document.querySelector('#groupDetailTitle').textContent = `Grupo ${group.id}`;
  renderParticipantContextMini();
  renderFixtureMatrix(groupId);
  renderResultingTable(groupId);
  updateGroupProgress(groupId);
}

function renderParticipantContextMini() {
  document.querySelector('#participantContextMini').innerHTML = `
    <span class="participant-active-badge"><i class="bi bi-person"></i>${state.activeParticipant.nombre_apellido}</span>
    <span class="text-muted">${state.activeParticipant.area}</span>
  `;
}

function renderFixtureMatrix(groupId) {
  const group = getGroup(groupId);
  const matrix = document.querySelector('#fixtureMatrix');
  const header = ['<div></div>'].concat(group.teams.map((team) => `<div class="matrix-head">${CoronaUi.formatTeamName(team.name, { code: true })}</div>`)).join('');

  const rows = group.teams.map((rowTeam) => {
    const cells = group.teams.map((colTeam) => {
      if (rowTeam.id === colTeam.id) {
        return '<button class="match-cell match-cell-disabled" type="button" disabled>—</button>';
      }
      const match = findMatch(group, rowTeam.id, colTeam.id);
      if (!match || match.homeId !== rowTeam.id) {
        return '<button class="match-cell match-cell-disabled" type="button" disabled>—</button>';
      }
      const prediction = match.prediction;
      const label = prediction ? `${prediction.goalsA} - ${prediction.goalsB}` : '—';
      return `
        <button class="match-cell ${cellClass(prediction)}" data-match-id="${match.id}" type="button" aria-label="${rowTeam.name} vs ${colTeam.name}: ${label}">
          <span class="match-cell-flags" aria-hidden="true">
            ${CoronaUi.formatTeamFlag(rowTeam.name)}
            <span class="match-cell-vs">vs</span>
            ${CoronaUi.formatTeamFlag(colTeam.name)}
          </span>
          <span class="match-cell-score">${label}</span>
        </button>
      `;
    }).join('');
    return `<div class="matrix-team">${CoronaUi.formatTeamName(rowTeam.name, { code: true })}</div>${cells}`;
  }).join('');

  matrix.innerHTML = header + rows;
  matrix.querySelectorAll('[data-match-id]').forEach((cell) => {
    cell.addEventListener('click', () => {
      const match = group.matches.find((item) => item.id === cell.dataset.matchId);
      openMatchPrediction(groupId, match.homeId, match.awayId);
    });
  });
}

function openMatchPrediction(groupId, teamAId, teamBId) {
  const group = getGroup(groupId);
  const match = findMatch(group, teamAId, teamBId);
  const teamA = group.teams.find((team) => team.id === teamAId);
  const teamB = group.teams.find((team) => team.id === teamBId);
  state.activeMatch = { groupId, matchId: match.id };
  state.modalScoreA = match.prediction ? match.prediction.goalsA : 0;
  state.modalScoreB = match.prediction ? match.prediction.goalsB : 0;

  document.querySelector('#modalTeamAFlag').innerHTML = CoronaUi.formatTeamFlag(teamA.name, { size: 'lg' });
  document.querySelector('#modalTeamAName').textContent = teamA.name;
  document.querySelector('#modalTeamBFlag').innerHTML = CoronaUi.formatTeamFlag(teamB.name, { size: 'lg' });
  document.querySelector('#modalTeamBName').textContent = teamB.name;
  renderModalScores();
  document.querySelector('#matchPredictionModal').classList.remove('d-none');
}

function saveMatchPrediction() {
  const group = getGroup(state.activeMatch.groupId);
  const match = group.matches.find((item) => item.id === state.activeMatch.matchId);
  match.prediction = { goalsA: state.modalScoreA, goalsB: state.modalScoreB };
  closeMatchModal();
  renderGroupPrediction(group.id);
}

function clearMatchPrediction() {
  const group = getGroup(state.activeMatch.groupId);
  const match = group.matches.find((item) => item.id === state.activeMatch.matchId);
  match.prediction = null;
  closeMatchModal();
  renderGroupPrediction(group.id);
}

function cancelMatchPrediction() {
  closeMatchModal();
}

function closeMatchModal() {
  state.activeMatch = null;
  document.querySelector('#matchPredictionModal').classList.add('d-none');
}

function recalculateGroupTable(groupId) {
  const group = getGroup(groupId);
  const table = group.teams.map((team) => ({
    ...team,
    pj: 0, g: 0, e: 0, p: 0, gf: 0, gc: 0, dg: 0, pts: 0
  }));

  group.matches.forEach((match) => {
    if (!match.prediction) return;
    const home = table.find((team) => team.id === match.homeId);
    const away = table.find((team) => team.id === match.awayId);
    const goalsA = match.prediction.goalsA;
    const goalsB = match.prediction.goalsB;
    home.pj += 1; away.pj += 1;
    home.gf += goalsA; home.gc += goalsB;
    away.gf += goalsB; away.gc += goalsA;
    if (goalsA > goalsB) {
      home.g += 1; home.pts += 3; away.p += 1;
    } else if (goalsA < goalsB) {
      away.g += 1; away.pts += 3; home.p += 1;
    } else {
      home.e += 1; away.e += 1; home.pts += 1; away.pts += 1;
    }
  });

  table.forEach((team) => { team.dg = team.gf - team.gc; });
  return table.sort((a, b) => b.pts - a.pts || b.dg - a.dg || b.gf - a.gf || a.name.localeCompare(b.name));
}

function updateGroupProgress(groupId) {
  const group = getGroup(groupId);
  const completed = completedMatches(group);
  const percent = Math.round((completed / 6) * 100);
  document.querySelector('#groupMatchProgressText').textContent = `${completed} / 6 partidos`;
  document.querySelector('#groupMatchProgressBar').style.width = `${percent}%`;
  document.querySelector('#groupCompletionBadge').textContent = completed === 6 ? '✓ Predicho' : 'Pendiente';
  document.querySelector('#groupCompletionBadge').className = completed === 6 ? 'badge text-bg-success' : 'badge text-bg-light';
}

async function saveGroupPrediction(groupId) {
  if (!state.activeParticipant) {
    showPredictionMessage('warning', 'Primero valida tu email registrado.');
    renderEmailValidationView();
    return;
  }

  const group = getGroup(groupId);
  if (!isGroupComplete(group)) {
    showPredictionMessage('warning', 'Completa los 6 partidos antes de guardar el grupo.');
    return;
  }

  const button = document.querySelector('#saveGroupButton');
  button.disabled = true;
  button.textContent = 'Guardando...';
  try {
    const payload = buildPredictionPayload(groupId);
    for (const match of group.matches) {
      const matchPayload = payload.predicciones_partidos.find((item) => item.partido_id === match.backendId);
      await CoronaApi.savePrediction({
        participante_id: payload.participante_id,
        nombre_apellido: payload.nombre_apellido,
        email: payload.email,
        area: payload.area,
        grupo_id: payload.grupo_id,
        partido_id: matchPayload.partido_id,
        ganador_predicho: matchPayload.ganador_predicho,
        goles_a_predicho: matchPayload.goles_a,
        goles_b_predicho: matchPayload.goles_b
      });
    }
    await loadParticipantPredictions(state.activeParticipant);
    showPredictionMessage('success', `Grupo ${group.id} guardado correctamente.`);
    renderGroupsView();
  } catch (error) {
    showPredictionMessage('danger', error.message);
  } finally {
    button.disabled = false;
    button.textContent = 'Guardar prediccion';
  }
}

function buildPredictionPayload(groupId) {
  const group = getGroup(groupId);
  const table = recalculateGroupTable(groupId);
  return {
    participante_id: state.activeParticipant.participante_id,
    nombre_apellido: state.activeParticipant.nombre_apellido,
    email: state.activeParticipant.email,
    area: state.activeParticipant.area,
    grupo_id: group.id,
    grupo_nombre: `Grupo ${group.id}`,
    predicciones_partidos: group.matches.map((match) => {
      const teamA = group.teams.find((team) => team.id === match.homeId);
      const teamB = group.teams.find((team) => team.id === match.awayId);
      return {
        partido_id: match.backendId,
        equipo_a: teamA.name,
        equipo_b: teamB.name,
        goles_a: match.prediction.goalsA,
        goles_b: match.prediction.goalsB,
        ganador_predicho: winnerName(match.prediction, teamA, teamB),
        es_empate_predicho: match.prediction.goalsA === match.prediction.goalsB
      };
    }),
    tabla_calculada: table.map((team, index) => ({
      posicion: index + 1,
      equipo: team.name,
      pj: team.pj,
      g: team.g,
      e: team.e,
      p: team.p,
      gf: team.gf,
      gc: team.gc,
      dg: team.dg,
      pts: team.pts
    })),
    estado: isGroupComplete(group) ? 'completa' : 'parcial',
    fecha_guardado: new Date().toISOString()
  };
}

function goBackToGroups() {
  state.selectedGroupId = null;
  renderGroupsView();
}

function applyAutoPrediction() {
  const group = getGroup(state.selectedGroupId);
  group.matches.forEach((match) => {
    if (!match.prediction) {
      match.prediction = {
        goalsA: Math.floor(Math.random() * 3),
        goalsB: Math.floor(Math.random() * 3)
      };
    }
  });
  renderGroupPrediction(group.id);
}

function renderResultingTable(groupId) {
  const body = document.querySelector('#resultingTableBody');
  body.innerHTML = recalculateGroupTable(groupId).map((team, index) => `
    <tr>
      <td>${index + 1}</td>
      <td>${CoronaUi.formatTeamName(team.name)}</td>
      <td>${team.pj}</td><td>${team.g}</td><td>${team.e}</td><td>${team.p}</td>
      <td>${team.gf}</td><td>${team.gc}</td><td>${team.dg > 0 ? '+' : ''}${team.dg}</td><td><strong>${team.pts}</strong></td>
    </tr>
  `).join('');
}

function updateGroupsProgress() {
  const completed = state.groups.filter(isGroupComplete).length;
  document.querySelector('#groupsProgressText').textContent = `${completed} / ${state.groups.length} grupos completos`;
  document.querySelector('#groupsProgressBar').style.width = `${Math.round((completed / state.groups.length) * 100)}%`;
}

function updateModalScore(side, action) {
  const prop = side === 'a' ? 'modalScoreA' : 'modalScoreB';
  state[prop] = action === 'plus' ? state[prop] + 1 : Math.max(0, state[prop] - 1);
  renderModalScores();
}

function renderModalScores() {
  document.querySelector('#modalScoreA').textContent = state.modalScoreA;
  document.querySelector('#modalScoreB').textContent = state.modalScoreB;
}

function buildGroupsFromMatches(matches) {
  const groupMatches = matches.filter((match) => /^[A-L]$/.test(extractGroupId(match.fase)));
  const byGroup = {};
  groupMatches.forEach((match) => {
    const groupId = extractGroupId(match.fase);
    byGroup[groupId] = byGroup[groupId] || [];
    byGroup[groupId].push(match);
  });

  return Object.keys(byGroup).sort().map((groupId, index) => {
    const names = uniqueTeams(byGroup[groupId]);
    return buildGroup(groupId, names, byGroup[groupId], index);
  }).filter((group) => group.teams.length === 4 && group.matches.length === 6);
}

function buildMockGroups(backendMatches) {
  backendMatches = backendMatches || [];
  const namesByGroup = {
    A: ['Mexico', 'Sudafrica', 'Corea del Sur', 'Republica Checa'],
    B: ['Canada', 'Bosnia y Herzegovina', 'Catar', 'Suiza'],
    C: ['Brasil', 'Marruecos', 'Haiti', 'Escocia'],
    D: ['Estados Unidos', 'Paraguay', 'Australia', 'Turquia'],
    E: ['Alemania', 'Curazao', 'Costa de Marfil', 'Ecuador'],
    F: ['Paises Bajos', 'Japon', 'Suecia', 'Tunez'],
    G: ['Belgica', 'Egipto', 'Iran', 'Nueva Zelanda'],
    H: ['Espana', 'Cabo Verde', 'Arabia Saudita', 'Uruguay'],
    I: ['Francia', 'Senegal', 'Iraq', 'Noruega'],
    J: ['Argentina', 'Argelia', 'Austria', 'Jordania'],
    K: ['Portugal', 'RD Congo', 'Uzbekistan', 'Colombia'],
    L: ['Inglaterra', 'Croacia', 'Ghana', 'Panama']
  };
  return Object.keys(namesByGroup).map((groupId, index) => buildGroup(groupId, namesByGroup[groupId], backendMatches, index));
}

function buildGroup(groupId, teamNames, backendMatches, index) {
  const teams = teamNames.map((name) => ({
    id: slug(name),
    code: teamCode(name),
    name,
    flag: countryFlag(name)
  }));
  const matches = [];
  for (let i = 0; i < teams.length; i += 1) {
    for (let j = i + 1; j < teams.length; j += 1) {
      const backend = findBackendMatch(backendMatches, teams[i].name, teams[j].name);
      matches.push({
        id: `${groupId}_${teams[i].id}_${teams[j].id}`,
        backendId: backend ? backend.partido_id : `mock_${groupId}_${i}_${j}`,
        homeId: teams[i].id,
        awayId: teams[j].id,
        prediction: null
      });
    }
  }
  return { id: groupId, color: groupColors[index % groupColors.length], teams, matches };
}

function findBackendMatch(matches, nameA, nameB) {
  return matches.find((match) => {
    const pair = [match.equipo_a, match.equipo_b].map(normalizeTeamName);
    return pair.includes(normalizeTeamName(nameA)) && pair.includes(normalizeTeamName(nameB));
  });
}

function uniqueTeams(matches) {
  const names = [];
  matches.forEach((match) => {
    [match.equipo_a, match.equipo_b].forEach((name) => {
      if (name && !names.some((item) => normalizeTeamName(item) === normalizeTeamName(name))) {
        names.push(name);
      }
    });
  });
  return names.slice(0, 4);
}

function extractGroupId(fase) {
  const text = String(fase || '');
  const direct = text.match(/^([A-L])$/);
  if (direct) return direct[1];
  const group = text.match(/grupo\s*([A-L])/i);
  return group ? group[1].toUpperCase() : '';
}

function getGroup(groupId) {
  return state.groups.find((group) => group.id === groupId);
}

function findMatch(group, teamAId, teamBId) {
  return group.matches.find((match) => (match.homeId === teamAId && match.awayId === teamBId) || (match.homeId === teamBId && match.awayId === teamAId));
}

function completedMatches(group) {
  return group.matches.filter((match) => match.prediction).length;
}

function isGroupComplete(group) {
  return completedMatches(group) === 6;
}

function cellClass(prediction) {
  if (!prediction) return '';
  if (prediction.goalsA > prediction.goalsB) return 'match-cell-win';
  if (prediction.goalsA < prediction.goalsB) return 'match-cell-loss';
  return 'match-cell-draw';
}

function winnerName(prediction, teamA, teamB) {
  if (prediction.goalsA === prediction.goalsB) return 'Empate';
  return prediction.goalsA > prediction.goalsB ? teamA.name : teamB.name;
}

function showPredictionMessage(type, message) {
  const status = document.querySelector('#prediccionStatus');
  CoronaUi.setMessage(status, type, message);
  window.setTimeout(() => CoronaUi.clearMessage(status), 4500);
}

function normalizeTeamName(name) {
  return String(name || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
}

function slug(name) {
  return normalizeTeamName(name).replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
}

function teamCode(name) {
  return CoronaUi.getTeamCode(name);
}

function countryFlag(name) {
  return CoronaUi.getTeamFlag(name);
}

function lookupByTeamName(map, name) {
  const direct = map[name];
  if (direct) return direct;
  const normalizedName = normalizeTeamName(name);
  const key = Object.keys(map).find((item) => normalizeTeamName(item) === normalizedName);
  return key ? map[key] : '';
}
