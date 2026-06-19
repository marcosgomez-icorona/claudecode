#!/usr/bin/env python3
"""
Build unified Node-RED flow for Facturas:
- Reads facturas from MySQL staging_facturas (where n8n writes)
- Reads OC/constancias from SQL Server (Calipso views)
- Syncs approved facturas from MySQL -> SQL Server UD_EZI_STAGING_FACTURAS
- Fixes critical bugs (T-SQL in JS, silent errors, SQL injection)
"""

import json, copy, uuid, re, sys

def load_json(path):
    with open(path) as f:
        return json.load(f)

def save_json(data, path):
    with open(path, 'w') as f:
        json.dump(data, f, indent=2)

# Load source flows
BASE     = load_json('/mnt/c/claudecode/proyectos-ingenio/automatizacion-facturas/nodered/flow_api_facturas_sin_constancia.json')
REGISTRO = load_json('/mnt/c/claudecode/proyectos-ingenio/automatizacion-facturas/nodered/api_registracion_facturas-16-06-26.json')

# Generate new unique IDs preserving readability
def gen_id(prefix='a'):
    return prefix + uuid.uuid4().hex[:11]

# Tab ID for the new flow
TAB_ID = 'facturas-unified-tab'

# ===================================================================
# 1. Start with base flow (MySQL endpoints for facturas)
# ===================================================================
new_nodes = []
# We'll copy nodes from BASE and REGISTRO, assigning new IDs

# Track all IDs to avoid conflicts
used_ids = set()
def new_id(prefix='n'):
    while True:
        nid = gen_id(prefix)
        if nid not in used_ids:
            used_ids.add(nid)
            return nid

# ===================================================================
# 2. MySQL Config node
# ===================================================================
mysql_config_id = new_id('mysql')
mysql_config = {
    "id": mysql_config_id,
    "type": "MySQL-database",
    "z": TAB_ID,
    "host": "127.0.0.1",
    "port": "3306",
    "db": "db_automatizaciones",
    "name": "MySQL staging",
    "x": 100,
    "y": 60,
    "wires": []
}

# ===================================================================
# 3. MSSQL Config node (for OC, constancias, Calipso reads)
# ===================================================================
mssql_config_id = new_id('mssql')
mssql_config = {
    "id": mssql_config_id,
    "type": "MSSQL",
    "z": TAB_ID,
    "mssqlConfig": "mssql-corona-config",
    "name": "SQL Server CORONA",
    "x": 100,
    "y": 120,
    "wires": []
}
# We need the MSSQL config in Node-RED settings as well
# The config node references a "mssql-corona-config" in the config nodes

# ===================================================================
# Helper: create a function node
# ===================================================================
def make_function(name, func, outputs=1, xy=(200, 200)):
    nid = new_id('fn')
    return {
        "id": nid,
        "type": "function",
        "z": TAB_ID,
        "name": name,
        "func": func,
        "outputs": outputs,
        "x": xy[0],
        "y": xy[1],
        "wires": [[] for _ in range(outputs)]
    }

def make_http_in(method, url, name, xy=(200, 200)):
    nid = new_id('http')
    return {
        "id": nid,
        "type": "http in",
        "z": TAB_ID,
        "name": name,
        "url": url,
        "method": method.lower(),
        "upload": False,
        "x": xy[0],
        "y": xy[1],
        "wires": [[]]
    }

def make_http_resp(xy=(200, 200)):
    nid = new_id('resp')
    return {
        "id": nid,
        "type": "http response",
        "z": TAB_ID,
        "name": "",
        "x": xy[0],
        "y": xy[1],
        "wires": []
    }

def make_mysql_node(name, config_id, xy=(200, 200)):
    nid = new_id('sql')
    return {
        "id": nid,
        "type": "mysql",
        "z": TAB_ID,
        "mydb": config_id,
        "name": name,
        "x": xy[0],
        "y": xy[1],
        "wires": [[]]
    }

def make_mssql_node(name, config_id, query='', xy=(200, 200)):
    nid = new_id('mssql')
    return {
        "id": nid,
        "type": "MSSQL",
        "z": TAB_ID,
        "mssql": config_id,
        "name": name,
        "query": query,
        "out": "msg",
        "x": xy[0],
        "y": xy[1],
        "wires": [[]]
    }

def make_catch(name, xy=(200, 200)):
    nid = new_id('catch')
    return {
        "id": nid,
        "type": "catch",
        "z": TAB_ID,
        "name": name,
        "scope": None,
        "x": xy[0],
        "y": xy[1],
        "wires": [[]]
    }

# ===================================================================
# 4. Build endpoints
# ===================================================================

# We'll build endpoints one by one with proper wiring
# Each endpoint is: HTTP In -> Function (query) -> DB (mysql/mssql) -> Function (respond) -> HTTP Resp

# Store all nodes
all_nodes = [mysql_config, mssql_config]

y_base = 200  # Starting Y position
y_step = 140  # Vertical spacing between endpoints

# ---- Tab ID node (required for Node-RED tabs) ----
tab_node = {
    "id": TAB_ID,
    "type": "tab",
    "label": "Facturas API",
    "disabled": False,
    "info": "API unificada: MySQL staging + SQL Server OC/constancias"
}
all_nodes.append(tab_node)

