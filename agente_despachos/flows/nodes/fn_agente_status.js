/**
 * fn_agente_status.js — Consulta y formatea el estado del agente
 * Endpoint: GET /api/despachos/agente/estado
 * Flow: Despachos Pendientes v1.5.0
 *
 * INPUT:  (msg desde HTTP in — no se usa el payload)
 * OUTPUT: msg.topic = SQL para consultar estadísticas y última ejecución
 *
 * El nodo mysql siguiente ejecuta la query.
 * Un segundo function node formatea la respuesta.
 */

msg.topic =
  "SELECT " +
  "  (SELECT COUNT(*) FROM despachos_pendientes_cache " +
  "   WHERE clasificacionAgente IS NULL OR clasificacionAgente = '') AS sin_clasificar, " +
  "  (SELECT COUNT(*) FROM despachos_pendientes_cache) AS total_en_cache, " +
  "  (SELECT MAX(creado) FROM despachos_agente_log) AS ultima_ejecucion, " +
  "  (SELECT COUNT(*) FROM despachos_agente_log " +
  "   WHERE DATE(creado) = CURDATE()) AS clasificados_hoy, " +
  "  (SELECT COUNT(DISTINCT run_uuid) FROM despachos_agente_log " +
  "   WHERE DATE(creado) = CURDATE()) AS ejecuciones_hoy";

return msg;
