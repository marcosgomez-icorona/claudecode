(function () {
  const fallbackMatches = [
    {
      partido_id: 'demo_1',
      fase: 'Grupo',
      equipo_a: 'Argentina',
      equipo_b: 'Canada',
      fecha_partido: '2026-06-11T21:00:00-03:00',
      fecha_limite_prediccion: '2026-06-11T20:00:00-03:00',
      estado: 'programado'
    },
    {
      partido_id: 'demo_2',
      fase: 'Grupo',
      equipo_a: 'Mexico',
      equipo_b: 'Estados Unidos',
      fecha_partido: '2026-06-12T18:00:00-03:00',
      fecha_limite_prediccion: '2026-06-12T17:00:00-03:00',
      estado: 'programado'
    }
  ];

  const fallbackRanking = [
    { posicion: 1, nombre_apellido: 'Participante Demo', area: 'Sistemas', puntaje_total: 0, resultados_exactos: 0, ganadores_correctos: 0 }
  ];

  const fallbackAreas = [
    { posicion: 1, area: 'Sistemas', cantidad_participantes: 1, puntaje_promedio_top5: 0, puntaje_total_top5: 0 }
  ];

  const fallbackConfig = {
    reglamento_html: '<h3>Puntajes</h3><p>Resultado exacto: 5 puntos. Ganador o empate correcto: 2 puntos.</p><p>Las predicciones cierran segun la fecha limite de cada partido.</p>',
    premios_html: '<h3>Premios</h3><p>Los premios seran comunicados por la organizacion interna del Ingenio La Corona.</p>'
  };

  async function request(action, payload) {
    const endpoint = window.CORONA_CONFIG.apiBaseUrl;
    if (!endpoint) {
      return fallback(action);
    }

    const response = await fetch(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'text/plain;charset=utf-8' },
      body: JSON.stringify({ action, payload: payload || {} })
    });

    const data = await response.json();
    if (!data.ok) {
      throw new Error(data.message || 'No se pudo completar la operacion.');
    }
    return data.data;
  }

  function fallback(action) {
    const readOnly = {
      getHomeData: { matches: fallbackMatches, config: fallbackConfig },
      listMatches: fallbackMatches,
      getParticipantByEmail: { participante_id: 'par_demo', nombre_apellido: 'Participante Demo', email: 'demo@ingeniolacorona.com', area: 'Sistemas', estado: 'activo' },
      getPredictionsByParticipant: [],
      getRankingIndividual: fallbackRanking,
      getRankingAreas: fallbackAreas,
      getContent: fallbackConfig
    };

    if (Object.prototype.hasOwnProperty.call(readOnly, action)) {
      return Promise.resolve(readOnly[action]);
    }

    return Promise.reject(new Error('Configurar endpoint Apps Script para guardar datos.'));
  }

  window.CoronaApi = {
    request,
    getHomeData: () => request('getHomeData'),
    registerParticipant: (payload) => request('registerParticipant', payload),
    getParticipantByEmail: (payload) => request('getParticipantByEmail', payload),
    getPredictionsByParticipant: (payload) => request('getPredictionsByParticipant', payload),
    listMatches: () => request('listMatches'),
    savePrediction: (payload) => request('savePrediction', payload),
    getRankingIndividual: () => request('getRankingIndividual'),
    getRankingAreas: () => request('getRankingAreas'),
    getContent: () => request('getContent'),
    adminLogin: (payload) => request('adminLogin', payload),
    saveMatch: (payload) => request('saveMatch', payload),
    saveResult: (payload) => request('saveResult', payload),
    recalculateScores: (payload) => request('recalculateScores', payload),
    syncOfficialFixture: (payload) => request('syncOfficialFixture', payload)
  };
})();
