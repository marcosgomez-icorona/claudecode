function getConfigMap() {
  const rows = readRows(SHEETS.config);
  const config = {};
  rows.forEach(function (row) {
    config[row.clave] = row.valor;
  });
  return config;
}

function getPublicConfig() {
  const config = getConfigMap();
  return {
    fecha_inicio_mundial: config.fecha_inicio_mundial || '2026-06-11T00:00:00-03:00',
    reglamento_html: config.reglamento_html || '<h3>Puntajes</h3><p>Resultado exacto: 5 puntos. Ganador o empate correcto: 2 puntos.</p>',
    premios_html: config.premios_html || '<h3>Premios</h3><p>Premios a definir por la organizacion.</p>'
  };
}

function seedDefaultConfig() {
  const existing = getConfigMap();
  const defaults = [
    ['admin_clave', 'cambiar-esta-clave', 'Clave simple para administracion MVP'],
    ['fecha_inicio_mundial', '2026-06-11T00:00:00-03:00', 'Fecha del contador regresivo'],
    ['reglamento_html', '<h3>Tutorial y reglamento</h3><p>Registrate, elegi tu area, carga predicciones antes del cierre y suma puntos para el ranking individual y por area.</p><p>Resultado exacto: 5 puntos. Ganador o empate correcto: 2 puntos.</p>', 'Contenido HTML del reglamento'],
    ['premios_html', '<h3>Premios</h3><p>Premios a definir por la organizacion.</p>', 'Contenido HTML de premios'],
    ['fixture_api_url', '', 'URL de API propia con datos oficiales normalizados'],
    ['fixture_api_token', '', 'Token opcional para API propia'],
    ['fixture_api_provider', 'api-propia', 'Nombre del proveedor interno'],
    ['fixture_api_language', 'es', 'Idioma solicitado a la API propia'],
    ['fixture_sync_enabled', 'false', 'Habilita sincronizacion programada']
  ];
  defaults.forEach(function (item) {
    if (!existing[item[0]]) {
      appendRow(SHEETS.config, {
        clave: item[0],
        valor: item[1],
        descripcion: item[2],
        fecha_actualizacion: nowIso()
      });
    }
  });
}

function assertAdmin(payload) {
  const config = getConfigMap();
  if (!payload || String(payload.admin_key || '') !== String(config.admin_clave || 'cambiar-esta-clave')) {
    throw new Error('Clave administrativa invalida.');
  }
}

function adminLogin(payload) {
  assertAdmin(payload);
  return { authenticated: true };
}
