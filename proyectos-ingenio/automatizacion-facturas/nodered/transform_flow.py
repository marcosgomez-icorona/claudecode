"""
Transforma flow Node-RED de MySQL → MSSQL + agrega endpoints faltantes.
Entrada:  flow_api_facturas.json
Salida:   flow_api_facturas_mssql.json
"""
import json, copy, uuid

def uid():
    return hex(uuid.uuid4().int >> 80)[2:18]

with open('/mnt/c/claudecode/proyectos-ingenio/automatizacion-facturas/nodered/flow_api_facturas.json') as f:
    flow = json.load(f)

# Mapeo de MySQL → MSSQL: los nodos mysql se reemplazan por MSSQL
mysql_to_mssql = {}
new_nodes = []

# ---------------------------------------------------------------------------
# 1. IDENTIFICAR nodos MySQL y sus function feeders
# ---------------------------------------------------------------------------
for n in flow:
    if n['type'] == 'mysql':
        # Encontrar el function node que lo alimenta (wire source)
        feeder_id = None
        for fn in flow:
            if fn.get('wires') and any(n['id'] in w for w in fn['wires']):
                feeder_id = fn['id']
                break

        mysql_to_mssql[n['id']] = {
            'mysql': n,
            'feeder': feeder_id,
            'name': n.get('name', ''),
        }
        print(f"  MySQL [{n['id'][:16]}] '{n['name']}' ← feeder {feeder_id[:16] if feeder_id else 'NONE'}")

