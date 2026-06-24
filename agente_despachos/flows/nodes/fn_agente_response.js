/**
 * fn_agente_response.js — Formatea la respuesta final del agente
 * Flow: Despachos Pendientes v1.5.0
 *
 * INPUT:  msg.summary  → resumen de la clasificación
 *         msg.runUuid  → UUID de la ejecución
 *         msg.payload  → resultados detallados
 *
 * OUTPUT: msg.payload  → JSON listo para HTTP response
 */

var summary = msg.summary || {};
var runUuid = msg.runUuid || '';
var resultados = msg.payload || [];

msg.payload = {
  success: true,
  runUuid: runUuid,
  timestamp: new Date().toISOString(),
  summary: {
    totalProcesados: summary.total || 0,
    aptos: summary.aptos || 0,
    pendientesValidacion: summary.pendientes || 0,
    requierenAprobacion: summary.requieren_aprob || 0,
    bloqueados: summary.bloqueados || 0,
    sinCambio: summary.sin_cambio || 0,
    cambiaron: summary.cambiaron || 0
  },
  detalles: resultados.slice(0, 50).map(function(r) {
    return {
      remito: r.remito,
      estadoAnterior: r.estado_anterior || null,
      estadoNuevo: r.estado_nuevo,
      motivo: r.motivo,
      puntaje: r.puntaje
    };
  }),
  reglasUsadas: msg.reglas_usadas || 0,
  reglasEmbebidas: msg.reglas_embebidas || false
};

return msg;
