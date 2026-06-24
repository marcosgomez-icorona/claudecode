/**
 * fn_agente_update.js — Prepara UPDATE e INSERT para aplicar clasificación
 * Flow: Despachos Pendientes v1.5.0
 *
 * INPUT:  msg.payload  → array de resultados del motor
 *         msg.runUuid  → UUID de la ejecución
 *         msg.summary  → resumen de clasificación
 *
 * OUTPUT: msg.topic    → SQL batch (UPDATE + INSERT)
 *         msg.payload  → se preserva para la respuesta final
 *         msg.summary  → se preserva
 */

var resultados = msg.payload || [];
var runUuid = msg.runUuid || 'SIN-UUID';

if (resultados.length === 0) {
  // Sin remitos para clasificar
  msg.topic = "SELECT 'sin_remitos' AS status";
  msg.summary = { total: 0, aptos: 0, pendientes: 0, requieren_aprob: 0, bloqueados: 0 };
  return msg;
}

// Construir batch SQL: UPDATE cache + INSERT auditoría
var sqlStatements = [];

// 1. UPDATEs a despachos_pendientes_cache
for (var i = 0; i < resultados.length; i++) {
  var r = resultados[i];
  var remitoEscaped = (r.remito || '').replace(/'/g, "\\'");
  var estadoEscaped = (r.estado_nuevo || '').replace(/'/g, "\\'");

  sqlStatements.push(
    "UPDATE despachos_pendientes_cache " +
    "SET clasificacionAgente = '" + estadoEscaped + "' " +
    "WHERE remito = '" + remitoEscaped + "';"
  );
}

// 2. INSERTs a despachos_agente_log
for (var j = 0; j < resultados.length; j++) {
  var r = resultados[j];
  var remitoEscaped = (r.remito || '').replace(/'/g, "\\'");
  var anteriorEscaped = (r.estado_anterior || '').replace(/'/g, "\\'");
  var nuevoEscaped = (r.estado_nuevo || '').replace(/'/g, "\\'");
  var motivoEscaped = (r.motivo || '').replace(/'/g, "\\'").substring(0, 300);
  var reglaId = r.regla_id ? r.regla_id : 'NULL';
  var puntaje = r.puntaje || 0;

  sqlStatements.push(
    "INSERT INTO despachos_agente_log " +
    "(run_uuid, remito, estado_anterior, estado_nuevo, motivo, puntaje, regla_id) " +
    "VALUES ('" + runUuid + "', '" + remitoEscaped + "', " +
    (anteriorEscaped ? "'" + anteriorEscaped + "'" : 'NULL') + ", " +
    "'" + nuevoEscaped + "', '" + motivoEscaped + "', " +
    puntaje + ", " + reglaId + ");"
  );
}

// Unir todo en un batch
msg.topic = sqlStatements.join('\n');

return msg;
