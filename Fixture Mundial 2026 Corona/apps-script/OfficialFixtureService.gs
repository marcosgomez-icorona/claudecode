function syncOfficialFixture(payload) {
  payload = payload || {};
  if (payload.admin_key) {
    assertAdmin(payload);
  }

  const config = getConfigMap();
  if (config.fixture_api_url && config.fixture_sync_enabled !== 'true' && !payload.force) {
    appendSyncLog('omitido', 0, 0, 'Sincronizacion deshabilitada por configuracion.');
    return { skipped: true, message: 'Sincronizacion deshabilitada.' };
  }

  try {
    const data = config.fixture_api_url ? fetchOfficialFixture(config) : getLocalOfficialFixtureData();
    const teamsUpdated = replaceOfficialTeams(data.teams || []);
    const matchResult = replaceOfficialMatches(data.matches || []);
    appendSyncLog('ok', matchResult.matches, matchResult.results, 'Equipos: ' + teamsUpdated);
    return {
      teams: teamsUpdated,
      matches: matchResult.matches,
      results: matchResult.results
    };
  } catch (error) {
    appendSyncLog('error', 0, 0, error.message);
    throw error;
  }
}

function fetchOfficialFixture(config) {
  const url = addQueryParams(config.fixture_api_url, {
    lang: config.fixture_api_language || 'es'
  });
  const headers = {};
  if (config.fixture_api_token) {
    headers.Authorization = 'Bearer ' + config.fixture_api_token;
  }

  const response = UrlFetchApp.fetch(url, {
    method: 'get',
    headers: headers,
    muteHttpExceptions: true
  });
  const status = response.getResponseCode();
  const body = response.getContentText();
  if (status < 200 || status >= 300) {
    throw new Error('API fixture respondio HTTP ' + status + ': ' + body.substring(0, 180));
  }

  const data = JSON.parse(body);
  if (!data.matches || !Array.isArray(data.matches)) {
    throw new Error('Respuesta invalida: falta array matches.');
  }
  return data;
}

function addQueryParams(url, params) {
  const query = Object.keys(params)
    .filter(function (key) { return params[key] !== undefined && params[key] !== ''; })
    .map(function (key) { return encodeURIComponent(key) + '=' + encodeURIComponent(params[key]); })
    .join('&');
  if (!query) return url;
  return url + (url.indexOf('?') === -1 ? '?' : '&') + query;
}

function upsertTeams(teams) {
  const existing = readRows(SHEETS.teams);
  let count = 0;
  teams.forEach(function (team) {
    const code = String(team.code || team.codigo || '').trim();
    if (!code) return;
    const row = {
      codigo: code,
      nombre_api: team.name || team.nombre_api || team.nombre || '',
      nombre_es: team.name_es || team.nombre_es || team.nombre || team.name || '',
      grupo: team.group || team.grupo || '',
      fecha_actualizacion: nowIso()
    };
    const current = existing.find(function (item) { return String(item.codigo) === code; });
    if (current) {
      updateRow(SHEETS.teams, current._rowNumber, row);
    } else {
      appendRow(SHEETS.teams, row);
      existing.push(row);
    }
    count += 1;
  });
  return count;
}

function replaceOfficialTeams(teams) {
  const rows = [];
  teams.forEach(function (team) {
    const code = String(team.code || team.codigo || '').trim();
    if (!code) return;
    rows.push({
      codigo: code,
      nombre_api: team.name || team.nombre_api || team.nombre || '',
      nombre_es: team.name_es || team.nombre_es || team.nombre || team.name || '',
      grupo: team.group || team.grupo || '',
      fecha_actualizacion: nowIso()
    });
  });
  replaceRows(SHEETS.teams, rows);
  return rows.length;
}

function upsertOfficialMatches(matches) {
  const existingMatches = readRows(SHEETS.matches);
  const existingResults = readRows(SHEETS.results);
  let matchesUpdated = 0;
  let resultsUpdated = 0;

  matches.forEach(function (item) {
    const partidoId = String(item.match_id || item.partido_id || '').trim();
    if (!partidoId) return;

    const match = normalizeOfficialMatch(item, partidoId);
    const currentMatch = existingMatches.find(function (row) { return String(row.partido_id) === partidoId; });
    if (currentMatch) {
      updateRow(SHEETS.matches, currentMatch._rowNumber, match);
    } else {
      appendRow(SHEETS.matches, match);
      existingMatches.push(match);
    }
    matchesUpdated += 1;

    if (item.status === 'finished' || item.estado === 'finalizado') {
      const result = normalizeOfficialResult(item, partidoId);
      const currentResult = existingResults.find(function (row) { return String(row.partido_id) === partidoId; });
      if (currentResult) {
        updateRow(SHEETS.results, currentResult._rowNumber, result);
      } else {
        appendRow(SHEETS.results, result);
        existingResults.push(result);
      }
      resultsUpdated += 1;
    }
  });

  if (resultsUpdated > 0) {
    rebuildScoresWithoutAdmin();
  }

  return { matches: matchesUpdated, results: resultsUpdated };
}

