const SPREADSHEET_ID = '1fCbqNsbnAZjE6IZqsAt_PMNEwkzXsEd7aervD1iDU6M';

function doPost(e) {
  try {
    const body = JSON.parse(e.postData.contents || '{}');
    const action = body.action;
    const payload = body.payload || {};
    const data = routeAction(action, payload);
    return jsonResponse({ ok: true, data: data });
  } catch (error) {
    return jsonResponse({ ok: false, message: error.message });
  }
}

function doGet(e) {
  if (e && e.parameter && e.parameter.api === 'fixture') {
    return jsonResponse(getLocalOfficialFixtureData());
  }
  return jsonResponse({ ok: true, data: { service: 'Corona Mundial 2026 API' } });
}

function routeAction(action, payload) {
  const routes = {
    getHomeData: function () {
      return {
        matches: listMatches().slice(0, 6),
        config: getPublicConfig()
      };
    },
    registerParticipant: registerParticipant,
    getParticipantByEmail: getParticipantByEmail,
    getPredictionsByParticipant: getPredictionsByParticipant,
    listMatches: listMatches,
    savePrediction: savePrediction,
    getRankingIndividual: getRankingIndividual,
    getRankingAreas: getRankingAreas,
    getContent: getPublicConfig,
    adminLogin: adminLogin,
    saveMatch: saveMatch,
    saveResult: saveResult,
    recalculateScores: recalculateScores,
    syncOfficialFixture: syncOfficialFixture
  };

  if (!routes[action]) {
    throw new Error('Accion no soportada: ' + action);
  }
  return routes[action](payload);
}

function jsonResponse(payload) {
  return ContentService
    .createTextOutput(JSON.stringify(payload))
    .setMimeType(ContentService.MimeType.JSON);
}

function setupSpreadsheet() {
  ensureSheets();
  seedDefaultConfig();
}

function createFiveMinuteFixtureTrigger() {
  deleteFixtureSyncTriggers();
  ScriptApp.newTrigger('syncOfficialFixture')
    .timeBased()
    .everyMinutes(5)
    .create();
}

function deleteFixtureSyncTriggers() {
  ScriptApp.getProjectTriggers().forEach(function (trigger) {
    if (trigger.getHandlerFunction() === 'syncOfficialFixture') {
      ScriptApp.deleteTrigger(trigger);
    }
  });
}
