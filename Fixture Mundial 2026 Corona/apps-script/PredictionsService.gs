function savePrediction(payload) {
  requireFields(payload, ['email', 'partido_id', 'ganador_predicho', 'goles_a_predicho', 'goles_b_predicho']);
  const participant = findParticipantByEmail(payload.email);
  if (!participant) {
    throw new Error('No existe participante activo con ese email.');
  }

  const match = findMatch(payload.partido_id);
  if (!match) {
    throw new Error('Partido no encontrado.');
  }

  if (new Date(match.fecha_limite_prediccion).getTime() < Date.now()) {
    throw new Error('La prediccion para este partido ya esta cerrada.');
  }

  const goalsA = Number(payload.goles_a_predicho);
  const goalsB = Number(payload.goles_b_predicho);
  if (goalsA < 0 || goalsB < 0 || isNaN(goalsA) || isNaN(goalsB)) {
    throw new Error('Los goles predichos deben ser numeros validos.');
  }

  const rows = readRows(SHEETS.predictions);
  const existing = rows.find(function (item) {
    return item.participante_id === participant.participante_id && item.partido_id === match.partido_id && item.estado === 'vigente';
  });
  const currentTime = nowIso();
  const prediction = {
    prediccion_id: existing ? existing.prediccion_id : makeId('pre'),
    participante_id: participant.participante_id,
    partido_id: match.partido_id,
    email: normalizeEmail(payload.email),
    ganador_predicho: String(payload.ganador_predicho),
    goles_a_predicho: goalsA,
    goles_b_predicho: goalsB,
    es_empate_predicho: goalsA === goalsB,
    fecha_prediccion: existing ? existing.fecha_prediccion : currentTime,
    fecha_actualizacion: currentTime,
    estado: 'vigente'
  };

  if (existing) {
    updateRow(SHEETS.predictions, existing._rowNumber, prediction);
  } else {
    appendRow(SHEETS.predictions, prediction);
  }
  return prediction;
}

function getPredictionsByParticipant(payload) {
  requireFields(payload, ['email']);
  const participant = findParticipantByEmail(payload.email);
  if (!participant) {
    throw new Error('No encontramos un participante registrado con ese email.');
  }
  return readRows(SHEETS.predictions).filter(function (prediction) {
    return normalizeEmail(prediction.email) === normalizeEmail(payload.email) &&
      prediction.participante_id === participant.participante_id &&
      prediction.estado === 'vigente';
  });
}
