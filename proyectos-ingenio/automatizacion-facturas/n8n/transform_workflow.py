"""
Adapta el workflow n8n de producción:
1. Filtro newer_than:30d en Gmail Trigger
2. IF post-OpenAI: solo FACTURA_COMPRA y FACTURA_SERVICIO
3. Anti-duplicado: consulta a Node-RED antes del POST
"""
import json, copy, uuid

def uid():
    return str(uuid.uuid4())

IN_PATH  = '/mnt/c/claudecode/proyectos-ingenio/automatizacion-facturas/n8n/Recepcion y Analisis de Factura_en_produccion.json'
OUT_PATH = '/mnt/c/claudecode/proyectos-ingenio/automatizacion-facturas/n8n/Recepcion y Analisis de Factura_v10_filtrada.json'

with open(IN_PATH) as f:
    wf = json.load(f)

nodes = wf['nodes']
connections = wf['connections']

# ===========================================================================
# CAMBIO 1: Gmail Trigger — agregar newer_than:30d
# ===========================================================================
for n in nodes:
    if n['name'] == 'Gmail Trigger':
        n['parameters']['filters']['q'] = 'has:attachment filename:pdf newer_than:30d'
        print(f"✓ Gmail Trigger: query actualizado a '{n['parameters']['filters']['q']}'")
        break

# ===========================================================================
# CAMBIO 2: Nuevo nodo IF — "Filtrar tipo comprobante"
#   Solo deja pasar FACTURA_COMPRA y FACTURA_SERVICIO
#   Se inserta entre "Normalizar campos OpenAI" y "Filtrar receptor Ingenio"
# ===========================================================================
filtro_tipo_id   = uid()
filtro_tipo_desc  = uid()  # nodo terminal para descartados

filtro_tipo_node = {
    "parameters": {
        "conditions": {
            "options": {
                "caseSensitive": True,
                "leftValue": "",
                "typeValidation": "strict",
                "version": 1
            },
            "conditions": [
                {
                    "id": "cond-tipo-01",
                    "leftValue": "={{ $json.tipo_operacion }}",
                    "rightValue": "FACTURA_COMPRA",
                    "operator": {"type": "string", "operation": "equals"}
                }
            ],
            "combinator": "or"
        }
    },
    "id": filtro_tipo_id,
    "name": "Filtrar tipo comprobante",
    "type": "n8n-nodes-base.if",
    "typeVersion": 2,
    "position": [544, 1000],
    "wires": [[], []]  # true → sigue, false → discard
}

# Nodo terminal para descartados (Code node vacío que termina el flujo)
filtro_tipo_desc_node = {
    "parameters": {"jsCode": "// Descartado: no es FACTURA_COMPRA ni FACTURA_SERVICIO\n// El email ya fue marcado como leído por el nodo 'Marcar como leído'\nreturn null;"},
    "id": filtro_tipo_desc,
    "name": "Descartado (tipo no válido)",
    "type": "n8n-nodes-base.code",
    "typeVersion": 2,
    "position": [768, 1000],
    "wires": []
}

nodes.append(filtro_tipo_node)
nodes.append(filtro_tipo_desc_node)

# ===========================================================================
# CAMBIO 2b: Agregar condición "FACTURA_SERVICIO" al filtro
# ===========================================================================
filtro_tipo_node['parameters']['conditions']['conditions'].append({
    "id": "cond-tipo-02",
    "leftValue": "={{ $json.tipo_operacion }}",
    "rightValue": "FACTURA_SERVICIO",
    "operator": {"type": "string", "operation": "equals"}
})
# Pasar de "or" con 2 condiciones
filtro_tipo_node['parameters']['conditions']['combinator'] = "or"

# ===========================================================================
# CAMBIO 2c: Actualizar conexiones
#   Antes: Normalizar campos OpenAI → Filtrar receptor Ingenio
#   Ahora: Normalizar campos OpenAI → Filtrar tipo comprobante
#          Filtrar tipo comprobante[true] → Filtrar receptor Ingenio
#          Filtrar tipo comprobante[false] → Descartado (tipo no válido)
# ===========================================================================
conn_normalizar = connections['Normalizar campos OpenAI']
conn_normalizar['main'] = [[
    {"node": "Filtrar tipo comprobante", "type": "main", "index": 0}
]]
print("✓ Conexión: Normalizar campos OpenAI → Filtrar tipo comprobante")

# Agregar conexiones del nuevo nodo IF
connections['Filtrar tipo comprobante'] = {
    "main": [
        [{"node": "Filtrar receptor Ingenio", "type": "main", "index": 0}],
        [{"node": "Descartado (tipo no válido)", "type": "main", "index": 0}]
    ]
}
print("✓ Nodo IF 'Filtrar tipo comprobante': solo FACTURA_COMPRA o FACTURA_SERVICIO")

# ===========================================================================
# CAMBIO 3: Anti-duplicado — HTTP Request a Node-RED antes del POST
#   Consulta GET /api/facturas/check?hash=XXX&doc=XXX&cuit=XXX
#   Si ya existe → descartar
#   Si no → continuar al POST
# ===========================================================================