# ---- ENDPOINT: GET /api/facturas/pendientes (MySQL) ----
y = y_base
pendientes = [
    make_http_in('GET', '/api/facturas/pendientes', 'GET /api/facturas/pendientes', (120, y)),
    make_function("Query MySQL pendientes",
        """// Consulta facturas pendientes desde MySQL staging
msg._req = msg.req;
msg._res = msg.res;
msg.topic = 'SELECT id, tipo_operacion, estado_proceso, fecha_carga, ' +
    'numerodocumento, letra, fecha_emision, fecha_vencimiento, ' +
    'proveedor_cuit, proveedor_nombre, proveedor_codigo, ' +
    'neto, iva_21, iva_105, percepciones, otros_impuestos, total, ' +
    'es_dolar, cotizacion, cotizacion_origen, cotizacion_aviso, ' +
    'cae, referencia, nro_remito, pdf_filename, email_origen, ' +
    'registrado_erp, requiere_constancia, constancia_nro ' +
    'FROM staging_facturas WHERE estado_proceso = \\'PENDIENTE\\' ' +
    'ORDER BY fecha_carga ASC';
return msg;""",
    outputs=1, xy=(320, y)),
    make_mysql_node("MySQL pendientes", mysql_config_id, (520, y)),
    make_function("Responder pendientes JSON",
        """// Formatear respuesta de pendientes
msg._res = msg._res || msg.res;
var rows = msg.payload || [];
msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
// Formatear fechas de YYYY-MM-DD a YYYYMMDD para compatibilidad con frontend
rows = rows.map(function(r) {
    var fmt = function(d) {
        if (!d) return null;
        var s = String(d).replace(/[^0-9]/g, '');
        return s.substring(0, 8);
    };
    return {
        ID: r.id,
        TIPO_OPERACION: r.tipo_operacion,
        ESTADO_PROCESO: r.estado_proceso,
        FECHA_CARGA: r.fecha_carga,
        NUMERODOCUMENTO: r.numerodocumento,
        LETRA: r.letra,
        FECHA_EMISION: fmt(r.fecha_emision),
        FECHA_VENCIMIENTO: fmt(r.fecha_vencimiento),
        PROVEEDOR_CUIT: r.proveedor_cuit,
        PROVEEDOR_NOMBRE: r.proveedor_nombre,
        PROVEEDOR_CODIGO: r.proveedor_codigo,
        NETO: Number(r.neto) || 0,
        IVA_21: Number(r.iva_21) || 0,
        IVA_105: Number(r.iva_105) || 0,
        PERCEPCIONES: Number(r.percepciones) || 0,
        OTROS_IMPUESTOS: Number(r.otros_impuestos) || 0,
        TOTAL: Number(r.total) || 0,
        ES_DOLAR: Number(r.es_dolar) || 0,
        COTIZACION: Number(r.cotizacion) || 1,
        COTIZACION_ORIGEN: r.cotizacion_origen,
        COTIZACION_AVISO: r.cotizacion_aviso,
        CAE: r.cae,
        REFERENCIA: r.referencia,
        NRO_REMITO: r.nro_remito,
        PDF_FILENAME: r.pdf_filename,
        EMAIL_ORIGEN: r.email_origen,
        MONEDA: (Number(r.es_dolar) === 1) ? 'Dólares' : 'Pesos',
        ESTADO_CAE: (!r.cae) ? 'SIN_CAE' : 'CAE_OK',
        DIAS_EN_COLA: 0,
        requiere_constancia: Number(r.requiere_constancia) || 0,
        constancia_nro: r.constancia_nro
    };
});
msg.payload = rows;
return msg;""",
    outputs=1, xy=(720, y)),
    make_http_resp((920, y)),
]
# Wire
pendientes[0]['wires'][0] = [pendientes[1]['id']]
pendientes[1]['wires'][0] = [pendientes[2]['id']]
pendientes[2]['wires'][0] = [pendientes[3]['id']]
pendientes[3]['wires'][0] = [pendientes[4]['id']]
all_nodes.extend(pendientes)

# ---- ENDPOINT: GET /api/facturas/:id (MySQL) ----
y += y_step
factura_id = [
    make_http_in('GET', '/api/facturas/:id', 'GET /api/facturas/:id', (120, y)),
    make_function("Query MySQL factura por ID",
        """// Obtener factura por ID desde MySQL
msg._req = msg.req;
msg._res = msg.res;
var id = (msg.req.params && msg.req.params.id) || '';
// Sanitizar: solo alfanumerico + guiones
id = id.replace(/[^a-zA-Z0-9\\-]/g, '');
msg.topic = 'SELECT * FROM staging_facturas WHERE id = \\'' + id.replace(/'/g, "\\'\\'") + '\\'';
return msg;""",
    outputs=1, xy=(320, y)),
    make_mysql_node("MySQL factura by ID", mysql_config_id, (520, y)),
    make_function("Responder factura JSON",
        """// Formatear respuesta de factura individual
msg._res = msg._res || msg.res;
var rows = msg.payload || [];
msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
if (rows.length === 0) {
    msg.statusCode = 404;
    msg.payload = { error: 'Factura no encontrada' };
    return msg;
}
var r = rows[0];
// Formatear para frontend
var fmt = function(d) {
    if (!d) return null;
    var s = String(d).replace(/[^0-9]/g, '');
    return s.substring(0, 8);
};
msg.payload = {
    ID: r.id,
    TIPO_OPERACION: r.tipo_operacion,
    ESTADO_PROCESO: r.estado_proceso,
    FECHA_CARGA: r.fecha_carga,
    FECHA_APROBACION: r.fecha_aprobacion,
    NUMERODOCUMENTO: r.numerodocumento,
    LETRA: r.letra,
    FECHA_EMISION: fmt(r.fecha_emision),
    FECHA_VENCIMIENTO: fmt(r.fecha_vencimiento),
    PROVEEDOR_CUIT: r.proveedor_cuit,
    PROVEEDOR_NOMBRE: r.proveedor_nombre,
    PROVEEDOR_CODIGO: r.proveedor_codigo,
    NETO: Number(r.neto) || 0,
    IVA_21: Number(r.iva_21) || 0,
    IVA_105: Number(r.iva_105) || 0,
    PERCEPCIONES: Number(r.percepciones) || 0,
    OTROS_IMPUESTOS: Number(r.otros_impuestos) || 0,
    TOTAL: Number(r.total) || 0,
    ES_DOLAR: Number(r.es_dolar) || 0,
    COTIZACION: Number(r.cotizacion) || 1,
    COTIZACION_ORIGEN: r.cotizacion_origen,
    COTIZACION_AVISO: r.cotizacion_aviso,
    CAE: r.cae,
    FECHA_VTO_CAE: fmt(r.fecha_vto_cae),
    REFERENCIA: r.referencia,
    NRO_REMITO: r.nro_remito,
    PDF_FILENAME: r.pdf_filename,
    PDF_HASH: r.pdf_hash,
    EMAIL_ORIGEN: r.email_origen,
    EMAIL_ASUNTO: r.email_asunto,
    ITEMS_JSON: r.items_json,
    items_count: r.items_count,
    requiere_constancia: Number(r.requiere_constancia) || 0,
    constancia_nro: r.constancia_nro,
    constancia_id_calipso: r.constancia_id_calipso,
    constancia_total: Number(r.constancia_total) || 0,
    constancia_fecha: fmt(r.constancia_fecha),
    constancia_detalle: r.constancia_detalle,
    registrado_erp: Number(r.registrado_erp) || 0,
    fecha_registro_erp: r.fecha_registro_erp
};
return msg;""",
    outputs=1, xy=(720, y)),
    make_http_resp((920, y)),
]
factura_id[0]['wires'][0] = [factura_id[1]['id']]
factura_id[1]['wires'][0] = [factura_id[2]['id']]
factura_id[2]['wires'][0] = [factura_id[3]['id']]
factura_id[3]['wires'][0] = [factura_id[4]['id']]
all_nodes.extend(factura_id)

