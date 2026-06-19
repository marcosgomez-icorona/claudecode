#!/usr/bin/env python3
"""
Build a Node-RED subflow that syncs approved facturas from MySQL staging to SQL Server.
This runs on a timer (every 5 min) or can be triggered manually.
"""

import json, uuid

def gen_id(prefix='n'):
    return prefix + uuid.uuid4().hex[:11]

TAB_ID = 'facturas-sync-tab'

tab = {
    "id": TAB_ID,
    "label": "Sync Calipso",
    "type": "tab",
    "disabled": False,
    "info": "Sincroniza facturas aprobadas desde MySQL a SQL Server UD_EZI_STAGING_FACTURAS"
}

# MySQL config (reuse ID from main flow - must be adjusted when importing)
# MSSQL config (same)

# Timer node
timer = {
    "id": "sync-timer-01",
    "type": "inject",
    "z": TAB_ID,
    "name": "Cada 5 min",
    "props": [{"p": "payload"}, {"p": "topic", "vt": "str"}],
    "repeat": "300",
    "crontab": "",
    "once": True,
    "onceDelay": 30,
    "topic": "",
    "payload": "",
    "payloadType": "date",
    "x": 120,
    "y": 80,
    "wires": [["sync-check-01"]]
}

# Manual trigger
manual = {
    "id": "sync-manual-01",
    "type": "inject",
    "z": TAB_ID,
    "name": "Sync ahora",
    "props": [{"p": "payload"}],
    "repeat": "",
    "crontab": "",
    "once": False,
    "onceDelay": 0.1,
    "topic": "",
    "payload": "",
    "payloadType": "date",
    "x": 120,
    "y": 140,
    "wires": [["sync-check-01"]]
}

# Leer pendientes de sync
check = {
    "id": "sync-check-01",
    "type": "function",
    "z": TAB_ID,
    "name": "Leer pendientes sync",
    "func": """// Leer facturas aprobadas pendientes de sync a SQL Server
msg.topic = "SELECT f.*, l.id AS log_id " +
    "FROM staging_facturas f " +
    "JOIN log_sync_calipso l ON l.factura_id = f.id " +
    "WHERE f.estado_proceso = 'APROBADO' " +
    "AND l.estado = 'PENDIENTE' " +
    "LIMIT 10";
return msg;""",
    "outputs": 1,
    "x": 320,
    "y": 80,
    "wires": [["sync-mysql-01"]]
}

mysql_q = {
    "id": "sync-mysql-01",
    "type": "mysql",
    "z": TAB_ID,
    "mydb": "mysqle9c00ba9445",
    "name": "Leer MySQL",
    "x": 520,
    "y": 80,
    "wires": [["sync-process-01"]]
}