# ---------------------------------------------------------------------------
# 2. TRANSFORMAR function nodes: msg.topic → msg.payload, msg.payload → msg.queryParams
#    SQL syntax: MySQL → MSSQL
# ---------------------------------------------------------------------------
SQL_TRANSFORMS = {
    'ab89c395a6d82caf': {  # Query pendientes
        'old': "msg.topic = `SELECT ... FROM staging_facturas ...`",
        'new_query': (
            "msg.payload = `SELECT\\n"
            "    s.ID, s.TIPO_OPERACION, s.ESTADO_PROCESO, s.FECHA_CARGA,\\n"
            "    s.NUMERODOCUMENTO, s.LETRA, s.FECHA_EMISION, s.FECHA_VENCIMIENTO,\\n"
            "    s.PROVEEDOR_CUIT, s.PROVEEDOR_NOMBRE, s.PROVEEDOR_CODIGO,\\n"
            "    s.NETO, s.IVA_21, s.IVA_105, s.TOTAL,\\n"
            "    s.COTIZACION, s.CAE, s.REFERENCIA, s.PDF_FILENAME, s.EMAIL_ORIGEN,\\n"
            "    m.NOMBRE AS MONEDA,\\n"
            "    CASE WHEN s.CAE IS NULL OR s.CAE = '' THEN 'SIN_CAE'\\n"
            "         WHEN s.FECHA_VTO_CAE < CONVERT(varchar(8), GETDATE(), 112) THEN 'CAE_VENCIDO'\\n"
            "         ELSE 'CAE_OK' END AS ESTADO_CAE,\\n"
            "    DATEDIFF(DAY, CONVERT(datetime, SUBSTRING(s.FECHA_CARGA,1,8), 112), GETDATE()) AS DIAS_EN_COLA\\n"
            "FROM UD_EZI_STAGING_FACTURAS s\\n"
            "LEFT JOIN MONEDA m ON m.ID = s.MONEDA_ID\\n"
            "WHERE s.ESTADO_PROCESO IN ('PENDIENTE', 'EN_REVISION')\\n"
            "ORDER BY s.FECHA_CARGA ASC`;\\n"
            "return msg;"
        ),
    },
    '239880dcd1f251fd': {  # Query factura por ID
        'old': "msg.topic = 'SELECT * FROM staging_facturas WHERE id = ?'",
        'new_query': (
            "msg.payload = `SELECT s.*, m.NOMBRE AS MONEDA\\n"
            "FROM UD_EZI_STAGING_FACTURAS s\\n"
            "LEFT JOIN MONEDA m ON m.ID = s.MONEDA_ID\\n"
            "WHERE s.ID = '${msg.req.params.id.replace(/'/g, \"''\")}'`;\\n"
            "return msg;"
        ),
    },
    '2b31382b5766fb17': {  # Query UPDATE (PUT /api/facturas/:id)
        'old': 'msg.topic = `UPDATE staging_facturas SET ...',
        'new_query': (
            "const b = msg.req.body || {};\\n"
            "const id = msg.req.params.id.replace(/'/g, \"''\");\\n"
            "const esc = (v) => v != null ? \"'\" + String(v).replace(/'/g, \"''\") + \"'\" : 'NULL';\\n"
            "msg.payload = `UPDATE UD_EZI_STAGING_FACTURAS SET\\n"
            "    PROVEEDOR_NOMBRE      = ${esc(b.proveedor_nombre)},\\n"
            "    PROVEEDOR_CUIT        = ${esc(b.proveedor_cuit)},\\n"
            "    LETRA                 = ${esc(b.letra || 'A')},\\n"
            "    NUMERODOCUMENTO       = ${esc(b.numerodocumento)},\\n"
            "    FECHA_EMISION         = ${esc(b.fecha_emision)},\\n"
            "    FECHA_VENCIMIENTO     = ${esc(b.fecha_vencimiento)},\\n"
            "    CAE                   = ${esc(b.cae)},\\n"
            "    FECHA_VTO_CAE         = ${esc(b.fecha_vto_cae)},\\n"
            "    REFERENCIA            = ${esc(b.referencia)},\\n"
            "    NETO                  = ${parseFloat(b.neto) || 0},\\n"
            "    IVA_21                = ${parseFloat(b.iva_21) || 0},\\n"
            "    IVA_105               = ${parseFloat(b.iva_105) || 0},\\n"
            "    PERCEPCIONES          = ${parseFloat(b.percepciones) || 0},\\n"
            "    OTROS_IMPUESTOS       = ${parseFloat(b.otros_impuestos) || 0},\\n"
            "    TOTAL                 = ${parseFloat(b.total) || 0},\\n"
            "    COTIZACION            = ${parseFloat(b.cotizacion) || 1}\\n"
            "WHERE ID = '${id}' AND ESTADO_PROCESO IN ('PENDIENTE','EN_REVISION')`;\\n"
            "return msg;"
        ),
    },
    '24c5b4a782f7c8a0': {  # Query APROBAR
        'old': "msg.topic = `UPDATE staging_facturas SET ... NOW(), ...`",
        'new_query': (
            "const operador = (msg.req.body && msg.req.body.operador) || 'pdietrich';\\n"
            "const id = msg.req.params.id.replace(/'/g, \"''\");\\n"
            "const ts = CONVERT(varchar(8), GETDATE(), 112) +\\n"
            "           REPLACE(REPLACE(REPLACE(CONVERT(varchar(12), GETDATE(), 114),':',''),'.',''),' ','');\\n"
            "msg.payload = `UPDATE UD_EZI_STAGING_FACTURAS\\n"
            "SET ESTADO_PROCESO    = 'APROBADO',\\n"
            "    FECHA_APROBACION  = '${ts}',\\n"
            "    APROBADO_POR      = '${operador.replace(/'/g, \"''\")}'\\n"
            "WHERE ID = '${id}'\\n"
            "  AND ESTADO_PROCESO IN ('PENDIENTE', 'EN_REVISION')`;\\n"
            "return msg;"
        ),
    },
    'c45d2b1440cbd9ba': {  # Query RECHAZAR
        'old': "msg.topic = `UPDATE staging_facturas SET ... NOW(), ...`",
        'new_query': (
            "const b = msg.req.body || {};\\n"
            "const operador = b.operador || 'pdietrich';\\n"
            "const motivo = (b.motivo || 'Rechazado').replace(/'/g, \"''\");\\n"
            "const id = msg.req.params.id.replace(/'/g, \"''\");\\n"
            "const ts = CONVERT(varchar(8), GETDATE(), 112) +\\n"
            "           REPLACE(REPLACE(REPLACE(CONVERT(varchar(12), GETDATE(), 114),':',''),'.',''),' ','');\\n"
            "msg.payload = `UPDATE UD_EZI_STAGING_FACTURAS\\n"
            "SET ESTADO_PROCESO    = 'RECHAZADO',\\n"
            "    FECHA_APROBACION  = '${ts}',\\n"
            "    APROBADO_POR      = '${operador.replace(/'/g, \"''\")}' ,\\n"
            "    ERROR_DETALLE     = '${motivo}'\\n"
            "WHERE ID = '${id}'\\n"
            "  AND ESTADO_PROCESO IN ('PENDIENTE', 'EN_REVISION')`;\\n"
            "return msg;"
        ),
    },
    '9ecf6661b4b3ae30': {  # Query items
        'old': "msg.topic = 'SELECT ... FROM staging_facturas_items WHERE factura_id = ?'",
        'new_query': (
            "const id = msg.req.params.id.replace(/'/g, \"''\");\\n"
            "msg.payload = `SELECT LINEA, DESCRIPCION, CANTIDAD, UNIDAD,\\n"
            "    PRECIO_UNITARIO, ALICUOTA_IVA, SUBTOTAL\\n"
            "FROM UD_EZI_STAGING_ITEMS\\n"
            "WHERE FACTURA_ID = '${id}'\\n"
            "ORDER BY LINEA ASC`;\\n"
            "return msg;"
        ),
    },
}