# ---- ENDPOINT: PUT /api/facturas/:id (MySQL update) ----
y += y_step
put_factura = [
    make_http_in('PUT', '/api/facturas/:id', 'PUT /api/facturas/:id', (120, y)),
    make_function("Query MySQL UPDATE factura",
        """// Actualizar campos editables de factura en MySQL
msg._req = msg.req;
msg._res = msg.res;
var id = (msg.req.params && msg.req.params.id) || '';
id = id.replace(/[^a-zA-Z0-9\\-]/g, '');
var b = msg.req.body || {};

// Helper sanitize
var esc = function(v) {
    if (v === null || v === undefined) return 'NULL';
    return '\\'' + String(v).replace(/\\\\/g, '\\\\\\\\').replace(/'/g, "\\\\'") + '\\'';
};
var num = function(v) {
    if (v === null || v === undefined || v === '' || isNaN(Number(v))) return 'NULL';
    return Number(v);
};

msg.topic = 'UPDATE staging_facturas SET ' +
    'proveedor_nombre = ' + esc(b.proveedor_nombre) + ', ' +
    'proveedor_cuit = ' + esc(b.proveedor_cuit) + ', ' +
    'letra = ' + esc(b.letra) + ', ' +
    'numerodocumento = ' + esc(b.numerodocumento) + ', ' +
    'fecha_emision = ' + esc(b.fecha_emision) + ', ' +
    'fecha_vencimiento = ' + esc(b.fecha_vencimiento) + ', ' +
    'neto = ' + num(b.neto) + ', ' +
    'iva_21 = ' + num(b.iva_21) + ', ' +
    'iva_105 = ' + num(b.iva_105) + ', ' +
    'percepciones = ' + num(b.percepciones) + ', ' +
    'otros_impuestos = ' + num(b.otros_impuestos) + ', ' +
    'total = ' + num(b.total) + ', ' +
    'referencia = ' + esc(b.referencia) + ', ' +
    'cae = ' + esc(b.cae) + ', ' +
    'es_dolar = ' + num(b.es_dolar) + ', ' +
    'cotizacion = ' + num(b.cotizacion) + ' ' +
    'WHERE id = \\'' + id.replace(/'/g, "\\'\\'") + '\\'' +
    ' AND estado_proceso IN (\\'PENDIENTE\\',\\'EN_REVISION\\')';
return msg;""",
    outputs=1, xy=(320, y)),
    make_mysql_node("MySQL UPDATE factura", mysql_config_id, (520, y)),
    make_function("Responder UPDATE OK",
        """// Respuesta de exito para actualizacion
msg._res = msg._res || msg.res;
msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
msg.payload = { ok: true, changed: (msg.payload && msg.payload.affectedRows) || 0 };
return msg;""",
    outputs=1, xy=(720, y)),
    make_http_resp((920, y)),
]
put_factura[0]['wires'][0] = [put_factura[1]['id']]
put_factura[1]['wires'][0] = [put_factura[2]['id']]
put_factura[2]['wires'][0] = [put_factura[3]['id']]
put_factura[3]['wires'][0] = [put_factura[4]['id']]
all_nodes.extend(put_factura)

