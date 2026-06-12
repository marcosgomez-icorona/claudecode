function ejecutarAgenteDespachos() {
  const runUuid = Utilities.getUuid();
  const startedAt = new Date();

  const data = loadDispatchData_();
  const results = classifyDispatchOrders_(data, runUuid);
  const report = buildDispatchMarkdownReport_(results, data, runUuid, startedAt);

  saveDispatchOutput_(report, runUuid);

  return {
    runUuid: runUuid,
    totalAnalizados: results.length,
    outputPreview: report.substring(0, 1000)
  };
}

function ejecutarAgenteDespachosConResumenIa() {
  const runUuid = Utilities.getUuid();
  const startedAt = new Date();

  const data = loadDispatchData_();
  const results = classifyDispatchOrders_(data, runUuid);
  let report = buildDispatchMarkdownReport_(results, data, runUuid, startedAt);

  try {
    const llmSummary = generateLlmExecutiveSummary_(results, data, runUuid);
    report = report.replace(
      'Principales riesgos:',
      'Resumen IA:\n' + llmSummary + '\n\nPrincipales riesgos:'
    );
  } catch (err) {
    logDispatchError_(runUuid, 'LLM_ERROR', err);
    report += '\n\n> Nota tecnica: fallo la generacion con IA. La clasificacion deterministica fue generada correctamente para revision humana.\n';
  }

  saveDispatchOutput_(report, runUuid);

  return {
    runUuid: runUuid,
    totalAnalizados: results.length,
    outputPreview: report.substring(0, 1000)
  };
}