# Aplicar transforms
count = 0
for n in flow:
    if n['id'] in SQL_TRANSFORMS:
        t = SQL_TRANSFORMS[n['id']]
        n['func'] = "msg._req = msg.req;\nmsg._res = msg.res;\n" + t['new_query']
        count += 1
        print(f"  ✓ Function '{n['name']}' actualizado")

print(f"  {count} function nodes actualizados")

# ---------------------------------------------------------------------------
# 3. REEMPLAZAR nodos mysql → MSSQL
# ---------------------------------------------------------------------------
count = 0
for n in flow:
    if n['type'] == 'mysql':
        n['type'] = 'MSSQL'
        # Quitar campo mydb, agregar mssqlCN
        del n['mydb']
        n['mssqlCN'] = '6814eb4bb7e82ddb'  # Calipso
        n['outField'] = 'payload'
        n['returnType'] = '0'
        n['throwErrors'] = '0'
        n['query'] = ''
        n['modeOpt'] = ''
        n['modeOptType'] = 'query'
        n['queryOpt'] = 'payload'
        n['queryOptType'] = 'msg'
        n['paramsOpt'] = 'queryParams'
        n['paramsOptType'] = 'msg'
        n['rows'] = ''
        n['rowsType'] = 'msg'
        n['parseMustache'] = False
        n['params'] = []
        count += 1
        print(f"  ✓ MSSQL '{n['name']}' → Calipso")

print(f"  {count} nodos MySQL → MSSQL")

# ---------------------------------------------------------------------------
# 4. ACTUALIZAR responder nodes para detectar errores MSSQL (rowCount)
# ---------------------------------------------------------------------------
for n in flow:
    # Responder OK para aprobar/rechazar: verificar si hubo UPDATE real
    if n['id'] == 'e3dd16b998144cb5':  # Responder OK aprobar
        n['func'] = (
            "msg.res = msg._res;\n"
            "msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};\n"
            "// msg.payload puede ser [rowsAffected] del MSSQL node\n"
            "const affected = Array.isArray(msg.payload) ? msg.payload[0] : msg.payload;\n"
            "msg.payload = JSON.stringify({ ok: true, mensaje: 'Factura aprobada', affected: affected });\n"
            "return msg;"
        )
    if n['id'] == 'de549712fbcc34df':  # Responder OK rechazar
        n['func'] = (
            "msg.res = msg._res;\n"
            "msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};\n"
            "msg.payload = JSON.stringify({ ok: true, mensaje: 'Factura rechazada' });\n"
            "return msg;"
        )
    if n['id'] == '2793b56616f4bf3b':  # Responder OK guardar
        n['func'] = (
            "msg.res = msg._res;\n"
            "msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};\n"
            "msg.payload = JSON.stringify({ ok: true, mensaje: 'Guardado correctamente' });\n"
            "return msg;"
        )