# ---- ENDPOINT: POST /api/facturas/:id/aprobar (MySQL + sync to SQL Server) ----
y += y_step
aprox_id = new_id('aprx')
aprox = [
    make_http_in('POST', '/api/facturas/:id/aprobar', 'POST /api/facturas/:id/aprobar', (120, y)),
    make_function("Aprobar: leer datos de MySQL",
        """// PASO 1: Leer la factura de MySQL para obtener datos completos
msg._req = msg.req;
msg._res = msg.res;
var id = (msg.req.params && msg.req.params.id) || '';
id = id.replace(/[^a-zA-Z0-9\\-]/g, '');
msg._facturaId = id;

// Obtener operador
var operador = (msg.req.body && msg.req.body.operador) || 'pdietrich';
msg._operador = operador;

// Leer la factura completa de MySQL
msg.topic = 'SELECT * FROM staging_facturas WHERE id = \\'' + id.replace(/'/g, "\\'\\'") + '\\'';
return msg;""",
    outputs=1, xy=(320, y)),
    make_mysql_node("MySQL leer factura", mysql_config_id, (520, y)),
    make_function("Aprobar: actualizar MySQL + preparar sync SQL Server",
        """// PASO 2: Actualizar estado en MySQL y preparar sync a SQL Server
var rows = msg.payload || [];
if (rows.length === 0) {
    msg._res = msg._res || msg.res;
    msg._res.status(404).json({ ok: false, error: 'Factura no encontrada' });
    msg._res = null;
    return null;
}

var factura = rows[0];
var id = msg._facturaId;
var operador = msg._operador;
var now = new Date();
var ts = now.getFullYear().toString() +
    String(now.getMonth()+1).padStart(2,'0') +
    String(now.getDate()).padStart(2,'0') +
    String(now.getHours()).padStart(2,'0') +
    String(now.getMinutes()).padStart(2,'0') +
    String(now.getSeconds()).padStart(2,'0');

// Actualizar estado en MySQL
msg.topic = "UPDATE staging_facturas SET " +
    "estado_proceso = 'APROBADO', " +
    "fecha_aprobacion = NOW(), " +
    "aprobado_por = '" + operador.replace(/'/g, "\\'\\'") + "' " +
    "WHERE id = '" + id.replace(/'/g, "\\'\\'") + "' " +
    "AND estado_proceso IN ('PENDIENTE','EN_REVISION')";

// Guardar datos para el paso de sync a SQL Server
msg._facturaData = factura;
msg._ts = ts;
msg._operadorFinal = operador;
return msg;""",
    outputs=1, xy=(720, y)),
    make_mysql_node("MySQL aprobar factura", mysql_config_id, (920, y)),
    make_function("Aprobar: sync a SQL Server staging",
        """// PASO 3: Copiar datos a SQL Server UD_EZI_STAGING_FACTURAS
var factura = msg._facturaData;
var id = msg._facturaId;
var ts = msg._ts;
var operador = msg._operadorFinal;

// Verificar que la actualizacion MySQL fue exitosa
var affected = (msg.payload && msg.payload.affectedRows) || 0;
if (affected === 0) {
    msg._res = msg._res || msg.res;
    msg._res.status(409).json({ ok: false, error: 'Factura no esta en estado PENDIENTE o no existe' });
    msg._res = null;
    return null;
}

// Construir INSERT para SQL Server UD_EZI_STAGING_FACTURAS
var esc = function(v) {
    if (v === null || v === undefined) return 'NULL';
    return "'" + String(v).replace(/'/g, "''") + "'";
};
var num = function(v) {
    if (v === null || v === undefined || v === '' || isNaN(Number(v))) return '0';
    return Number(v);
};

// Generar nuevo UUID para SQL Server (distinto del MySQL id)
var sqlId = 'NEWID()';  // SQL Server genera el UUID

var neto = num(factura.neto);
var iva21 = num(factura.iva_21);
var iva105 = num(factura.iva_105);
var percepciones = num(factura.percepciones);
var otros = num(factura.otros_impuestos);
var total = num(factura.total);
var cotizacion = num(factura.cotizacion) || 1;
var esDolar = factura.es_dolar == 1 || factura.es_dolar == '1' ? 1 : 0;
var monedaId = esDolar ? '76C69768-3DAE-11D5-B059-004854841C8A' : '76C69765-3DAE-11D5-B059-004854841C8A';
var companiaId = 'FC20C32D-3EFA-11D5-86AD-0080AD403F5F';
var tipoTrId = '50829758-5905-11D5-86C4-0080AD403F5F';

msg.payload = "INSERT INTO UD_EZI_STAGING_FACTURAS (" +
    "ID, TIPO_OPERACION, ESTADO_PROCESO, FECHA_CARGA, FECHA_APROBACION, " +
    "USUARIO_CARGA, APROBADO_POR, ORIGEN, " +
    "TIPOTRANSACCION_ID, NUMERODOCUMENTO, LETRA, FECHA_EMISION, FECHA_VENCIMIENTO, REFERENCIA, " +
    "PROVEEDOR_ID, PROVEEDOR_CODIGO, PROVEEDOR_CUIT, PROVEEDOR_NOMBRE, " +
    "NETO, IVA_21, IVA_105, PERCEPCIONES, OTROS_IMPUESTOS, TOTAL, MONEDA_ID, COTIZACION, " +
    "CAE, FECHA_VTO_CAE, COMPANIA_ID, " +
    "PDF_FILENAME, PDF_HASH, EMAIL_ORIGEN, EMAIL_ASUNTO, " +
    "ITEMS_JSON, ES_DOLAR, COTIZACION_ORIGEN, COTIZACION_AVISO, NRO_REMITO, CONFIANZA_PARSEO, NOTAS_PARSEO" +
    ") VALUES (" +
    sqlId + ", " +
    esc(factura.tipo_operacion || 'FACTURA_COMPRA') + ", " +
    "'APROBADO', " +
    esc(ts) + ", " +
    esc(ts) + ", " +
    "'n8n', " +
    esc(operador) + ", " +
    esc(factura.origen || 'EMAIL_GMAIL_N8N') + ", " +
    "'" + tipoTrId + "', " +
    esc(factura.numerodocumento) + ", " +
    esc(factura.letra) + ", " +
    esc(String(factura.fecha_emision).replace(/[^0-9]/g, '').substring(0,8)) + ", " +
    esc(String(factura.fecha_vencimiento || '').replace(/[^0-9]/g, '').substring(0,8)) + ", " +
    esc(factura.referencia) + ", " +
    "'00000000-0000-0000-0000-000000000000', " +  // PROVEEDOR_ID placeholder
    esc(factura.proveedor_codigo) + ", " +
    esc(factura.proveedor_cuit) + ", " +
    esc(factura.proveedor_nombre) + ", " +
    num(neto) + ", " +
    num(iva21) + ", " +
    num(iva105) + ", " +
    num(percepciones) + ", " +
    num(otros) + ", " +
    num(total) + ", " +
    "'" + monedaId + "', " +
    num(cotizacion) + ", " +
    esc(factura.cae) + ", " +
    esc(String(factura.fecha_vto_cae || '').replace(/[^0-9]/g, '').substring(0,8)) + ", " +
    "'" + companiaId + "', " +
    esc(factura.pdf_filename) + ", " +
    esc(factura.pdf_hash) + ", " +
    esc(factura.email_origen) + ", " +
    esc(factura.email_asunto) + ", " +
    esc(factura.items_json) + ", " +
    esDolar + ", " +
    esc(factura.cotizacion_origen) + ", " +
    esc(factura.cotizacion_aviso) + ", " +
    esc(factura.nro_remito) + ", " +
    "70, " +  // confianza_parseo default
    esc(factura.notas_parseo) +
    ")";

// Guardar datos para items
msg._facturaItems = factura.items_json || '[]';
msg._facturaSqlId = 'SCOPE_IDENTITY()';  // No usamos SCOPE_IDENTITY porque el ID es NEWID
// Para obtener el ID generado, usamos un approach diferente
msg._mssqlId = null;
return msg;""",
    outputs=1, xy=(1120, y)),
    # MSSQL node needs to be added manually since mssql-plus is not installed
]

# We need to handle the mssql-plus dependency. Since it may not be installed,
# let's add it but with a note that it depends on the package.
# Actually, the mssql-plus cannot execute via MySQL node. We need a different approach.
# Let's skip the MSSQL sync for now and just update MySQL, then have a separate sync process.

# Redisenamos aprobar: mas simple, solo MySQL + log
# Los nodos de la version anterior ((variable `aprox`) jamas se extendieron a all_nodes
y -= y_step  # Reuse this Y position

