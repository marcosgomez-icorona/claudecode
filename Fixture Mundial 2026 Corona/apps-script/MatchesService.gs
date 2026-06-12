function listMatches() {
  return readRows(SHEETS.matches).sort(function (a, b) {
    return new Date(a.fecha_partido).getTime() - new Date(b.fecha_partido).getTime();
  });
}

function findMatch(partidoId) {
  return readRows(SHEETS.matches).find(function (item) {
    return item.partido_id === partidoId;
  });
}

function saveMatch(payload) {
  assertAdmin(payload);
  requireFields(payload, ['fase', 'equipo_a', 'equipo_b', 'fecha_partido', 'fecha_limite_prediccion']);

  const rows = readRows(SHEETS.matches);
  const existing = payload.partido_id ? rows.find(function (item) { return item.partido_id === payload.partido_id; }) : null;
  const match = {
    partido_id: payload.partido_id || makeId('mat'),
    fase: String(payload.fase).trim(),
    equipo_a: String(payload.equipo_a).trim(),
    equipo_b: String(payload.equipo_b).trim(),
    fecha_partido: toIso(payload.fecha_partido),
    fecha_limite_prediccion: toIso(payload.fecha_limite_prediccion),
    estado: payload.estado || 'programado'
  };

  if (existing) {
    updateRow(SHEETS.matches, existing._rowNumber, match);
  } else {
    appendRow(SHEETS.matches, match);
  }
  return match;
}

function toIso(value) {
  const date = new Date(value);
  if (isNaN(date.getTime())) {
    throw new Error('Fecha invalida: ' + value);
  }
  return date.toISOString();
}
