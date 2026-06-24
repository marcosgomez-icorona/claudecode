/**
 * fn_agente_status_format.js — Formatea respuesta de estado del agente
 * Flow: Despachos Pendientes v1.5.0
 *
 * INPUT:  msg.payload → resultado de la query MySQL (array con 1 fila)
 * OUTPUT: msg.payload → JSON listo para HTTP response
 */

var row = (msg.payload && msg.payload.length > 0) ? msg.payload[0] : {};

msg.payload = {
  service: 'despachos-agente',
  version: '1.5.0',
  cache: {
    totalRemitos: row.total_en_cache || 0,
    sinClasificar: row.sin_clasificar || 0,
    clasificados: (row.total_en_cache || 0) - (row.sin_clasificar || 0)
  },
  ultimaEjecucion: row.ultima_ejecucion || null,
  hoy: {
    clasificados: row.clasificados_hoy || 0,
    ejecuciones: row.ejecuciones_hoy || 0
  },
  estadosDisponibles: [
    'APTO_PARA_PROGRAMAR',
    'PENDIENTE_VALIDACION',
    'REQUIERE_APROBACION_HUMANA',
    'BLOQUEADO'
  ]
};

return msg;
