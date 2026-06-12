const SHEETS = {
  participants: 'Participantes',
  matches: 'Partidos',
  predictions: 'Predicciones',
  results: 'Resultados',
  scores: 'Puntajes',
  rankingIndividual: 'Ranking Individual',
  rankingAreas: 'Ranking Areas',
  config: 'Configuracion',
  teams: 'Equipos',
  syncLog: 'Sincronizaciones'
};

const HEADERS = {};
HEADERS[SHEETS.participants] = ['participante_id', 'nombre_apellido', 'email', 'area', 'telefono', 'fecha_registro', 'estado'];
HEADERS[SHEETS.matches] = ['partido_id', 'fase', 'equipo_a', 'equipo_b', 'fecha_partido', 'fecha_limite_prediccion', 'estado'];
HEADERS[SHEETS.predictions] = ['prediccion_id', 'participante_id', 'partido_id', 'email', 'ganador_predicho', 'goles_a_predicho', 'goles_b_predicho', 'es_empate_predicho', 'fecha_prediccion', 'fecha_actualizacion', 'estado'];
HEADERS[SHEETS.results] = ['resultado_id', 'partido_id', 'goles_a_real', 'goles_b_real', 'ganador_real', 'es_empate_real', 'fecha_carga', 'cargado_por'];
HEADERS[SHEETS.scores] = ['puntaje_id', 'participante_id', 'partido_id', 'puntos_resultado', 'puntos_extra', 'puntos_total', 'fecha_calculo'];
HEADERS[SHEETS.rankingIndividual] = ['posicion', 'participante_id', 'nombre_apellido', 'area', 'puntaje_total', 'resultados_exactos', 'ganadores_correctos', 'fecha_actualizacion'];
HEADERS[SHEETS.rankingAreas] = ['posicion', 'area', 'cantidad_participantes', 'puntaje_promedio_top5', 'puntaje_total_top5', 'fecha_actualizacion'];
HEADERS[SHEETS.config] = ['clave', 'valor', 'descripcion', 'fecha_actualizacion'];
HEADERS[SHEETS.teams] = ['codigo', 'nombre_api', 'nombre_es', 'grupo', 'fecha_actualizacion'];
HEADERS[SHEETS.syncLog] = ['sync_id', 'proveedor', 'estado', 'partidos_actualizados', 'resultados_actualizados', 'mensaje', 'fecha_sync'];

var SPREADSHEET_CACHE = null;
var SHEETS_READY = false;

function getSpreadsheet() {
  if (SPREADSHEET_ID === 'PEGAR_ID_GOOGLE_SHEET') {
    throw new Error('Configurar SPREADSHEET_ID en Code.gs.');
  }
  if (!SPREADSHEET_CACHE) {
    SPREADSHEET_CACHE = SpreadsheetApp.openById(SPREADSHEET_ID);
  }
  return SPREADSHEET_CACHE;
}

function ensureSheets() {
  if (SHEETS_READY) return;
  const ss = getSpreadsheet();
  Object.keys(HEADERS).forEach(function (sheetName) {
    let sheet = ss.getSheetByName(sheetName);
    if (!sheet) {
      sheet = ss.insertSheet(sheetName);
    }
    const headers = HEADERS[sheetName];
    sheet.getRange(1, 1, 1, headers.length).setValues([headers]);
    sheet.setFrozenRows(1);
  });
  SHEETS_READY = true;
}

function getSheet(name) {
  ensureSheets();
  return getSpreadsheet().getSheetByName(name);
}

function readRows(name) {
  const sheet = getSheet(name);
  const values = sheet.getDataRange().getValues();
  if (values.length <= 1) return [];
  const headers = values[0];
  return values.slice(1).filter(function (row) {
    return row.some(function (cell) { return cell !== ''; });
  }).map(function (row, index) {
    const item = { _rowNumber: index + 2 };
    headers.forEach(function (header, columnIndex) {
      item[header] = row[columnIndex];
    });
    return item;
  });
}

function appendRow(name, object) {
  const sheet = getSheet(name);
  const headers = HEADERS[name];
  sheet.appendRow(headers.map(function (header) {
    return object[header] !== undefined ? object[header] : '';
  }));
}

function updateRow(name, rowNumber, object) {
  const sheet = getSheet(name);
  const headers = HEADERS[name];
  sheet.getRange(rowNumber, 1, 1, headers.length).setValues([headers.map(function (header) {
    return object[header] !== undefined ? object[header] : '';
  })]);
}

function replaceRows(name, rows) {
  const sheet = getSheet(name);
  const headers = HEADERS[name];
  const lastRow = sheet.getLastRow();
  if (lastRow > 1) {
    sheet.getRange(2, 1, lastRow - 1, headers.length).clearContent();
  }
  if (rows.length) {
    sheet.getRange(2, 1, rows.length, headers.length).setValues(rows.map(function (row) {
      return headers.map(function (header) {
        return row[header] !== undefined ? row[header] : '';
      });
    }));
  }
}

function nowIso() {
  return new Date().toISOString();
}

function makeId(prefix) {
  const stamp = Utilities.formatDate(new Date(), Session.getScriptTimeZone(), 'yyyyMMddHHmmss');
  const random = Math.random().toString(36).substring(2, 6);
  return prefix + '_' + stamp + '_' + random;
}

function normalizeEmail(email) {
  return String(email || '').trim().toLowerCase();
}

function requireFields(payload, fields) {
  fields.forEach(function (field) {
    if (payload[field] === undefined || payload[field] === null || String(payload[field]).trim() === '') {
      throw new Error('Campo obligatorio: ' + field);
    }
  });
}