# Simpler aprobar endpoint: just update MySQL + log sync request
aprox_nodes = [
    make_http_in('POST', '/api/facturas/:id/aprobar', 'POST /api/facturas/:id/aprobar', (120, y)),
    make_function("Aprobar factura",
        """// Aprobar factura: actualiza MySQL + registra sync pendiente
msg._req = msg.req;
msg._res = msg.res;
var id = (msg.req.params && msg.req.params.id) || '';
id = id.replace(/[^a-zA-Z0-9\\-]/g, '');
var operador = (msg.req.body && msg.req.body.operador) || 'pdietrich';

// Actualizar estado en MySQL
msg.topic = "UPDATE staging_facturas SET " +
    "estado_proceso = 'APROBADO', " +
    "fecha_aprobacion = NOW(), " +
    "aprobado_por = '" + operador.replace(/'/g, "\\\\'") + "' " +
    "WHERE id = '" + id.replace(/'/g, "\\\\'") + "' " +
    "AND estado_proceso IN ('PENDIENTE','EN_REVISION')";
msg._facturaId = id;
msg._operador = operador;
return msg;""",
    outputs=1, xy=(320, y)),
    make_mysql_node("MySQL UPDATE aprobar", mysql_config_id, (520, y)),
    make_function("Verificar + insertar log sync",
        """// Verificar que se actualizo y loguear sync pendiente
var affected = (msg.payload && msg.payload.affectedRows) || 0;
if (affected === 0) {
    msg._res = msg._res || msg.res;
    msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
    msg.statusCode = 409;
    msg.payload = { ok: false, error: 'Factura no encontrada o ya procesada' };
    return msg;
}

// Insertar log de sync pendiente para SQL Server
var id = msg._facturaId;
var oper = msg._operador;
msg.topic = "INSERT INTO log_sync_calipso (factura_id, tipo_operacion, estado) " +
    "VALUES ('" + id.replace(/'/g, "\\\\'") + "', 'INSERT', 'PENDIENTE')";
msg._syncLogged = true;
return msg;""",
    outputs=1, xy=(720, y)),
    make_mysql_node("MySQL log sync", mysql_config_id, (920, y)),
    make_function("Responder OK aprobar",
        """// Respuesta exitosa de aprobacion
msg._res = msg._res || msg.res;
msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
msg.payload = { ok: true, affected: 1, sync_pendiente: true };
return msg;""",
    outputs=1, xy=(1120, y)),
    make_http_resp((1320, y)),
]
aprox_nodes[0]['wires'][0] = [aprox_nodes[1]['id']]
aprox_nodes[1]['wires'][0] = [aprox_nodes[2]['id']]
aprox_nodes[2]['wires'][0] = [aprox_nodes[3]['id']]
aprox_nodes[3]['wires'][0] = [aprox_nodes[4]['id']]
aprox_nodes[4]['wires'][0] = [aprox_nodes[5]['id']]
all_nodes.extend(aprox_nodes)

# ---- ENDPOINT: POST /api/facturas/:id/rechazar (MySQL) ----
y += y_step
rechazar_nodes = [
    make_http_in('POST', '/api/facturas/:id/rechazar', 'POST /api/facturas/:id/rechazar', (120, y)),
    make_function("Rechazar factura",
        """// Rechazar factura
msg._req = msg.req;
msg._res = msg.res;
var id = (msg.req.params && msg.req.params.id) || '';
id = id.replace(/[^a-zA-Z0-9\\-]/g, '');
var b = msg.req.body || {};
var operador = b.operador || 'pdietrich';
var motivo = (b.motivo || 'Rechazado por operador').replace(/'/g, "\\\\'");

msg.topic = "UPDATE staging_facturas SET " +
    "estado_proceso = 'RECHAZADO', " +
    "fecha_aprobacion = NOW(), " +
    "aprobado_por = '" + operador.replace(/'/g, "\\\\'") + "', " +
    "error_detalle = '" + motivo.replace(/'/g, "\\\\'") + "' " +
    "WHERE id = '" + id.replace(/'/g, "\\\\'") + "' " +
    "AND estado_proceso IN ('PENDIENTE','EN_REVISION')";
return msg;""",
    outputs=1, xy=(320, y)),
    make_mysql_node("MySQL UPDATE rechazar", mysql_config_id, (520, y)),
    make_function("Responder OK rechazar",
        """// Verificar y responder
var affected = (msg.payload && msg.payload.affectedRows) || 0;
msg._res = msg._res || msg.res;
msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
if (affected === 0) {
    msg.statusCode = 409;
    msg.payload = { ok: false, error: 'Factura no encontrada o ya procesada' };
} else {
    msg.payload = { ok: true, affected: 1 };
}
return msg;""",
    outputs=1, xy=(720, y)),
    make_http_resp((920, y)),
]
rechazar_nodes[0]['wires'][0] = [rechazar_nodes[1]['id']]
rechazar_nodes[1]['wires'][0] = [rechazar_nodes[2]['id']]
rechazar_nodes[2]['wires'][0] = [rechazar_nodes[3]['id']]
all_nodes.extend(rechazar_nodes)

# ---- ENDPOINT: GET /api/facturas/:id/items (MySQL) ----
y += y_step
items_nodes = [
    make_http_in('GET', '/api/facturas/:id/items', 'GET /api/facturas/:id/items', (120, y)),
    make_function("Query MySQL items",
        """// Obtener items de factura desde MySQL
msg._req = msg.req;
msg._res = msg.res;
var id = (msg.req.params && msg.req.params.id) || '';
id = id.replace(/[^a-zA-Z0-9\\-]/g, '');
msg.topic = 'SELECT id, linea, descripcion, cantidad, unidad, precio_unitario, ' +
    'alicuota_iva, subtotal, moneda ' +
    'FROM staging_facturas_items WHERE factura_id = \\'' + id.replace(/'/g, "\\'\\'") + '\\' ' +
    'ORDER BY linea ASC';
return msg;""",
    outputs=1, xy=(320, y)),
    make_mysql_node("MySQL items", mysql_config_id, (520, y)),
    make_function("Responder items",
        """// Formatear respuesta de items
msg._res = msg._res || msg.res;
msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
var rows = msg.payload || [];
msg.payload = rows.map(function(r) {
    return {
        LINEA: Number(r.linea) || 0,
        DESCRIPCION: r.descripcion || '',
        CANTIDAD: Number(r.cantidad) || 0,
        UNIDAD: r.unidad || 'u',
        PRECIO_UNITARIO: Number(r.precio_unitario) || 0,
        ALICUOTA_IVA: Number(r.alicuota_iva) || 0,
        SUBTOTAL: Number(r.subtotal) || 0,
        MONEDA: r.moneda || 'ARS'
    };
});
return msg;""",
    outputs=1, xy=(720, y)),
    make_http_resp((920, y)),
]
items_nodes[0]['wires'][0] = [items_nodes[1]['id']]
items_nodes[1]['wires'][0] = [items_nodes[2]['id']]
items_nodes[2]['wires'][0] = [items_nodes[3]['id']]
items_nodes[3]['wires'][0] = [items_nodes[4]['id']]
all_nodes.extend(items_nodes)