function replaceOfficialMatches(matches) {
  const matchRows = [];
  const resultRows = readRows(SHEETS.results);
  let resultsUpdated = 0;

  matches.forEach(function (item) {
    const partidoId = String(item.match_id || item.partido_id || '').trim();
    if (!partidoId) return;
    matchRows.push(normalizeOfficialMatch(item, partidoId));

    if (item.status === 'finished' || item.estado === 'finalizado') {
      upsertResultInMemory(resultRows, normalizeOfficialResult(item, partidoId));
      resultsUpdated += 1;
    }
  });

  replaceRows(SHEETS.matches, matchRows);

  if (resultsUpdated > 0) {
    replaceRows(SHEETS.results, resultRows);
    rebuildScoresWithoutAdmin();
  }

  return { matches: matchRows.length, results: resultsUpdated };
}

function upsertResultInMemory(rows, result) {
  const index = rows.findIndex(function (row) {
    return String(row.partido_id) === String(result.partido_id);
  });
  if (index >= 0) {
    rows[index] = result;
  } else {
    rows.push(result);
  }
}

function normalizeOfficialMatch(item, partidoId) {
  const teamA = item.team_a || item.equipo_a || {};
  const teamB = item.team_b || item.equipo_b || {};
  const startTime = item.start_time || item.fecha_partido;
  const group = item.group || item.grupo || '';
  return {
    partido_id: partidoId,
    fase: group ? 'Grupo ' + group : (item.stage_es || item.fase || item.stage || 'Grupo'),
    equipo_a: teamNameEs(teamA),
    equipo_b: teamNameEs(teamB),
    fecha_partido: toIso(startTime),
    fecha_limite_prediccion: toIso(item.prediction_deadline || item.fecha_limite_prediccion || startTime),
    estado: mapOfficialStatus(item.status || item.estado)
  };
}

function normalizeOfficialResult(item, partidoId) {
  const goalsA = Number(item.score_a !== undefined ? item.score_a : item.goles_a_real);
  const goalsB = Number(item.score_b !== undefined ? item.score_b : item.goles_b_real);
  const teamA = teamNameEs(item.team_a || item.equipo_a || {});
  const teamB = teamNameEs(item.team_b || item.equipo_b || {});
  return {
    resultado_id: makeId('res'),
    partido_id: partidoId,
    goles_a_real: goalsA,
    goles_b_real: goalsB,
    ganador_real: goalsA === goalsB ? 'Empate' : (goalsA > goalsB ? teamA : teamB),
    es_empate_real: goalsA === goalsB,
    fecha_carga: nowIso(),
    cargado_por: 'sync-api-oficial'
  };
}

function teamNameEs(team) {
  if (typeof team === 'string') return team;
  return team.name_es || team.nombre_es || team.name || team.nombre || '';
}

function mapOfficialStatus(status) {
  const value = String(status || '').toLowerCase();
  if (value === 'finished' || value === 'finalizado') return 'finalizado';
  if (value === 'live' || value === 'en_juego') return 'en_juego';
  if (value === 'postponed' || value === 'suspendido') return 'suspendido';
  return 'programado';
}

function rebuildScoresWithoutAdmin() {
  const participants = readRows(SHEETS.participants);
  const predictions = readRows(SHEETS.predictions).filter(function (item) { return item.estado === 'vigente'; });
  const results = readRows(SHEETS.results);
  const now = nowIso();
  const scoreRows = [];

  predictions.forEach(function (prediction) {
    const result = results.find(function (item) { return item.partido_id === prediction.partido_id; });
    if (!result) return;
    const points = calculatePredictionPoints(prediction, result);
    scoreRows.push({
      puntaje_id: makeId('sco'),
      participante_id: prediction.participante_id,
      partido_id: prediction.partido_id,
      puntos_resultado: points.total,
      puntos_extra: 0,
      puntos_total: points.total,
      fecha_calculo: now
    });
  });

  replaceRows(SHEETS.scores, scoreRows);
  rebuildRankings(participants, predictions, results, scoreRows, now);
}

function appendSyncLog(status, matches, results, message) {
  appendRow(SHEETS.syncLog, {
    sync_id: makeId('syn'),
    proveedor: getConfigMap().fixture_api_provider || 'api-propia',
    estado: status,
    partidos_actualizados: matches,
    resultados_actualizados: results,
    mensaje: message,
    fecha_sync: nowIso()
  });
}
