function getRankingIndividual() {
  return readRows(SHEETS.rankingIndividual);
}

function getRankingAreas() {
  return readRows(SHEETS.rankingAreas);
}

function rebuildRankings(participants, predictions, results, scoreRows, timestamp) {
  const participantMap = {};
  participants.forEach(function (participant) {
    participantMap[participant.participante_id] = participant;
  });

  const stats = {};
  scoreRows.forEach(function (score) {
    const participant = participantMap[score.participante_id];
    if (!participant) return;
    if (!stats[score.participante_id]) {
      stats[score.participante_id] = {
        participante_id: score.participante_id,
        nombre_apellido: participant.nombre_apellido,
        area: participant.area,
        fecha_registro: participant.fecha_registro,
        puntaje_total: 0,
        resultados_exactos: 0,
        ganadores_correctos: 0
      };
    }

    const prediction = predictions.find(function (item) {
      return item.participante_id === score.participante_id && item.partido_id === score.partido_id;
    });
    const result = results.find(function (item) { return item.partido_id === score.partido_id; });
    const detail = prediction && result ? calculatePredictionPoints(prediction, result) : { exact: false, winner: false };

    stats[score.participante_id].puntaje_total += Number(score.puntos_total || 0);
    if (detail.exact) {
      stats[score.participante_id].resultados_exactos += 1;
    } else if (detail.winner) {
      stats[score.participante_id].ganadores_correctos += 1;
    }
  });

  const individual = Object.keys(stats).map(function (key) { return stats[key]; }).sort(function (a, b) {
    return b.puntaje_total - a.puntaje_total ||
      b.resultados_exactos - a.resultados_exactos ||
      b.ganadores_correctos - a.ganadores_correctos ||
      new Date(a.fecha_registro).getTime() - new Date(b.fecha_registro).getTime() ||
      String(a.nombre_apellido).localeCompare(String(b.nombre_apellido));
  }).map(function (item, index) {
    return {
      posicion: index + 1,
      participante_id: item.participante_id,
      nombre_apellido: item.nombre_apellido,
      area: item.area,
      puntaje_total: item.puntaje_total,
      resultados_exactos: item.resultados_exactos,
      ganadores_correctos: item.ganadores_correctos,
      fecha_actualizacion: timestamp
    };
  });

  replaceRows(SHEETS.rankingIndividual, individual);
  replaceRows(SHEETS.rankingAreas, buildAreaRanking(participants, individual, timestamp));
}

function buildAreaRanking(participants, individual, timestamp) {
  const areas = {};
  participants.forEach(function (participant) {
    if (participant.estado === 'inactivo') return;
    if (!areas[participant.area]) {
      areas[participant.area] = { participants: 0, scores: [] };
    }
    areas[participant.area].participants += 1;
  });

  individual.forEach(function (item) {
    if (!areas[item.area]) {
      areas[item.area] = { participants: 0, scores: [] };
    }
    areas[item.area].scores.push(Number(item.puntaje_total || 0));
  });

  return Object.keys(areas).map(function (area) {
    const scores = areas[area].scores.sort(function (a, b) { return b - a; }).slice(0, 5);
    const total = scores.reduce(function (sum, value) { return sum + value; }, 0);
    return {
      area: area,
      cantidad_participantes: areas[area].participants,
      puntaje_promedio_top5: scores.length ? total / scores.length : 0,
      puntaje_total_top5: total,
      fecha_actualizacion: timestamp
    };
  }).sort(function (a, b) {
    return b.puntaje_promedio_top5 - a.puntaje_promedio_top5 || String(a.area).localeCompare(String(b.area));
  }).map(function (item, index) {
    item.posicion = index + 1;
    return item;
  });
}