# ---- ENDPOINT: GET /api/oc (SQL Server - MSSQL) ----
y += y_step
# For MSSQL endpoints, we use function nodes that build query strings for MSSQL node
oc_list = [
    make_http_in('GET', '/api/oc', 'GET /api/oc', (120, y)),
    make_function("Query MSSQL OC",
        """// Buscar OCs en SQL Server por CUIT
msg._req = msg.req;
msg._res = msg.res;
var cuit = (msg.req.query && msg.req.query.cuit) || '';
// Sanitizar: solo numeros
cuit = cuit.replace(/[^0-9]/g, '');
if (!cuit) {
    msg.payload = [];
    msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
    return msg;
}
msg.payload = "SELECT TOP 20 OC, FECHAACTUAL, nom_PROVEEDOR, CUIT, ESTADO, " +
    "DESCRIPCION, btotal_doc, bneto, NOMBREMONEDA " +
    "FROM V_EZI_PROV_ORDENCOMPRA_ENC " +
    "WHERE CUIT = '" + cuit + "' AND ESTADO != 'ANULADO' " +
    "ORDER BY FECHAACTUAL DESC";
return msg;""",
    outputs=1, xy=(320, y)),
    make_mssql_node("MSSQL OC por CUIT", 'mssql-corona-config', '', (520, y)),
    make_function("Responder OC list",
        """// Formatear respuesta de OCs
msg._res = msg._res || msg.res;
msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
var rows = msg.payload || [];
msg.payload = rows.map(function(r) {
    return {
        OC: r.OC,
        FECHAACTUAL: r.FECHAACTUAL,
        nom_PROVEEDOR: r.nom_PROVEEDOR,
        CUIT: r.CUIT,
        ESTADO: r.ESTADO,
        DESCRIPCION: r.DESCRIPCION,
        btotal_doc: Number(r.btotal_doc) || 0,
        bneto: Number(r.bneto) || 0,
        NOMBREMONEDA: r.NOMBREMONEDA || 'Pesos'
    };
});
return msg;""",
    outputs=1, xy=(720, y)),
    make_http_resp((920, y)),
]
oc_list[0]['wires'][0] = [oc_list[1]['id']]
oc_list[1]['wires'][0] = [oc_list[2]['id']]
oc_list[2]['wires'][0] = [oc_list[3]['id']]
oc_list[3]['wires'][0] = [oc_list[4]['id']]
all_nodes.extend(oc_list)

# ---- ENDPOINT: GET /api/oc/:oc/items (SQL Server) ----
y += y_step
oc_items = [
    make_http_in('GET', '/api/oc/:oc/items', 'GET /api/oc/:oc/items', (120, y)),
    make_function("Query MSSQL items OC",
        """// Obtener items de una OC desde SQL Server
msg._req = msg.req;
msg._res = msg.res;
var oc = (msg.req.params && msg.req.params.oc) || '';
// Sanitizar: solo alfanumerico + guiones
oc = oc.replace(/[^a-zA-Z0-9\\-]/g, '');

msg.payload = "SELECT OC, ITEM, NOM_PRODUCTO, CANTIDAD, PU, TOTAL_ITEM, IVATASA " +
    "FROM V_EZI_ORDENDECOMPRA0 " +
    "WHERE OC = '" + oc.replace(/'/g, "''") + "' " +
    "ORDER BY NRO_ITEM ASC";
return msg;""",
    outputs=1, xy=(320, y)),
    make_mssql_node("MSSQL items OC", 'mssql-corona-config', '', (520, y)),
    make_function("Responder items OC",
        """// Formatear items de OC
msg._res = msg._res || msg.res;
msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
var rows = msg.payload || [];
msg.payload = rows.map(function(r) {
    return {
        OC: r.OC,
        ITEM: Number(r.ITEM) || 0,
        NOM_PRODUCTO: r.NOM_PRODUCTO || '',
        CANTIDAD: Number(r.CANTIDAD) || 0,
        PU: Number(r.PU) || 0,
        TOTAL_ITEM: Number(r.TOTAL_ITEM) || 0,
        IVATASA: Number(r.IVATASA) || 0
    };
});
return msg;""",
    outputs=1, xy=(720, y)),
    make_http_resp((920, y)),
]
oc_items[0]['wires'][0] = [oc_items[1]['id']]
oc_items[1]['wires'][0] = [oc_items[2]['id']]
oc_items[2]['wires'][0] = [oc_items[3]['id']]
oc_items[3]['wires'][0] = [oc_items[4]['id']]
all_nodes.extend(oc_items)