process = {
    "id": "sync-process-01",
    "type": "function",
    "z": TAB_ID,
    "name": "Procesar sync a SQL Server",
    "func": """// Procesar cada fila y generar INSERT a SQL Server
var facturas = msg.payload || [];
var results = [];

for (var i = 0; i < facturas.length; i++) {
    var f = facturas[i];
    var esDolar = (f.es_dolar == 1 || f.es_dolar == '1') ? 1 : 0;
    var monedaId = esDolar
        ? '76C69768-3DAE-11D5-B059-004854841C8A'
        : '76C69765-3DAE-11D5-B059-004854841C8A';

    var esc = function(v) {
        if (v === null || v === undefined) return 'NULL';
        return "'" + String(v).replace(/'/g, "''") + "'";
    };
    var num = function(v) {
        if (v === null || v === undefined || v === '' || isNaN(Number(v))) return 0;
        return Number(v);
    };
    var fmtDate = function(d) {
        if (!d) return 'NULL';
        var s = String(d).replace(/[^0-9]/g, '');
        return s.length >= 8 ? "'" + s.substring(0,8) + "'" : 'NULL';
    };

    var now = new Date();
    var ts = now.getFullYear().toString() +
        String(now.getMonth()+1).padStart(2,'0') +
        String(now.getDate()).padStart(2,'0') +
        String(now.getHours()).padStart(2,'0') +
        String(now.getMinutes()).padStart(2,'0') +
        String(now.getSeconds()).padStart(2,'0');

    var sql = "INSERT INTO UD_EZI_STAGING_FACTURAS (" +
        "ID, TIPO_OPERACION, ESTADO_PROCESO, FECHA_CARGA, FECHA_APROBACION, " +
        "USUARIO_CARGA, APROBADO_POR, ORIGEN, " +
        "TIPOTRANSACCION_ID, NUMERODOCUMENTO, LETRA, FECHA_EMISION, " +
        "FECHA_VENCIMIENTO, REFERENCIA, " +
        "PROVEEDOR_ID, PROVEEDOR_CODIGO, PROVEEDOR_CUIT, PROVEEDOR_NOMBRE, " +
        "NETO, IVA_21, IVA_105, PERCEPCIONES, OTROS_IMPUESTOS, TOTAL, " +
        "MONEDA_ID, COTIZACION, " +
        "CAE, FECHA_VTO_CAE, COMPANIA_ID, " +
        "PDF_FILENAME, PDF_HASH, EMAIL_ORIGEN, EMAIL_ASUNTO, " +
        "ITEMS_JSON, ES_DOLAR, COTIZACION_ORIGEN, COTIZACION_AVISO, " +
        "NRO_REMITO, CONFIANZA_PARSEO, NOTAS_PARSEO" +
        ") VALUES (" +
        "NEWID(), " +
        esc(f.tipo_operacion || 'FACTURA_COMPRA') + ", " +
        "'APROBADO', " +
        esc(ts) + ", " +
        esc(ts) + ", " +
        "'n8n', " +
        esc(f.aprobado_por || 'pdietrich') + ", " +
        esc(f.origen || 'EMAIL_GMAIL_N8N') + ", " +
        "'50829758-5905-11D5-86C4-0080AD403F5F', " +  // TipoTR Fact.Cpra.
        esc(f.numerodocumento) + ", " +
        esc(f.letra) + ", " +
        fmtDate(f.fecha_emision) + ", " +
        fmtDate(f.fecha_vencimiento) + ", " +
        esc(f.referencia) + ", " +
        "'00000000-0000-0000-0000-000000000000', " +  // PROVEEDOR_ID placeholder
        esc(f.proveedor_codigo) + ", " +
        esc(f.proveedor_cuit) + ", " +
        esc(f.proveedor_nombre) + ", " +
        num(f.neto) + ", " +
        num(f.iva_21) + ", " +
        num(f.iva_105) + ", " +
        num(f.percepciones) + ", " +
        num(f.otros_impuestos) + ", " +
        num(f.total) + ", " +
        "'" + monedaId + "', " +
        num(f.cotizacion || 1) + ", " +
        esc(f.cae) + ", " +
        fmtDate(f.fecha_vto_cae) + ", " +
        "'FC20C32D-3EFA-11D5-86AD-0080AD403F5F', " +  // Compania ID
        esc(f.pdf_filename) + ", " +
        esc(f.pdf_hash) + ", " +
        esc(f.email_origen) + ", " +
        esc(f.email_asunto) + ", " +
        esc(f.items_json) + ", " +
        esDolar + ", " +
        esc(f.cotizacion_origen) + ", " +
        esc(f.cotizacion_aviso) + ", " +
        esc(f.nro_remito) + ", " +
        "70, " +  // confianza_parseo default
        esc(f.notas_parseo) +
        ")";

    results.push({
        sql: sql,
        factura_id: f.id,
        log_id: f.log_id
    });
}

msg._results = results;
msg._index = 0;
return msg;""",
    "outputs": 1,
    "x": 740,
    "y": 80,
    "wires": [["sync-loop-01"]]
}

