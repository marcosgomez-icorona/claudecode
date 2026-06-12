function registerParticipant(payload) {
  requireFields(payload, ['nombre_apellido', 'email', 'area']);
  const email = normalizeEmail(payload.email);
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    throw new Error('Email invalido.');
  }

  const participants = readRows(SHEETS.participants);
  const duplicate = participants.some(function (item) {
    return normalizeEmail(item.email) === email;
  });
  if (duplicate) {
    throw new Error('Ya existe un participante registrado con ese email.');
  }

  const participant = {
    participante_id: makeId('par'),
    nombre_apellido: String(payload.nombre_apellido).trim(),
    email: email,
    area: String(payload.area).trim(),
    telefono: payload.telefono || '',
    fecha_registro: nowIso(),
    estado: 'activo'
  };
  appendRow(SHEETS.participants, participant);
  return participant;
}

function findParticipantByEmail(email) {
  const normalized = normalizeEmail(email);
  return readRows(SHEETS.participants).find(function (item) {
    return normalizeEmail(item.email) === normalized && item.estado !== 'inactivo';
  });
}

function getParticipantByEmail(payload) {
  requireFields(payload, ['email']);
  const participant = findParticipantByEmail(payload.email);
  if (!participant) {
    throw new Error('No encontramos un participante registrado con ese email.');
  }
  return {
    participante_id: participant.participante_id,
    nombre_apellido: participant.nombre_apellido,
    email: normalizeEmail(participant.email),
    area: participant.area,
    estado: participant.estado
  };
}
