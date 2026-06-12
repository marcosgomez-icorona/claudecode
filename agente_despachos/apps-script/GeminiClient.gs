function generateLlmExecutiveSummary_(results, data, runUuid) {
  const prompt = [
    'Actua como agente logistico interno del Ingenio La Corona.',
    'Redacta un resumen ejecutivo breve, operativo y sin inventar datos.',
    'No confirmes entregas. No agregues informacion que no este en el JSON.',
    'Run UUID: ' + runUuid,
    'Datos:',
    JSON.stringify({
      total: results.length,
      clasificaciones: countStatuses_(results),
      resultados: results.map(function(item) {
        return {
          pedido_id: item.context.pedidoId,
          cliente_id: item.context.clienteId,
          producto_tipo: item.context.productoTipo,
          cantidad: item.context.cantidad,
          clasificacion: item.clasificacion,
          motivos: item.motivos,
          riesgos: item.riesgos
        };
      })
    })
  ].join('\n');

  return callGemini_(prompt);
}

function callGemini_(prompt) {
  const apiKey = PropertiesService.getScriptProperties().getProperty(DISPATCH_CONFIG.llm.apiKeyProperty);
  if (!apiKey) {
    throw new Error('Falta configurar Script Property: ' + DISPATCH_CONFIG.llm.apiKeyProperty);
  }

  const model = DISPATCH_CONFIG.llm.model;
  const url = 'https://generativelanguage.googleapis.com/v1beta/models/' + encodeURIComponent(model) + ':generateContent?key=' + encodeURIComponent(apiKey);

  const payload = {
    contents: [
      {
        role: 'user',
        parts: [{ text: prompt }]
      }
    ],
    generationConfig: {
      temperature: 0.2,
      topP: 0.8,
      maxOutputTokens: 1200
    }
  };

  const response = UrlFetchApp.fetch(url, {
    method: 'post',
    contentType: 'application/json',
    payload: JSON.stringify(payload),
    muteHttpExceptions: true
  });

  const status = response.getResponseCode();
  const body = response.getContentText();

  if (status < 200 || status >= 300) {
    throw new Error('Gemini HTTP ' + status + ': ' + body);
  }

  const parsed = JSON.parse(body);
  const candidate = parsed.candidates && parsed.candidates[0];
  const part = candidate && candidate.content && candidate.content.parts && candidate.content.parts[0];

  if (!part || !part.text) {
    throw new Error('Respuesta Gemini sin texto util: ' + body);
  }

  return part.text;
}

function countStatuses_(results) {
  const counts = {};
  results.forEach(function(item) {
    counts[item.clasificacion] = (counts[item.clasificacion] || 0) + 1;
  });
  return counts;
}