# 3a. HTTP Request para consultar duplicado
check_dup_id = uid()
check_dup_node = {
    "parameters": {
        "method": "GET",
        "url": "=http://192.168.0.23:1880/api/facturas/check?hash={{ $json.pdf_hash }}&doc={{ $json.nro_documento }}&cuit={{ $json.cuit }}",
        "sendBody": False,
        "options": {"timeout": 10000}
    },
    "id": check_dup_id,
    "name": "Verificar duplicado staging",
    "type": "n8n-nodes-base.httpRequest",
    "typeVersion": 4,
    "position": [1664, 960],
    "wires": [[]]
}
nodes.append(check_dup_node)

# 3b. IF para decidir basado en la respuesta del check
check_if_id = uid()
check_if_node = {
    "parameters": {
        "conditions": {
            "options": {
                "caseSensitive": True,
                "leftValue": "",
                "typeValidation": "strict",
                "version": 1
            },
            "conditions": [
                {
                    "id": "cond-dup-01",
                    "leftValue": "={{ $json.existe }}",
                    "rightValue": True,
                    "operator": {"type": "boolean", "operation": "equals"}
                }
            ],
            "combinator": "and"
        }
    },
    "id": check_if_id,
    "name": "¿Ya existe en staging?",
    "type": "n8n-nodes-base.if",
    "typeVersion": 2,
    "position": [1888, 960],
    "wires": [[], []]
}
nodes.append(check_if_node)

# 3c. Nodo terminal para duplicados
check_dup_desc_id = uid()
check_dup_desc_node = {
    "parameters": {"jsCode": "// Descartado: la factura ya existe en staging\n// Email ya marcado como leído\nreturn null;"},
    "id": check_dup_desc_id,
    "name": "Descartado (duplicado)",
    "type": "n8n-nodes-base.code",
    "typeVersion": 2,
    "position": [2112, 960],
    "wires": []
}
nodes.append(check_dup_desc_node)

# 3d. Actualizar conexiones para el path de anti-duplicado
#   Antes: Confianza >= 80% [true] → POST a Node-RED
#   Ahora: Confianza >= 80% [true] → Verificar duplicado staging → ¿Ya existe en staging?
#          ¿Ya existe? [true] → Descartado (duplicado)
#          ¿Ya existe? [false] → POST a Node-RED

conn_confianza = connections['Confianza >= 80%']
conn_confianza['main'] = [
    [{"node": "Verificar duplicado staging", "type": "main", "index": 0}],
    [{"node": "Alerta parseo BAJA", "type": "main", "index": 0}]
]
print("✓ Conexión: Confianza >= 80% [true] → Verificar duplicado staging")

connections['Verificar duplicado staging'] = {
    "main": [[{"node": "¿Ya existe en staging?", "type": "main", "index": 0}]]
}

connections['¿Ya existe en staging?'] = {
    "main": [
        [{"node": "Descartado (duplicado)", "type": "main", "index": 0}],
        [{"node": "POST a Node-RED", "type": "main", "index": 0}]
    ]
}
print("✓ Anti-duplicado: Verificar → IF → POST o Descartar")

# ===========================================================================
# CAMBIO 4: Actualizar código SQL de MySQL → SQL Server
#   En nodos "Normalizar campos OpenAI" y "Aplicar TC BNA"
#   Cambiar INSERT INTO staging_facturas → llamada a UD_EZI_SP_STAGING_INSERTAR
#   Cambiar NOW() → GETDATE()
# ===========================================================================
for n in nodes:
    if n['name'] in ('Normalizar campos OpenAI', 'Aplicar TC BNA'):
        old_code = n['parameters']['jsCode']
        # Reemplazar NOW() → GETDATE()
        new_code = old_code.replace('NOW()', 'GETDATE()')
        # Reemplazar referencia a staging_facturas (MySQL) con UD_EZI_STAGING_FACTURAS
        new_code = new_code.replace('INSERT INTO staging_facturas', 'INSERT INTO UD_EZI_STAGING_FACTURAS')
        # Actualizar nombre de variable mysql_query → sql_query
        new_code = new_code.replace('mysql_query', 'sql_query')
        n['parameters']['jsCode'] = new_code
        print(f"✓ {n['name']}: SQL actualizado MySQL→MSSQL")

# ===========================================================================
# CAMBIO 5: Actualizar POST a Node-RED para usar el endpoint correcto
#   El flow Node-RED tiene endpoints en /api/facturas/*
# ===========================================================================
for n in nodes:
    if n['name'] == 'POST a Node-RED':
        n['parameters']['url'] = 'http://192.168.0.23:1880/api/recepcion/factura'
        n['parameters']['options']['timeout'] = 30000
        print(f"✓ POST a Node-RED: URL actualizada")
        break

# ===========================================================================
# GUARDAR
# ===========================================================================
wf['nodes'] = nodes
wf['connections'] = connections

with open(OUT_PATH, 'w') as f:
    json.dump(wf, f, indent=2, ensure_ascii=False)

print(f"\n✅ Workflow n8n actualizado: {OUT_PATH}")
print(f"   Nodos totales: {len(nodes)}")

# Resumen de nodos
print("\n=== Nodos del workflow ===")
for n in nodes:
    tipo = n['type'].split('.')[-1] if '.' in n['type'] else n['type']
    nombre = n['name']
    print(f"  [{tipo:20s}] {nombre}")