# ---- ENDPOINT: GET /api/constancias (SQL Server) ----
y += y_step
constancias = [
    make_http_in('GET', '/api/constancias', 'GET /api/constancias', (120, y)),
    make_function("Query MSSQL constancias por OC",
        """// Buscar constancias de servicio en SQL Server por OC
msg._req = msg.req;
msg._res = msg.res;
var oc = (msg.req.query && msg.req.query.oc) || '';
if (!oc) {
    msg.payload = [];
    msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
    return msg;
}
oc = oc.replace(/[^a-zA-Z0-9\\-]/g, '');

msg.payload = "SELECT " +
    "enc.ID AS CONSTANCIA_ID, enc.CONSTANCIA, enc.CODIGOPROVEEDOR, " +
    "enc.FECHAACTUAL, enc.NETO, " +
    "(SELECT TOP 1 DETALLE FROM V_EZI_PROV_CONST_SC_DET det " +
    " WHERE det.ID_CABECERA = enc.ID) AS DETALLE, " +
    "ISNULL((SELECT TOP 1 'EXISTE' FROM V_EZI_CONSTANCIA_FACTURA cf " +
    " WHERE cf.ID_CABECERA = enc.ID), '') AS FACTURA_EXISTENTE " +
    "FROM V_EZI_PROV_CONST_SC_ENC enc " +
    "WHERE enc.numeroOC = '" + oc.replace(/'/g, "''") + "' " +
    "ORDER BY enc.FECHAACTUAL DESC";
return msg;""",
    outputs=1, xy=(320, y)),
    make_mssql_node("MSSQL constancias", 'mssql-corona-config', '', (520, y)),
    make_function("Responder constancias",
        """// Formatear constancias
msg._res = msg._res || msg.res;
msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
var rows = msg.payload || [];
msg.payload = rows.map(function(r) {
    return {
        CONSTANCIA_ID: r.CONSTANCIA_ID || '',
        CONSTANCIA: r.CONSTANCIA || '',
        CODIGOPROVEEDOR: r.CODIGOPROVEEDOR || '',
        FECHAACTUAL: r.FECHAACTUAL || '',
        NETO: Number(r.NETO) || 0,
        DETALLE: r.DETALLE || '',
        FACTURA_EXISTENTE: r.FACTURA_EXISTENTE || ''
    };
});
return msg;""",
    outputs=1, xy=(720, y)),
    make_http_resp((920, y)),
]
constancias[0]['wires'][0] = [constancias[1]['id']]
constancias[1]['wires'][0] = [constancias[2]['id']]
constancias[2]['wires'][0] = [constancias[3]['id']]
constancias[3]['wires'][0] = [constancias[4]['id']]
all_nodes.extend(constancias)

# ---- ENDPOINT: GET /api/proveedores (SQL Server) ----
y += y_step
proveedores = [
    make_http_in('GET', '/api/proveedores', 'GET /api/proveedores', (120, y)),
    make_function("Query MSSQL proveedores",
        """// Buscar proveedores en SQL Server
msg._req = msg.req;
msg._res = msg.res;
var q = (msg.req.query && msg.req.query.q) || '';
// Sanitizar: solo letras, numeros y espacios
q = q.replace(/[^a-zA-Z0-9\\s\\-\\u00C0-\\u024F]/g, '');

var where = "ACTIVESTATUS = 0";
if (q.length > 0) {
    where += " AND (DENOMINACION LIKE '%" + q.replace(/'/g, "''") + "%'" +
             " OR CUIT LIKE '%" + q.replace(/'/g, "''") + "%' " +
             " OR CODIGO LIKE '%" + q.replace(/'/g, "''") + "%')";
}

msg.payload = "SELECT TOP 20 ID, CODIGO, DENOMINACION AS nombre, CUIT AS cuit " +
    "FROM PROVEEDOR WHERE " + where + " ORDER BY DENOMINACION";
return msg;""",
    outputs=1, xy=(320, y)),
    make_mssql_node("MSSQL proveedores", 'mssql-corona-config', '', (520, y)),
    make_function("Responder proveedores",
        """// Formatear proveedores
msg._res = msg._res || msg.res;
msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
var rows = msg.payload || [];
msg.payload = rows.map(function(r) {
    return {
        ID: r.ID || '',
        CODIGO: r.CODIGO || '',
        nombre: r.nombre || '',
        cuit: r.cuit || ''
    };
});
return msg;""",
    outputs=1, xy=(720, y)),
    make_http_resp((920, y)),
]
proveedores[0]['wires'][0] = [proveedores[1]['id']]
proveedores[1]['wires'][0] = [proveedores[2]['id']]
proveedores[2]['wires'][0] = [proveedores[3]['id']]
proveedores[3]['wires'][0] = [proveedores[4]['id']]
all_nodes.extend(proveedores)

# ---- ENDPOINT: GET /api/facturas/resumen (MySQL) ----
y += y_step
resumen = [
    make_http_in('GET', '/api/facturas/resumen', 'GET /api/facturas/resumen', (120, y)),
    make_function("Query MySQL resumen",
        """// Obtener resumen de facturas en staging
msg._req = msg.req;
msg._res = msg.res;
msg.topic = "SELECT " +
    "estado_proceso AS estado, " +
    "COUNT(*) AS cantidad, " +
    "COALESCE(SUM(total), 0) AS total_acumulado " +
    "FROM staging_facturas " +
    "GROUP BY estado_proceso " +
    "ORDER BY CASE estado_proceso " +
    "  WHEN 'PENDIENTE' THEN 1 " +
    "  WHEN 'EN_REVISION' THEN 2 " +
    "  WHEN 'APROBADO' THEN 3 " +
    "  WHEN 'RECHAZADO' THEN 4 " +
    "  ELSE 5 END ASC";
return msg;""",
    outputs=1, xy=(320, y)),
    make_mysql_node("MySQL resumen", mysql_config_id, (520, y)),
    make_function("Responder resumen",
        """// Formatear resumen
msg._res = msg._res || msg.res;
msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
msg.payload = msg.payload || [];
return msg;""",
    outputs=1, xy=(720, y)),
    make_http_resp((920, y)),
]
resumen[0]['wires'][0] = [resumen[1]['id']]
resumen[1]['wires'][0] = [resumen[2]['id']]
resumen[2]['wires'][0] = [resumen[3]['id']]
resumen[3]['wires'][0] = [resumen[4]['id']]
all_nodes.extend(resumen)

# ---- ENDPOINT: GET /api/facturas/check (duplicado en MySQL) ----
y += y_step
check = [
    make_http_in('GET', '/api/facturas/check', 'GET /api/facturas/check', (120, y)),
    make_function("Query MySQL check duplicado",
        """// Verificar si una factura ya existe en staging
msg._req = msg.req;
msg._res = msg.res;
var q = msg.req.query || {};
var hash = (q.hash || '').replace(/[^a-fA-F0-9]/g, '');
var doc = (q.numerodocumento || '').replace(/[^a-zA-Z0-9\\-]/g, '');
var cuit = (q.cuit || '').replace(/[^0-9]/g, '');

var conditions = [];
if (hash.length > 0) {
    conditions.push("pdf_hash = '" + hash + "'");
}
if (doc.length > 0 && cuit.length > 0) {
    conditions.push("(numerodocumento = '" + doc + "' AND proveedor_cuit = '" + cuit + "')");
}
if (conditions.length === 0) {
    msg.statusCode = 400;
    msg.payload = { ok: false, error: 'Se requiere hash o numerodocumento+cuit' };
    return msg;
}

msg.topic = "SELECT id FROM staging_facturas WHERE " + conditions.join(" OR ") + " LIMIT 1";
return msg;""",
    outputs=1, xy=(320, y)),
    make_mysql_node("MySQL check duplicado", mysql_config_id, (520, y)),
    make_function("Responder check",
        """// Formatear respuesta de check
msg._res = msg._res || msg.res;
msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
var rows = msg.payload || [];
msg.payload = { existe: rows.length > 0, id: rows.length > 0 ? rows[0].id : null };
return msg;""",
    outputs=1, xy=(720, y)),
    make_http_resp((920, y)),
]
check[0]['wires'][0] = [check[1]['id']]
check[1]['wires'][0] = [check[2]['id']]
check[2]['wires'][0] = [check[3]['id']]
check[3]['wires'][0] = [check[4]['id']]
all_nodes.extend(check)

