"""
Migra recepcion_factura_test1.json de MySQL a MSSQL
"""
import json, uuid

def uid():
    return hex(uuid.uuid4().int >> 80)[2:18]

IN_PATH  = '/mnt/c/claudecode/proyectos-ingenio/automatizacion-facturas/nodered/recepcion_factura_test1.json'
OUT_PATH = '/mnt/c/claudecode/proyectos-ingenio/automatizacion-facturas/nodered/recepcion_factura_mssql.json'

with open(IN_PATH) as f:
    flow = json.load(f)

TAB_ID = "tab-registrar-facturas"
MSSQL_CN_ID = "6814eb4bb7e82ddb"  # Calipso

# ===========================================================================
# 1. Actualizar fn-validar-params: msg.topic → msg.payload, mysql_query → sql_query
# ===========================================================================
for n in flow:
    if n['id'] == 'fn-validar-params':
        n['func'] = (
            "const q = msg.req.body || {};\n\n"
            "if (!q.staging_id || !q.sql_query) {\n"
            "    msg.statusCode = 400;\n"
            "    msg.headers = { 'Content-Type': 'application/json' };\n"
            "    msg.payload = JSON.stringify({ ok: false, error: 'Faltan staging_id o sql_query' });\n"
            "    return [null, msg];\n"
            "}\n\n"
            "msg._factura = {\n"
            "    staging_id:    q.staging_id,\n"
            "    cuit:          q.cuit          || '',\n"
            "    razon_social:  q.razon_social  || '',\n"
            "    nro_documento: q.nro_documento || '',\n"
            "    letra:         q.letra         || 'A',\n"
            "    fecha_emision: q.fecha_emision || '',\n"
            "    total:         q.total         || '0',\n"
            "    tipo_operacion:q.tipo_operacion|| 'FACTURA_COMPRA',\n"
            "    confianza:     q.confianza_parseo || '',\n"
            "    sql_query:     q.sql_query,\n"
            "    items_json:    q.items_json    || '[]'\n"
            "};\n\n"
            "// MSSQL: query via msg.payload\n"
            "msg.payload = q.sql_query;\n"
            "return [msg, null];"
        )
        print("✓ fn-validar-params actualizado (mysql_query → sql_query, topic → payload)")

    # 2. Migrar MySQL nodes → MSSQL
    if n['type'] == 'mysql':
        n['type'] = 'MSSQL'
        n['mssqlCN'] = MSSQL_CN_ID
        del n['mydb']
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
        print(f"✓ {n['name']}: MySQL → MSSQL (Calipso)")

    # 3. Actualizar fn-preparar-items: staging_facturas_items → UD_EZI_STAGING_ITEMS, topic → payload
    if n['id'] == 'fn-preparar-items':
        n['func'] = (
            "const f = msg._factura;\n"
            "let items = [];\n"
            "try { items = JSON.parse(f.items_json || '[]'); } catch(e) { items = []; }\n"
            "if (!items || items.length === 0) return [null, msg];\n"
            "const esc = s => s ? String(s).replace(/'/g, \"''\") : '';\n"
            "const rows = items.map(it => {\n"
            "    const desc = esc(it.descripcion || '');\n"
            "    const cant = (it.cantidad != null) ? Number(it.cantidad) : 'NULL';\n"
            "    const uni  = esc(it.unidad || 'u');\n"
            "    const pu   = (it.precio_unitario != null) ? Number(it.precio_unitario) : 'NULL';\n"
            "    const aliq = (it.alicuota_iva != null) ? parseInt(it.alicuota_iva) : 'NULL';\n"
            "    const sub  = (it.subtotal != null) ? Number(it.subtotal) : 'NULL';\n"
            "    const lin  = parseInt(it.linea) || 1;\n"
            "    return `('${f.staging_id}', ${lin}, '${desc}', ${cant}, '${uni}', ${pu}, ${aliq}, ${sub})`;\n"
            "}).join(',\\n');\n"
            "// MSSQL: query via msg.payload\n"
            "msg.payload = `INSERT INTO UD_EZI_STAGING_ITEMS (FACTURA_ID, LINEA, DESCRIPCION, CANTIDAD, UNIDAD, PRECIO_UNITARIO, ALICUOTA_IVA, SUBTOTAL) VALUES\\n${rows}`;\n"
            "return [msg, null];"
        )
        print("✓ fn-preparar-items actualizado (MySQL→MSSQL, staging_facturas_items→UD_EZI_STAGING_ITEMS)")

    # 4. Actualizar mensajes de respuesta
    if n['id'] == 'fn-resp-ok':
        n['func'] = (
            "const f = msg._factura || {};\n"
            "msg.statusCode = 200;\n"
            "msg.headers = { 'Content-Type': 'application/json' };\n"
            "msg.payload = JSON.stringify({\n"
            "    ok: true,\n"
            "    staging_id:    f.staging_id,\n"
            "    razon_social:  f.razon_social,\n"
            "    nro_documento: f.nro_documento,\n"
            "    total:         f.total,\n"
            "    mensaje:       'Factura registrada en UD_EZI_STAGING_FACTURAS (SQL Server)'\n"
            "});\n"
            "return msg;"
        )
        print("✓ fn-resp-ok actualizado")

    if n['id'] == 'fn-resp-error-mysql':
        n['name'] = 'Respuesta error SQL Server'
        n['func'] = (
            "const f = msg._factura || {};\n"
            "node.error('Error SQL Server [' + f.staging_id + ']: ' + (msg.error && msg.error.message));\n"
            "msg.statusCode = 500;\n"
            "msg.headers = { 'Content-Type': 'application/json' };\n"
            "msg.payload = JSON.stringify({\n"
            "    ok: false, staging_id: f.staging_id,\n"
            "    error: (msg.error && msg.error.message) || 'Error en SQL Server'\n"
            "});\n"
            "return msg;"
        )
        print("✓ fn-resp-error-mysql → fn-resp-error-sql")

    if n['id'] == 'catch-mysql-error':
        n['name'] = 'Error SQL Server'
        n['scope'] = ['mysql-insert-staging', 'mysql-insert-items']
        print("✓ catch actualizado")

# 5. Eliminar MySQLdatabase config
flow = [n for n in flow if n['type'] != 'MySQLdatabase']

# 6. Guardar
with open(OUT_PATH, 'w') as f:
    json.dump(flow, f, indent=4, ensure_ascii=False)

print(f"\n✅ Flow recepción migrado: {OUT_PATH}")
print(f"   Total nodos: {len(flow)}")
for n in flow:
    print(f"  [{n['type']:20s}] {n.get('name','')[:40]}")