# Loop through results
loop = {
    "id": "sync-loop-01",
    "type": "function",
    "z": TAB_ID,
    "name": "Loop: ejecutar INSERT",
    "func": """// Ejecutar INSERT para cada factura
var results = msg._results || [];
var idx = msg._index || 0;

if (idx >= results.length) {
    // Ya terminamos
    node.status({fill:"green", shape:"dot", text:"Sync completado: " + results.length + " facturas"});
    return null;
}

var r = results[idx];
msg.payload = r.sql;
msg._currentLogId = r.log_id;
msg._currentFacturaId = r.factura_id;
msg._index = idx + 1;
msg._total = results.length;

return msg;""",
    "outputs": 1,
    "x": 940,
    "y": 80,
    "wires": [["sync-mssql-01"]]
}

mssql_exec = {
    "id": "sync-mssql-01",
    "type": "MSSQL",
    "z": TAB_ID,
    "mssql": "mssql-corona-config",
    "name": "INSERT a SQL Server",
    "query": "",
    "out": "msg",
    "x": 1140,
    "y": 80,
    "wires": [["sync-result-01"]]
}

result = {
    "id": "sync-result-01",
    "type": "function",
    "z": TAB_ID,
    "name": "Resultado: log OK o ERROR",
    "func": """// Registrar resultado del sync
var total = msg._total || 0;
var facturaId = msg._currentFacturaId;
var logId = msg._currentLogId;

if (msg.payload && msg.payload.rowsAffected && msg.payload.rowsAffected > 0) {
    // OK: actualizar log como sincronizado
    node.status({fill:"green", shape:"dot", text: "OK: " + facturaId});
    msg.topic = "UPDATE log_sync_calipso SET estado = 'SINCRONIZADO', " +
        "fecha_sync = NOW() " +
        "WHERE id = " + logId;
} else {
    // ERROR
    var err = msg.error || msg.payload || {};
    node.status({fill:"red", shape:"dot", text: "Error: " + facturaId});
    msg.topic = "UPDATE log_sync_calipso SET estado = 'ERROR', " +
        "error_detalle = '" + (String(err.message || err).replace(/'/g, "''")) + "', " +
        "fecha_sync = NOW() " +
        "WHERE id = " + logId;
}

// Volver al loop
return msg;""",
    "outputs": 1,
    "x": 1340,
    "y": 80,
    "wires": [["sync-update-log-01"]]
}

# MySQL update for log
update_log = {
    "id": "sync-update-log-01",
    "type": "mysql",
    "z": TAB_ID,
    "mydb": "mysqle9c00ba9445",
    "name": "Actualizar log sync",
    "x": 1540,
    "y": 80,
    "wires": [["sync-loop-01"]]
}

# Error handler
catch_sync = {
    "id": "sync-catch-01",
    "type": "catch",
    "z": TAB_ID,
    "name": "Error sync",
    "scope": [check['id'], mysql_q['id'], process['id'], loop['id'], mssql_exec['id'], result['id'], update_log['id']],
    "x": 120,
    "y": 300,
    "wires": [["sync-error-01"]]
}

error_handler = {
    "id": "sync-error-01",
    "type": "function",
    "z": TAB_ID,
    "name": "Log error + continuar",
    "func": """// Loggear error y continuar con siguiente
var err = msg._error || msg.error || {};
node.warn("Error en sync: " + (err.message || JSON.stringify(err)));

// Avanzar al siguiente item en el loop
var results = msg._results || [];
var idx = (msg._index || 0) + 1;
msg._index = idx;

if (idx < results.length) {
    var r = results[idx];
    msg.payload = r.sql;
    msg._currentLogId = r.log_id;
    msg._currentFacturaId = r.factura_id;
    return msg;
}
return null;""",
    "outputs": 1,
    "x": 320,
    "y": 300,
    "wires": [["sync-mssql-01"]]
}

all_nodes = [tab, timer, manual, check, mysql_q, process, loop, mssql_exec, result, update_log, catch_sync, error_handler]

# Save
with open('/mnt/c/claudecode/proyectos-ingenio/automatizacion-facturas/nodered/flow_sync_calipso.json', 'w') as f:
    json.dump(all_nodes, f, indent=2)

print(f"Flow sync generado: {len(all_nodes)} nodos")
print("Importar en Node-RED como nueva tab 'Sync Calipso'")
print("Requisito: npm install node-red-contrib-mssql-plus")
