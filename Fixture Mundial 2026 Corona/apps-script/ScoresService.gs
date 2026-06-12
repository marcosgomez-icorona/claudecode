function saveResult(payload) {
  assertAdmin(payload);
  requireFields(payload, ['partido_id', 'goles_a_real', 'goles_b_real']);

  const match = findMatch(payload.partido_id);
  if (!match) {
    throw new Error('Partido no encontrado.');
  }

  const goalsA = Number(payload.goles_a_real);
  const goalsB = Number(payload.goles_b_real);
  if (goalsA < 0 || goalsB < 0 || isNaN(goalsA) || isNaN(goalsB)) {
    throw new Error('Resultado invalido.');
  }

  const winner = goalsA === goalsB ? 'Empate' : (goalsA > goalsB ? match.equipo_a : match.equipo_b);
  const rows = readRows(SHEETS.results);
  const existing = rows.find(function (item) { return item.partido_id === match.partido_id; });
  const result = {
    resultado_id: existing ? existing.resultado_id : makeId('res'),
    partido_id: match.partido_id,
    goles_a_real: goalsA,
    goles_b_real: goalsB,
    ganador_real: winner,
    es_empate_real: goalsA === goalsB,
    fecha_carga: nowIso(),
    cargado_por: payload.cargado_por || 'Admin'
  };

  if (existing) {
    updateRow(SHEETS.results, existing._rowNumber, result);
  } else {
    appendRow(SHEETS.results, result);
  }

  recalculateScores(payload);
  return result;
}

function recalculateScores(payload) {
  assertAdmin(payload);
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
  return { scores: scoreRows.length };
}

function calculatePredictionPoints(prediction, result) {
  const predA = Number(prediction.goles_a_predicho);
  const predB = Number(prediction.goles_b_predicho);
  const realA = Number(result.goles_a_real);
  const realB = Number(result.goles_b_real);
  const exact = predA === realA && predB === realB;
  if (exact) {
    return { total: 5, exact: true, winner: true };
  }

  const predictedDraw = predA === predB || String(prediction.ganador_predicho) === 'Empate';
  const realDraw = realA === realB || String(result.ganador_real) === 'Empate';
  if (predictedDraw && realDraw) {
    return { total: 2, exact: false, winner: false };
  }

  if (String(prediction.ganador_predicho) === String(result.ganador_real)) {
    return { total: 2, exact: false, winner: true };
  }

  return { total: 0, exact: false, winner: false };
}