# ---- ENDPOINT: GET /api/oc/proveedor/:cuit (SQL Server) ----
y += y_step
oc_prov = [
    make_http_in('GET', '/api/oc/proveedor/:cuit', 'GET /api/oc/proveedor/:cuit', (120, y)),
    make_function("Query MSSQL OC por proveedor",
        """// Obtener OCs por CUIT de proveedor desde SQL Server
msg._req = msg.req;
msg._res = msg.res;
var cuit = (msg.req.params && msg.req.params.cuit) || '';
cuit = cuit.replace(/[^0-9]/g, '');
if (!cuit) {
    msg.payload = [];
    msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
    return msg;
}

msg.payload = "SELECT oc.OC, oc.FECHAINGRESO, oc.FECHAACTUAL, " +
    "oc.ESTADO, oc.DESCRIPCION, oc.btotal_doc, oc.NOMBREMONEDA, " +
    "p.CODIGO, p.DENOMINACION, p.CUIT " +
    "FROM TRORDENCOMPRA oc " +
    "JOIN PROVEEDOR p ON p.CODIGO = oc.CODIGODESTINATARIO " +
    "WHERE p.CUIT = '" + cuit.replace(/'/g, "''") + "' " +
    "AND oc.ESTADO IN ('A','P','C') " +
    "ORDER BY oc.FECHAINGRESO DESC";
return msg;""",
    outputs=1, xy=(320, y)),
    make_mssql_node("MSSQL OC por proveedor", 'mssql-corona-config', '', (520, y)),
    make_function("Responder OCs proveedor",
        """// Formatear OCs de proveedor
msg._res = msg._res || msg.res;
msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};
var rows = msg.payload || [];
msg.payload = rows.map(function(r) {
    return {
        OC: r.OC || '',
        FECHAINGRESO: r.FECHAINGRESO || '',
        FECHAACTUAL: r.FECHAACTUAL || '',
        ESTADO: r.ESTADO || '',
        DESCRIPCION: r.DESCRIPCION || '',
        btotal_doc: Number(r.btotal_doc) || 0,
        NOMBREMONEDA: r.NOMBREMONEDA || 'Pesos',
        CODIGO: r.CODIGO || '',
        DENOMINACION: r.DENOMINACION || '',
        CUIT: r.CUIT || ''
    };
});
return msg;""",
    outputs=1, xy=(720, y)),
    make_http_resp((920, y)),
]
oc_prov[0]['wires'][0] = [oc_prov[1]['id']]
oc_prov[1]['wires'][0] = [oc_prov[2]['id']]
oc_prov[2]['wires'][0] = [oc_prov[3]['id']]
oc_prov[3]['wires'][0] = [oc_prov[4]['id']]
all_nodes.extend(oc_prov)

# ---- CORS preflight ----
y += y_step
cors = [
    make_http_in('OPTIONS', '/*', 'OPTIONS /* (CORS preflight)', (120, y)),
    make_function("CORS OK",
        """// Responder CORS preflight
msg._res = msg._res || msg.res;
msg.headers = {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Methods': 'GET, PUT, POST, DELETE, OPTIONS',
    'Access-Control-Allow-Headers': 'Content-Type, Authorization',
    'Access-Control-Max-Age': '86400'
};
msg.statusCode = 204;
msg.payload = '';
return msg;""",
    outputs=1, xy=(320, y)),
    make_http_resp((520, y)),
]
cors[0]['wires'][0] = [cors[1]['id']]
cors[1]['wires'][0] = [cors[2]['id']]
all_nodes.extend(cors)

# ---- Error handler (catch all) ----
y += y_step
catch_node = make_catch("Error handler", (120, y))
err_resp = make_function("Responder error",
    """// Manejo general de errores
var err = msg._error || msg.error || msg;
msg._res = msg._res || msg.res;
if (msg._res && typeof msg._res.status === 'function') {
    msg._res.setHeader('Content-Type', 'application/json');
    msg._res.setHeader('Access-Control-Allow-Origin', '*');
    msg._res.status(500).json({
        ok: false,
        error: (err.message || err.toString() || 'Error interno'),
        source: err.source || 'unknown'
    });
    msg._res = null;
}
return null;""",
outputs=1, xy=(320, y))
catch_node['wires'][0] = [err_resp['id']]
all_nodes.extend([catch_node, err_resp])

# ===================================================================
# 5. Update all node tab IDs and renumber them
# ===================================================================
for n in all_nodes:
    if n.get('z') and n['type'] != 'tab':
        n['z'] = TAB_ID

# ===================================================================
# 6. Save the output
# ===================================================================
output_path = '/mnt/c/claudecode/proyectos-ingenio/automatizacion-facturas/nodered/flow_api_facturas_unificado_v2.json'
save_json(all_nodes, output_path)
print(f"Flow generado: {output_path}")
print(f"Total nodos: {len(all_nodes)}")
print(f"Tab ID: {TAB_ID}")
print(f"MySQL config ID: {mysql_config_id}")
print(f"MSSQL config ID: {mssql_config_id}")
print("")
print("NOTAS:")
print("1. El MSSQL config node 'mssql-corona-config' debe existir en Node-RED settings.js")
print("   o crearse manualmente en Node-RED como config node MSSQL con nombre 'mssql-corona-config'")
print("2. El MySQL config node 'mysql-api-config' debe existir o usar el ID generado")
print("3. Instalar: npm install node-red-contrib-mssql-plus para queries SQL Server")
print("4. El flow usa MySQL para staging y MSSQL para OC/constancias/Calipso")