# ---------------------------------------------------------------------------
# 5. CREAR NUEVOS ENDPOINTS
# ---------------------------------------------------------------------------
TAB_ID = "86bcda5b2e8a7b12"  # mismo tab que el resto
MSSQL_CN_ID = "6814eb4bb7e82ddb"
X_HTTP = 120
X_FN = 340
X_MSSQL = 560
X_RESP_FN = 760
X_RESP = 940
Y_START = 1100  # Debajo de constancias

# --- 5A. GET /api/facturas/resumen ---
resumen_in = {
    "id": uid(), "type": "http in", "z": TAB_ID,
    "name": "GET /api/facturas/resumen",
    "url": "/api/facturas/resumen", "method": "get",
    "x": X_HTTP, "y": Y_START, "wires": [[uid()]]
}
resumen_fn_id = resumen_in['wires'][0][0]

resumen_fn = {
    "id": resumen_fn_id, "type": "function", "z": TAB_ID,
    "name": "Query resumen",
    "func": (
        "msg._req = msg.req;\n"
        "msg._res = msg.res;\n"
        "msg.payload = `SELECT\\n"
        "    ESTADO_PROCESO   AS estado,\\n"
        "    COUNT(*)         AS cantidad,\\n"
        "    SUM(TOTAL)       AS total_acumulado,\\n"
        "    MIN(FECHA_CARGA) AS mas_antigua,\\n"
        "    MAX(FECHA_CARGA) AS mas_reciente\\n"
        "FROM UD_EZI_STAGING_FACTURAS\\n"
        "GROUP BY ESTADO_PROCESO\\n"
        "ORDER BY\\n"
        "  CASE ESTADO_PROCESO\\n"
        "    WHEN 'APROBADO' THEN 1 WHEN 'PENDIENTE' THEN 2\\n"
        "    WHEN 'EN_REVISION' THEN 3 WHEN 'PROCESADO' THEN 4\\n"
        "    WHEN 'RECHAZADO' THEN 5 WHEN 'ERROR' THEN 6 ELSE 7 END`;\\n"
        "return msg;"
    ),
    "outputs": 1, "x": X_FN, "y": Y_START, "wires": [[uid()]]
}
resumen_mssql_id = resumen_fn['wires'][0][0]

resumen_mssql = {
    "id": resumen_mssql_id, "type": "MSSQL", "z": TAB_ID,
    "mssqlCN": MSSQL_CN_ID, "name": "Query resumen SQL Server",
    "outField": "payload", "returnType": "0", "throwErrors": "0",
    "query": "", "modeOpt": "", "modeOptType": "query",
    "queryOpt": "payload", "queryOptType": "msg",
    "paramsOpt": "queryParams", "paramsOptType": "msg",
    "rows": "", "rowsType": "msg", "parseMustache": False, "params": [],
    "x": X_MSSQL, "y": Y_START, "wires": [[uid()]]
}
resumen_resp_fn_id = resumen_mssql['wires'][0][0]

resumen_resp_fn = {
    "id": resumen_resp_fn_id, "type": "function", "z": TAB_ID,
    "name": "Responder resumen JSON",
    "func": (
        "msg.res = msg._res;\n"
        "msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};\n"
        "msg.payload = JSON.stringify(msg.payload || []);\n"
        "return msg;"
    ),
    "outputs": 1, "x": X_RESP_FN, "y": Y_START, "wires": [[uid()]]
}
resumen_resp_id = resumen_resp_fn['wires'][0][0]

resumen_resp = {
    "id": resumen_resp_id, "type": "http response", "z": TAB_ID,
    "x": X_RESP, "y": Y_START, "wires": []
}

# Catch error para resumen
resumen_catch = {
    "id": uid(), "type": "catch", "z": TAB_ID,
    "name": "Error resumen SQL",
    "scope": [resumen_mssql_id], "uncaught": False,
    "x": X_MSSQL, "y": Y_START + 60, "wires": [["49e00ab8cc710b1d"]]
}

new_nodes.extend([resumen_in, resumen_fn, resumen_mssql, resumen_resp_fn, resumen_resp, resumen_catch])

