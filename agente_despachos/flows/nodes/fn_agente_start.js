/**
 * fn_agente_start.js — Inicializa ejecución del agente clasificador
 * Endpoint: POST /api/despachos/agente/clasificar
 * Flow: Despachos Pendientes v1.5.0
 *
 * Genera UUID, lee parámetros (days), y construye queries para:
 *  - Leer remitos sin clasificar de la cache MySQL
 *  - Leer reglas activas de despachos_reglas_clasificacion
 *
 * Las queries se pasan a msg.queries[] para que el motor las ejecute.
 */

var days = 90; // default: todos los remitos en cache
if (msg.payload && msg.payload.days) {
  days = parseInt(msg.payload.days) || 90;
}

var runUuid = msg.runUuid || (function() {
  // crypto.randomUUID() fallback para entornos viejos
  if (typeof crypto !== 'undefined' && crypto.randomUUID) {
    return crypto.randomUUID();
  }
  return 'AGENTE-' + Date.now().toString(36).toUpperCase() + '-' +
    Math.random().toString(36).substring(2, 10).toUpperCase();
})();

// Guardar en msg para que lo usen los nodos siguientes
msg.runUuid = runUuid;
msg.days = days;
msg.startTime = new Date().toISOString();

// Query 1: Remitos sin clasificar (o reclasificar todos si force=true)
var force = (msg.payload && msg.payload.force === true);
var sqlRemitos;
if (force) {
  sqlRemitos = "SELECT * FROM despachos_pendientes_cache ORDER BY fecha DESC";
} else {
  sqlRemitos = "SELECT * FROM despachos_pendientes_cache " +
    "WHERE clasificacionAgente IS NULL OR clasificacionAgente = '' " +
    "ORDER BY fecha DESC";
}

// Query 2: Reglas activas ordenadas
var sqlReglas = "SELECT id, nombre, estado_resultante, condicion_tipo, " +
  "campo_evaluar, operador, valor_referencia, motivo, puntaje, orden " +
  "FROM despachos_reglas_clasificacion " +
  "WHERE activo = 1 ORDER BY orden ASC";

// Guardamos ambas queries en msg para el nodo mysql
// El nodo mysql ejecutará la primera, y wireamos la salida
// a un segundo mysql para la segunda query
msg.topic = sqlRemitos;
msg.sqlReglas = sqlReglas;
msg.sqlRemitos = sqlRemitos;

return msg;