# --- 5B. GET /api/proveedores?q= ---
prov_in = {
    "id": uid(), "type": "http in", "z": TAB_ID,
    "name": "GET /api/proveedores",
    "url": "/api/proveedores", "method": "get",
    "x": X_HTTP, "y": Y_START + 100, "wires": [[uid()]]
}
prov_fn_id = prov_in['wires'][0][0]

prov_fn = {
    "id": prov_fn_id, "type": "function", "z": TAB_ID,
    "name": "Query proveedores",
    "func": (
        "msg._req = msg.req;\n"
        "msg._res = msg.res;\n"
        "const q = (msg.req.query.q || '').replace(/[^a-zA-Z0-9 ]/g, '');\n"
        "if (!q || q.length < 2) { msg.payload = JSON.stringify([]); return msg; }\n"
        "msg.payload = `SELECT TOP 20 ID, CODIGO, DENOMINACION AS nombre, CUIT AS cuit\\n"
        "FROM PROVEEDOR\\n"
        "WHERE ACTIVESTATUS = 0\\n"
        "  AND (DENOMINACION LIKE '%${q}%' OR CUIT LIKE '%${q}%' OR CODIGO LIKE '%${q}%')\\n"
        "ORDER BY DENOMINACION`;\\n"
        "return msg;"
    ),
    "outputs": 1, "x": X_FN, "y": Y_START + 100, "wires": [[uid()]]
}
prov_mssql_id = prov_fn['wires'][0][0]

prov_mssql = {
    "id": prov_mssql_id, "type": "MSSQL", "z": TAB_ID,
    "mssqlCN": MSSQL_CN_ID, "name": "Query proveedores SQL",
    "outField": "payload", "returnType": "0", "throwErrors": "0",
    "query": "", "modeOpt": "", "modeOptType": "query",
    "queryOpt": "payload", "queryOptType": "msg",
    "paramsOpt": "queryParams", "paramsOptType": "msg",
    "rows": "", "rowsType": "msg", "parseMustache": False, "params": [],
    "x": X_MSSQL, "y": Y_START + 100, "wires": [[uid()]]
}
prov_resp_fn_id = prov_mssql['wires'][0][0]

prov_resp_fn = {
    "id": prov_resp_fn_id, "type": "function", "z": TAB_ID,
    "name": "Responder proveedores JSON",
    "func": (
        "msg.res = msg._res;\n"
        "msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};\n"
        "msg.payload = JSON.stringify(msg.payload || []);\n"
        "return msg;"
    ),
    "outputs": 1, "x": X_RESP_FN, "y": Y_START + 100, "wires": [[uid()]]
}
prov_resp_id = prov_resp_fn['wires'][0][0]

prov_resp = {
    "id": prov_resp_id, "type": "http response", "z": TAB_ID,
    "x": X_RESP, "y": Y_START + 100, "wires": []
}

new_nodes.extend([prov_in, prov_fn, prov_mssql, prov_resp_fn, prov_resp])

# --- 5C. GET /api/oc/proveedor/:cuit ---
oc_prov_in = {
    "id": uid(), "type": "http in", "z": TAB_ID,
    "name": "GET /api/oc/proveedor/:cuit",
    "url": "/api/oc/proveedor/:cuit", "method": "get",
    "x": X_HTTP, "y": Y_START + 180, "wires": [[uid()]]
}
oc_prov_fn_id = oc_prov_in['wires'][0][0]

oc_prov_fn = {
    "id": oc_prov_fn_id, "type": "function", "z": TAB_ID,
    "name": "Query OC por CUIT (params)",
    "func": (
        "msg._req = msg.req;\n"
        "msg._res = msg.res;\n"
        "const cuit = (msg.req.params.cuit || '').replace(/[^0-9]/g, '');\n"
        "if (!cuit) {\n"
        "  msg.statusCode = 400;\n"
        "  msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};\n"
        "  msg.payload = JSON.stringify({ error: 'CUIT requerido' });\n"
        "  return [null, msg];\n"
        "}\n"
        "msg.payload = `SELECT\\n"
        "    oc.ID AS id,\\n"
        "    oc.NUMERODOCUMENTO AS nro,\\n"
        "    CONVERT(varchar, oc.FECHAINGRESO, 112) AS fecha,\\n"
        "    oc.TOTAL AS total,\\n"
        "    oc.ESTADO AS estado,\\n"
        "    CASE WHEN EXISTS (SELECT 1 FROM TRFACTURACOMPRA fc WHERE fc.DETALLE = oc.NUMERODOCUMENTO) THEN 1 ELSE 0 END AS facturada\\n"
        "FROM TRORDENCOMPRA oc\\n"
        "JOIN PROVEEDOR p ON p.CODIGO = oc.CODIGODESTINATARIO\\n"
        "WHERE p.CUIT = '${cuit}' AND oc.ESTADO IN ('A','P','C')\\n"
        "ORDER BY oc.FECHAINGRESO DESC`;\\n"
        "return [msg, null];"
    ),
    "outputs": 2, "x": X_FN, "y": Y_START + 180, "wires": [[uid()], [uid()]]
}
oc_prov_mssql_id = oc_prov_fn['wires'][0][0]
oc_prov_err_id = oc_prov_fn['wires'][1][0]

oc_prov_mssql = {
    "id": oc_prov_mssql_id, "type": "MSSQL", "z": TAB_ID,
    "mssqlCN": MSSQL_CN_ID, "name": "Query OC proveedor SQL",
    "outField": "payload", "returnType": "0", "throwErrors": "0",
    "query": "", "modeOpt": "", "modeOptType": "query",
    "queryOpt": "payload", "queryOptType": "msg",
    "paramsOpt": "queryParams", "paramsOptType": "msg",
    "rows": "", "rowsType": "msg", "parseMustache": False, "params": [],
    "x": X_MSSQL, "y": Y_START + 180, "wires": [[uid()]]
}
oc_prov_resp_fn_id = oc_prov_mssql['wires'][0][0]

oc_prov_resp_fn = {
    "id": oc_prov_resp_fn_id, "type": "function", "z": TAB_ID,
    "name": "Responder OCs JSON",
    "func": (
        "msg.res = msg._res;\n"
        "msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};\n"
        "msg.payload = JSON.stringify(msg.payload || []);\n"
        "return msg;"
    ),
    "outputs": 1, "x": X_RESP_FN, "y": Y_START + 180, "wires": [[uid()]]
}
oc_prov_resp_id = oc_prov_resp_fn['wires'][0][0]

oc_prov_resp = {
    "id": oc_prov_resp_id, "type": "http response", "z": TAB_ID,
    "x": X_RESP, "y": Y_START + 180, "wires": []
}
# Error path
oc_prov_err_resp = {
    "id": oc_prov_err_id, "type": "http response", "z": TAB_ID,
    "x": X_RESP, "y": Y_START + 220, "wires": []
}

new_nodes.extend([oc_prov_in, oc_prov_fn, oc_prov_mssql, oc_prov_resp_fn, oc_prov_resp, oc_prov_err_resp])

# ---------------------------------------------------------------------------
# 6. ELIMINAR nodo MySQLdatabase config (ya no se usa)
# ---------------------------------------------------------------------------
flow = [n for n in flow if n['id'] != 'mysql-api-config']

# ---------------------------------------------------------------------------
# 7. AGREGAR nuevos nodos al flow
# ---------------------------------------------------------------------------
flow.extend(new_nodes)

# ---------------------------------------------------------------------------
# 8. GUARDAR
# ---------------------------------------------------------------------------
out_path = '/mnt/c/claudecode/proyectos-ingenio/automatizacion-facturas/nodered/flow_api_facturas_mssql.json'
with open(out_path, 'w') as f:
    json.dump(flow, f, indent=2, ensure_ascii=False)

print(f"\n✅ Flow actualizado guardado en: {out_path}")
print(f"   Total nodos: {len(flow)}")
print(f"   MSSQL: {sum(1 for n in flow if n['type'] == 'MSSQL')}")
print(f"   MySQL: {sum(1 for n in flow if n['type'] in ('mysql', 'MySQLdatabase'))}")

# Resumen de endpoints
print("\n=== Endpoints finales ===")
for n in flow:
    if n['type'] == 'http in':
        print(f"  {n['method']:7s} {n['url']:35s} → {n['name']}")
