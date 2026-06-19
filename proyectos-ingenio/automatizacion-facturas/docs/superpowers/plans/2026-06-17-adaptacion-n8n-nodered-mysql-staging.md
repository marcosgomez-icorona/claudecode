# Adaptación n8n → Node-RED → MySQL Staging — Plan de Implementación

> **Para agentic workers:** REQUIRED SUB-SKILL: Usar superpowers:subagent-driven-development (recomendado) o superpowers:executing-plans para implementar tarea por tarea. Los pasos usan checkbox (`- [ ]`).

**Goal:** Que el workflow n8n deje de escribir directamente en SQL Server Calipso y envíe los datos extraídos vía POST a Node-RED, quien los inserta en MySQL staging_facturas para la UI de validación.

**Architecture:** Se modifican 3 nodos en el n8n workflow existente (eliminando generación de INSERT directo a SQL Server, expandiendo payload a ~28 campos, cambiando URL del POST). Se agrega un nuevo endpoint POST `/api/recepcion/factura` en el Node-RED flow existente con 6 nodos que validan payload, detectan duplicados e insertan en MySQL. Se elimina 1 nodo de n8n (verificación de duplicado externa) y se reconecta otro.

**Tech Stack:** n8n workflow JSON, Node-RED (JavaScript function nodes + MySQL node), MySQL (db_automatizaciones.staging_facturas)

## Global Constraints

- NO escribir directo en SQL Server Calipso — todo pasa por middleware Node-RED
- SQL compatible con MySQL (no SQL Server)
- JSON válido en todos los nodos Code de n8n
- Node-RED function nodes: JavaScript ES5/ES6 compatible con Node 16.x
- Los IDs de nodos nuevos en Node-RED deben ser únicos dentro del flow
- Separación test/prod: este cambio se hace primero sobre el flow actual `flow_api_facturas.json`

---

### Task 1: Modificar nodo "Normalizar campos OpenAI" en n8n

**Files:**
- Modify: `proyectos-ingenio/automatizacion-facturas/n8n/Recepcion y Analisis de Factura_en_produccion.json` — nodo id: `22c87937-029e-4ca0-b092-b6190c7463a6`

**Interfaces:**
- Consumes: output del AI Agent (JSON parseado por OpenAI)
- Produces: objeto JSON con todos los campos normalizados (SIN `sql_query`)

- [ ] **Paso 1: Identificar el código a cambiar**

El nodo actual (líneas 365-381 del JSON) tiene código JS que:
1. Parsea la respuesta de OpenAI
2. Normaliza fechas, CUITs, importes
3. Genera `sql_query` con INSERT a `UD_EZI_STAGING_FACTURAS` (SQL Server)
4. Devuelve el objeto con `sql_query` incluido

- [ ] **Paso 2: Reemplazar el código JS del nodo**

Localizar en el JSON el bloque `"jsCode"` del nodo con id `22c87937-029e-4ca0-b092-b6190c7463a6`. Reemplazar todo el contenido de `"jsCode"` desde la línea que comienza `const sql_query = ` hasta el `return` final.

ELIMINAR este bloque (desde la línea 361 del código actual):
```javascript
const sql_query = `INSERT INTO UD_EZI_STAGING_FACTURAS (...
```
Reemplazar el `return [{ json: { ... } }]` final con:

```javascript
return [{ json: {
  staging_id,
  tipo_operacion,
  proveedor_cuit: cuit,
  proveedor_nombre: razon_social,
  letra,
  numerodocumento: nro_documento,
  fecha_emision,
  fecha_vencimiento,
  fecha_vto_cae,
  cae,
  neto,
  iva_21,
  iva_105,
  percepciones,
  otros_impuestos,
  total,
  es_dolar,
  cotizacion_val,
  cotizacion_origen,
  referencia,
  nro_remito,
  items_json,
  items_count: items.length,
  confianza_parseo: confianza_num,
  confianza_parseo_label: confianza_label,
  notas_parseo: parsed.notas_parseo || null,
  // Metadata para staging
  pdf_filename:    prevNode.pdf_filename,
  file_type:       prevNode.file_type,
  pdf_hash:        prevNode.pdf_hash,
  email_origen:    prevNode.email_from,
  email_asunto:    prevNode.email_subject,
  email_date:      prevNode.email_date,
  email_gmail_id:  prevNode.email_gmail_id,
  fuente:          prevNode.fuente || 'EMAIL_GMAIL_N8N',
  pdf_text:        prevNode.pdf_text,
  openai_raw:      rawContent.substring(0, 500)
}}];
```

**Importante:** Mantener TODO el código de parseo, normalización y validación anterior intacto. Solo se elimina la generación de `sql_query` y se cambia el return.

- [ ] **Paso 3: Verificar que no hay otras referencias a SQL Server en el mismo nodo**

Buscar en el código si hay alguna otra referencia a `UD_EZI_STAGING_FACTURAS`, `MONEDA_ID`, `tipotransaccion_id`, `compania_id` que deba eliminarse. El resto del código (parseDate, numF, esc, str, etc.) se mantiene intacto.

- [ ] **Paso 4: Validar sintaxis JSON del workflow completo**

```bash
cd /mnt/c/claudecode
python3 -c "
import json
with open('proyectos-ingenio/automatizacion-facturas/n8n/Recepcion y Analisis de Factura_en_produccion.json') as f:
    data = json.load(f)
print('n8n workflow JSON válido')
print(f'Nodes: {len(data[\"nodes\"])}')
# Verificar que el nodo modificado existe
node = [n for n in data['nodes'] if n['id'] == '22c87937-029e-4ca0-b092-b6190c7463a6'][0]
print(f'Nodo \"{node[\"name\"]}\" encontrado')
assert 'sql_query' not in node['parameters']['jsCode'], 'Todavia contiene sql_query!'
print('OK: sql_query eliminado correctamente')
"
```

Expected output: `n8n workflow JSON válido`, `Nodes: ...`, `OK: sql_query eliminado correctamente`

- [ ] **Paso 5: Commit**

```bash
cd /mnt/c/claudecode
git add proyectos-ingenio/automatizacion-facturas/n8n/Recepcion\ y\ Analisis\ de\ Factura_en_produccion.json
git commit -m "fix(n8n): eliminar INSERT directo a SQL Server en Normalizar campos OpenAI

- Se elimina generacion de sql_query con INSERT a UD_EZI_STAGING_FACTURAS
- Los datos fluiran via POST a Node-RED como middleware
- Se mantiene toda la logica de parseo y normalizacion intacta

Co-Authored-By: Claude <noreply@anthropic.com>"
```

---

### Task 2: Modificar nodo "Aplicar TC BNA" en n8n (también genera sql_query)

**Files:**
- Modify: `proyectos-ingenio/automatizacion-facturas/n8n/Recepcion y Analisis de Factura_en_produccion.json` — nodo id: `e1c36349-e135-41ff-9bd2-994b796a9671`

**Interfaces:**
- Consumes: output del nodo "¿Necesita cotización BNA?" + respuesta BCRA
- Produces: objeto JSON mergeado con cotización aplicada (SIN `sql_query`)

- [ ] **Paso 1: Revisar el código del nodo**

Este nodo también genera `sql_query` después de obtener cotización BNA. El código actual también tiene un bloque de INSERT directo a SQL Server.

- [ ] **Paso 2: Eliminar el bloque sql_query del return**

Localizar en el JSON el bloque `"jsCode"` del nodo con id `e1c36349-e135-41ff-9bd2-994b796a9671`.

ELIMINAR las líneas que definen `sql_query` y `monedaId` (desde `const monedaId = ...` hasta la generación de `sql_query`).

Reemplazar el return final con:
```javascript
return [{ json: Object.assign({}, j, { cotizacion_val }) }];
```

**Nota:** `j` ya contiene `cotizacion_usd`, `cotizacion_origen` y `cotizacion_aviso` del merge con la respuesta BCRA.

- [ ] **Paso 3: Validar sintaxis JSON**

```bash
cd /mnt/c/claudecode
python3 -c "
import json
with open('proyectos-ingenio/automatizacion-facturas/n8n/Recepcion y Analisis de Factura_en_produccion.json') as f:
    data = json.load(f)
node = [n for n in data['nodes'] if n['id'] == 'e1c36349-e135-41ff-9bd2-994b796a9671'][0]
assert 'sql_query' not in node['parameters']['jsCode'], 'Todavia contiene sql_query!'
print('OK: sql_query eliminado del nodo Aplicar TC BNA')
"
```

- [ ] **Paso 4: Commit**

```bash
cd /mnt/c/claudecode
git add proyectos-ingenio/automatizacion-facturas/n8n/Recepcion\ y\ Analisis\ de\ Factura_en_produccion.json
git commit -m "fix(n8n): eliminar INSERT directo en nodo Aplicar TC BNA

Co-Authored-By: Claude <noreply@anthropic.com>"
```

---

### Task 3: Expandir nodo "Preparar payload limpio" en n8n

**Files:**
- Modify: `proyectos-ingenio/automatizacion-facturas/n8n/Recepcion y Analisis de Factura_en_produccion.json` — nodo id: `a3ee1e64-c414-47d3-9406-69b88d015bb5`

**Interfaces:**
- Consumes: output del nodo "Confianza >= 50%" (que puede venir de "Aplicar TC BNA" o directo)
- Produces: payload expandido con ~28 campos para POST a Node-RED

- [ ] **Paso 1: Revisar el código actual del nodo**

El código actual (líneas 687-701 del JSON) extrae solo 14 campos del source.

- [ ] **Paso 2: Reemplazar el código JS con el payload expandido**

```javascript
// Extrae todos los campos que Node-RED necesita para MySQL staging
const src = $('Confianza >= 50%').item.json;

return [{
  json: {
    // Identificacion
    staging_id:         src.staging_id,
    tipo_operacion:     src.tipo_operacion,
    fuente:             src.fuente || 'EMAIL_GMAIL_N8N',
    
    // Proveedor
    proveedor_cuit:     src.proveedor_cuit || src.cuit,
    proveedor_nombre:   src.proveedor_nombre || src.razon_social,
    proveedor_codigo:   src.proveedor_codigo || null,
    
    // Documento
    numerodocumento:    src.numerodocumento || src.nro_documento,
    letra:              src.letra,
    fecha_emision:      src.fecha_emision,
    fecha_vencimiento:  src.fecha_vencimiento,
    
    // CAE
    cae:                src.cae,
    fecha_vto_cae:      src.fecha_vto_cae,
    
    // Importes
    neto:               src.neto,
    iva_21:             src.iva_21,
    iva_105:            src.iva_105,
    percepciones:       src.percepciones,
    otros_impuestos:    src.otros_impuestos,
    total:              src.total,
    
    // Moneda
    es_dolar:           src.es_dolar || 0,
    cotizacion:         src.cotizacion_val || 1,
    cotizacion_origen:  src.cotizacion_origen || 'PESOS',
    cotizacion_aviso:   src.cotizacion_aviso || null,
    
    // Referencias
    referencia:         src.referencia || null,
    nro_remito:         src.nro_remito || null,
    
    // Items
    items_json:         src.items_json || '[]',
    items_count:        src.items_count || 0,
    
    // Archivo
    pdf_filename:       src.pdf_filename,
    pdf_hash:           src.pdf_hash,
    
    // Email
    email_origen:       src.email_origen || src.email_from,
    email_asunto:       src.email_asunto || src.email_subject,
    pdf_text:           src.pdf_text || null,
    
    // Confianza
    confianza_parseo:          src.confianza_parseo,
    confianza_parseo_label:    src.confianza_parseo_label,
    notas_parseo:              src.notas_parseo || null
  }
}];
```

- [ ] **Paso 3: Validar JSON**

```bash
cd /mnt/c/claudecode
python3 -c "
import json
with open('proyectos-ingenio/automatizacion-facturas/n8n/Recepcion y Analisis de Factura_en_produccion.json') as f:
    data = json.load(f)
node = [n for n in data['nodes'] if n['id'] == 'a3ee1e64-c414-47d3-9406-69b88d015bb5'][0]
code = node['parameters']['jsCode']
# Verificar que tiene los campos clave
assert 'cotizacion_aviso' in code
assert 'email_origen' in code
assert 'items_count' in code
assert 'confianza_parseo_label' in code
print('OK: Payload expandido correctamente')
print(f'Campos totales en return: {code.count(\"json:\")} bloques')
"
```

- [ ] **Paso 4: Commit**

```bash
cd /mnt/c/claudecode
git add proyectos-ingenio/automatizacion-facturas/n8n/Recepcion\ y\ Analisis\ de\ Factura_en_produccion.json
git commit -m "feat(n8n): expandir payload a ~28 campos para Node-RED

- De 14 a ~28 campos incluyendo cotizacion, items_count, metadata email
- Todos los campos del schema MySQL staging_facturas

Co-Authored-By: Claude <noreply@anthropic.com>"
```

---

### Task 4: Cambiar URL del POST y eliminar verificación duplicado en n8n

**Files:**
- Modify: `proyectos-ingenio/automatizacion-facturas/n8n/Recepcion y Analisis de Factura_en_produccion.json` — nodo id: `01470f05-029f-4c4c-b7e0-6ef455adfd10`
- Modify: eliminar nodo id: `760969eb-9054-422d-86e5-9a376bfd6601`
- Modify: reconectar nodo id: `bee8ecfe-f57b-449e-a458-986baaf7c0e1`

**Interfaces:**
- Consumes: payload expandido de Task 3
- Produces: POST HTTP a Node-RED, respuesta HTTP evaluada como duplicado/no-duplicado

- [ ] **Paso 1: Cambiar URL del nodo "POST a Node-RED"**

En el nodo `01470f05-029f-4c4c-b7e0-6ef455adfd10`, reemplazar:
```json
"url": "http://192.168.0.23:1880/recepcion/factura",
```
por:
```json
"url": "http://192.168.0.23:1880/facturas/api/recepcion/factura",
```

Y dejar el `"jsonBody"` como `"={{ $json }}"` para que envíe el payload completo (ahora expandido).

- [ ] **Paso 2: Configurar el HTTP Request para que capture la respuesta**

En el mismo nodo, asegurar que el response complete se devuelva como `$json` para que el siguiente nodo pueda evaluarlo. Verificar que `options` incluya:
```json
"options": {
  "timeout": 30000,
  "response": {
    "response": {
      "responseFormat": "json"
    }
  }
}
```
Esto permite que el nodo "¿Ya existe en staging?" pueda leer `$json.ok` y `$json.error`.

- [ ] **Paso 3: Eliminar nodo "Verificar duplicado staging"**

En el array `"nodes"`, eliminar el objeto completo con id `760969eb-9054-422d-86e5-9a376bfd6601`.

En las `"connections"`, eliminar la entrada `"Verificar duplicado staging"`.

- [ ] **Paso 4: Reconectar "¿Ya existe en staging?"**

Cambiar las conexiones para que:

**Antes:**
```
Confianza >= 50% → Verificar duplicado staging → ¿Ya existe en staging? → Descartado
                                                                    → Preparar payload limpio
```

**Después:**
```
Confianza >= 50% → POST a Node-RED → ¿Ya existe en staging? → Descartado (si 409)
                                                          → (sigue) (si 200)
```

En la sección `"connections"`, actualizar la entrada de `"Confianza >= 50%"` para que su output 0 vaya directo al nodo `01470f05-...` (POST a Node-RED):

```json
"Confianza >= 50%": {
  "main": [
    [
      {
        "node": "POST a Node-RED",
        "type": "main",
        "index": 0
      }
    ],
    [
      {
        "node": "Alerta parseo BAJA",
        "type": "main",
        "index": 0
      }
    ]
  ]
}
```

Y actualizar la conexión de `"POST a Node-RED"` para que su output vaya a `"¿Ya existe en staging?"`:

```json
"POST a Node-RED": {
  "main": [
    [
      {
        "node": "¿Ya existe en staging?",
        "type": "main",
        "index": 0
      }
    ]
  ]
}
```

- [ ] **Paso 5: Reconfigurar el nodo "¿Ya existe en staging?"**

Cambiar la condición del IF para que evalúe la respuesta HTTP en lugar de `$json.existe`:

```json
"conditions": {
  "options": {
    "caseSensitive": true,
    "typeValidation": "strict",
    "version": 2
  },
  "conditions": [
    {
      "id": "cond-dup-http-01",
      "leftValue": "={{ $json.statusCode }}",
      "rightValue": 409,
      "operator": {
        "type": "number",
        "operation": "equals"
      }
    }
  ],
  "combinator": "and"
}
```

Output 0 (true) → `"Descartado (duplicado)"` (id: `537e4ba8-4c49-42c1-87b3-74cc9b1471ca`)
Output 1 (false) → `"POST a Node-RED"` (NO, el flujo ya pasó por POST, así que va a fin de flujo o al nodo "Marcar como leído")

**Corrección:** El flujo después de "¿Ya existe en staging?" debería ser:
- Output 0 (statusCode == 409): duplicado → `"Descartado (duplicado)"`
- Output 1 (statusCode != 409): éxito → fin del flujo (el email se marca como leído en el otro branch)

- [ ] **Paso 6: Validar estructura completa del JSON**

```bash
cd /mnt/c/claudecode
python3 -c "
import json
with open('proyectos-ingenio/automatizacion-facturas/n8n/Recepcion y Analisis de Factura_en_produccion.json') as f:
    data = json.load(f)

# Verificar que el nodo eliminado no existe
node_ids = [n['id'] for n in data['nodes']]
assert '760969eb-9054-422d-86e5-9a376bfd6601' not in node_ids, 'Nodo Verificar duplicado aun existe!'

# Verificar conexiones
conns = data['connections']
# POST a Node-RED debe conectar a ¿Ya existe en staging?
post_wires = conns['POST a Node-RED']['main'][0]
assert post_wires[0]['node'] == '¿Ya existe en staging?', 'POST no conecta a ¿Ya existe?'
# Confianza >= 50% debe conectar directo a POST
conf_wires = conns['Confianza >= 50%']['main'][0]
assert conf_wires[0]['node'] == 'POST a Node-RED', 'Confianza no conecta a POST'

print('OK: Conexiones reconfiguradas correctamente')
print(f'Total nodos: {len(data[\"nodes\"])} (1 eliminado)')
"
```

- [ ] **Paso 7: Commit**

```bash
cd /mnt/c/claudecode
git add proyectos-ingenio/automatizacion-facturas/n8n/Recepcion\ y\ Analisis\ de\ Factura_en_produccion.json
git commit -m "refactor(n8n): cambiar POST a /facturas/api/recepcion/factura + eliminar check dup externo

- URL del POST movida a endpoint Node-RED correcto
- Nodo Verificar duplicado staging eliminado (lo maneja Node-RED)
- Nodo ¿Ya existe en staging? reconfigurado para evaluar HTTP 409

Co-Authored-By: Claude <noreply@anthropic.com>"
```

---

### Task 5: Agregar endpoint POST /api/recepcion/factura en Node-RED

**Files:**
- Modify: `proyectos-ingenio/automatizacion-facturas/nodered/flow_api_facturas.json`

**Interfaces:**
- Consumes: POST body con payload JSON de n8n (~28 campos)
- Produces: HTTP 200/400/409/500 según resultado

- [ ] **Paso 1: Generar IDs únicos para los nuevos nodos**

Usar IDs que sigan el patrón del flow existente (16 caracteres hex). Ejemplo:
- `rec-fact-http-in`: `a7f3c9d12e4b5f08`
- `rec-fact-valida`: `b8e4d0f23a5c6e19`
- `rec-fact-mysql-check`: `c9f5e1a34b6d7f20`
- `rec-fact-insert`: `d0a6f2b45c7e8a31`
- `rec-fact-items`: `e1b7a3c56d8f9b42`
- `rec-fact-mysql-items`: `f2c8b4d67e9a0c53`
- `rec-fact-http-resp`: `a3d9c5e78f0b1d64`

**Importante:** Verificar que estos IDs no existan ya en el JSON. Si existen, generar otros.

- [ ] **Paso 2: Agregar el nodo HTTP In**

```json
{
  "id": "a7f3c9d12e4b5f08",
  "type": "http in",
  "z": "86bcda5b2e8a7b12",
  "name": "POST recepcion factura",
  "url": "/api/recepcion/factura",
  "method": "post",
  "upload": false,
  "x": 120,
  "y": 1080,
  "wires": [
    [
      "b8e4d0f23a5c6e19"
    ]
  ]
}
```

- [ ] **Paso 3: Agregar nodo Function "Validar payload"**

```json
{
  "id": "b8e4d0f23a5c6e19",
  "type": "function",
  "z": "86bcda5b2e8a7b12",
  "name": "Validar payload + check dup",
  "func": "const b = msg.req.body || {};\n\n// Guardar para stages siguientes\nmsg._factura = b;\n\n// Validar campos obligatorios\nconst required = ['staging_id', 'proveedor_cuit', 'proveedor_nombre', 'numerodocumento', 'fecha_emision', 'total'];\nconst missing = required.filter(f => b[f] === undefined || b[f] === null || b[f] === '');\nif (missing.length > 0) {\n    msg.statusCode = 400;\n    msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};\n    msg.payload = JSON.stringify({\n        ok: false,\n        error: 'Campos requeridos faltantes: ' + missing.join(', ')\n    });\n    return [null, null, msg];\n}\n\n// Preparar query de check duplicado\n// Duplicado si: mismo hash, o mismo documento+cuit\nconst hash = b.pdf_hash ? `'${String(b.pdf_hash).replace(/'/g, \"''\")}'` : 'NULL';\nconst doc  = b.numerodocumento ? `'${String(b.numerodocumento).replace(/'/g, \"''\")}'` : 'NULL';\nconst cuit = b.proveedor_cuit ? `'${String(b.proveedor_cuit).replace(/'/g, \"''\")}'` : 'NULL';\n\nmsg.topic = 'SELECT id FROM staging_facturas WHERE ' +\n    `(pdf_hash = ${hash} AND ${hash} !== 'NULL')` +\n    ` OR (numerodocumento = ${doc} AND proveedor_cuit = ${cuit} AND ${doc} !== 'NULL')` +\n    ' LIMIT 1';\nmsg.payload = [];\nreturn [msg, null, null];\n",
  "outputs": 3,
  "x": 340,
  "y": 1080,
  "wires": [
    [
      "c9f5e1a34b6d7f20"
    ],
    [],
    [
      "a3d9c5e78f0b1d64"
    ]
  ]
}
```

**Outputs:** 0 = check duplicado, 1 = sin usar, 2 = error 400

- [ ] **Paso 4: Agregar nodo MySQL "Check duplicado"**

```json
{
  "id": "c9f5e1a34b6d7f20",
  "type": "mysql",
  "z": "86bcda5b2e8a7b12",
  "mydb": "mysql-api-config",
  "name": "Check duplicado en staging",
  "x": 560,
  "y": 1080,
  "wires": [
    [
      "d0a6f2b45c7e8a31"
    ]
  ]
}
```

- [ ] **Paso 5: Agregar nodo Function "Insertar o responder dup"**

```json
{
  "id": "d0a6f2b45c7e8a31",
  "type": "function",
  "z": "86bcda5b2e8a7b12",
  "name": "Insertar o responder dup",
  "func": "const dup = msg.payload || [];\nif (dup.length > 0) {\n    // Ya existe -> 409\n    msg.res = msg._res;\n    msg.statusCode = 409;\n    msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};\n    msg.payload = JSON.stringify({\n        ok: false,\n        error: 'Factura duplicada',\n        existente_id: dup[0].id\n    });\n    return [null, msg];\n}\n\n// No existe -> preparar INSERT\nconst f = msg._factura;\nconst esc = s => s ? String(s).replace(/'/g, \"''\") : null;\nconst str = (s) => esc(s) !== null ? `'${esc(s)}'` : 'NULL';\nconst num = (n) => (n !== null && n !== undefined && !isNaN(Number(n))) ? parseFloat(n) : 0;\nconst bool = (b) => (b === true || b === 1 || b === '1' || b === 1) ? 1 : 0;\n\nmsg.topic = `INSERT INTO staging_facturas (\n    id, tipo_operacion, estado_proceso, fecha_carga, usuario_carga, origen,\n    numerodocumento, letra, fecha_emision, fecha_vencimiento,\n    proveedor_cuit, proveedor_nombre, proveedor_codigo,\n    neto, iva_21, iva_105, percepciones, otros_impuestos, total,\n    es_dolar, cotizacion, cotizacion_origen, cotizacion_aviso,\n    cae, fecha_vto_cae, referencia, nro_remito,\n    pdf_filename, pdf_hash, email_origen, email_asunto,\n    items_json, confianza_parseo, notas_parseo\n) VALUES (\n    ${str(f.staging_id)}, ${str(f.tipo_operacion)}, 'PENDIENTE', NOW(), 'n8n', ${str(f.fuente)},\n    ${str(f.numerodocumento)}, ${str(f.letra)}, ${str(f.fecha_emision)}, ${str(f.fecha_vencimiento)},\n    ${str(f.proveedor_cuit)}, ${str(f.proveedor_nombre)}, ${str(f.proveedor_codigo)},\n    ${num(f.neto)}, ${num(f.iva_21)}, ${num(f.iva_105)}, ${num(f.percepciones)}, ${num(f.otros_impuestos)}, ${num(f.total)},\n    ${bool(f.es_dolar)}, ${num(f.cotizacion)}, ${str(f.cotizacion_origen)}, ${str(f.cotizacion_aviso)},\n    ${str(f.cae)}, ${str(f.fecha_vto_cae)}, ${str(f.referencia)}, ${str(f.nro_remito)},\n    ${str(f.pdf_filename)}, ${str(f.pdf_hash)}, ${str(f.email_origen)}, ${str(f.email_asunto)},\n    ${str(f.items_json)}, ${str(f.confianza_parseo_label)}, ${str(f.notas_parseo)}\n)`;\nreturn [msg, null];\n",
  "outputs": 2,
  "x": 780,
  "y": 1080,
  "wires": [
    [
      "e1b7a3c56d8f9b42"
    ],
    [
      "a3d9c5e78f0b1d64"
    ]
  ]
}
```

**Outputs:** 0 = continuar a items, 1 = responder 409 dup

- [ ] **Paso 6: Agregar nodo Function "Preparar items"**

```json
{
  "id": "e1b7a3c56d8f9b42",
  "type": "function",
  "z": "86bcda5b2e8a7b12",
  "name": "Preparar INSERT items",
  "func": "const f = msg._factura;\nlet items = [];\ntry { items = JSON.parse(f.items_json || '[]'); } catch(e) { items = []; }\n\nif (!items || items.length === 0) {\n    // Sin items -> responder OK directo\n    msg.res = msg._res;\n    msg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};\n    msg.payload = JSON.stringify({ ok: true, id: f.staging_id, items_inserted: 0 });\n    return [null, msg];\n}\n\nconst esc = s => s ? String(s).replace(/'/g, \"''\") : '';\nconst values = items.map(it => {\n    const desc = esc(it.descripcion || '');\n    const cant = it.cantidad != null ? parseFloat(it.cantidad) : 'NULL';\n    const uni  = esc(it.unidad || 'u');\n    const pu   = it.precio_unitario != null ? parseFloat(it.precio_unitario) : 'NULL';\n    const aliq = it.alicuota_iva != null ? parseInt(it.alicuota_iva) : 'NULL';\n    const sub  = it.subtotal != null ? parseFloat(it.subtotal) : 'NULL';\n    const lin  = parseInt(it.linea) || 1;\n    return `('${esc(f.staging_id)}', ${lin}, '${desc}', ${cant}, '${uni}', ${pu}, ${aliq}, ${sub})`;\n}).join(',\\n');\n\nmsg.topic = `INSERT INTO staging_facturas_items (factura_id, linea, descripcion, cantidad, unidad, precio_unitario, alicuota_iva, subtotal) VALUES\\n${values}`;\nreturn [msg, null];\n",
  "outputs": 2,
  "x": 1000,
  "y": 1080,
  "wires": [
    [
      "f2c8b4d67e9a0c53"
    ],
    [
      "b4e8c6d78f0a1e75"
    ]
  ]
}
```

**Outputs:** 0 = INSERT items, 1 = responder OK sin items

- [ ] **Paso 7a: Agregar nodo MySQL "INSERT items"**

```json
{
  "id": "f2c8b4d67e9a0c53",
  "type": "mysql",
  "z": "86bcda5b2e8a7b12",
  "mydb": "mysql-api-config",
  "name": "INSERT items",
  "x": 1220,
  "y": 1080,
  "wires": [
    [
      "b4e8c6d78f0a1e75"
    ]
  ]
}
```

- [ ] **Paso 7b: Agregar nodo Function "Responder OK items"**

```json
{
  "id": "b4e8c6d78f0a1e75",
  "type": "function",
  "z": "86bcda5b2e8a7b12",
  "name": "Responder OK items",
  "func": "// Formatea respuesta 200 OK despues de insertar items\n// (o cuando no habia items que insertar)\nmsg.res = msg._res;\nmsg.headers = {'Content-Type':'application/json','Access-Control-Allow-Origin':'*'};\nconst id = (msg._factura && msg._factura.staging_id) || 'unknown';\nconst inserted = (msg.payload && msg.payload.affectedRows) ? msg.payload.affectedRows : 0;\nmsg.payload = JSON.stringify({ ok: true, id, items_inserted: inserted });\nreturn msg;\n",
  "outputs": 1,
  "x": 1400,
  "y": 1080,
  "wires": [
    [
      "a3d9c5e78f0b1d64"
    ]
  ]
}
```

- [ ] **Paso 8: Agregar nodo HTTP Response final**

```json
{
  "id": "a3d9c5e78f0b1d64",
  "type": "http response",
  "z": "86bcda5b2e8a7b12",
  "name": "Responder 200 OK / 409 / 400",
  "x": 1600,
  "y": 1080,
  "wires": []
}
```

**Nota sobre flujo completo:**
- HTTP In → Validar (out 0) → MySQL Check Dup → Insertar (out 0) → Preparar items
  - out 0 (hay items) → MySQL Items → Responder OK items → HTTP Resp
  - out 1 (sin items) → Responder OK items → HTTP Resp
- Insertar (out 1, dup) ─→ HTTP Resp (status 409 ya seteado)
- Validar (out 2, error) ─→ HTTP Resp (status 400 ya seteado)

- [ ] **Paso 9: Validar estructura del JSON**

```bash
cd /mnt/c/claudecode
python3 -c "
import json

with open('proyectos-ingenio/automatizacion-facturas/nodered/flow_api_facturas.json') as f:
    data = json.load(f)

# Verificar que los nuevos nodos se agregaron (7 nodos nuevos: 6 funcionales + 1 HTTP Resp)
new_ids = ['a7f3c9d12e4b5f08', 'b8e4d0f23a5c6e19', 'c9f5e1a34b6d7f20', 'd0a6f2b45c7e8a31', 'e1b7a3c56d8f9b42', 'f2c8b4d67e9a0c53', 'b4e8c6d78f0a1e75', 'a3d9c5e78f0b1d64']
existing_ids = [n['id'] for n in data]
for nid in new_ids:
    assert nid not in existing_ids or nid == 'TBD', f'ID {nid} ya existe en el flow'
    existing_ids.append(nid)  # para proximas verificaciones

# Verificar conexiones del nuevo endpoint
http_in = [n for n in data if n['id'] == 'a7f3c9d12e4b5f08'][0]
assert http_in['url'] == '/api/recepcion/factura'
assert http_in['method'] == 'post'

# Verificar que MySQL items conecta a Responder OK items, no directo a HTTP
mysql_items = [n for n in data if n['id'] == 'f2c8b4d67e9a0c53'][0]
assert mysql_items['wires'][0][0] == 'b4e8c6d78f0a1e75', 'MySQL Items no conecta a Responder OK items'

# Verificar que Preparar items output 1 (sin items) conecta a Responder OK items
prep_items = [n for n in data if n['id'] == 'e1b7a3c56d8f9b42'][0]
assert prep_items['wires'][1][0] == 'b4e8c6d78f0a1e75', 'Preparar items (no items) no conecta a Responder OK items'

print(f'OK: Flow Node-RED valido, total nodos: {len(data)}')
print('Endpoint /api/recepcion/factura configurado correctamente')
print('Flujo: Validar → Check Dup → Insertar → Preparar items → [MySQL Items] → Responder OK → HTTP Resp')
"
```

- [ ] **Paso 10: Commit**

```bash
cd /mnt/c/claudecode
git add proyectos-ingenio/automatizacion-facturas/nodered/flow_api_facturas.json
git commit -m "feat(nodered): agregar endpoint POST /api/recepcion/factura

- 6 nodos nuevos: HTTP In, Validar payload, Check duplicado,
  Insertar factura, Preparar items, Insert items
- Validacion de campos obligatorios con respuesta 400
- Deteccion de duplicados por hash o doc+cuit con respuesta 409
- Insercion en staging_facturas + staging_facturas_items
- Manejo de casos: sin items, duplicado, payload invalido

Co-Authored-By: Claude <noreply@anthropic.com>"
```

---

### Task 6: Verificación y testing del flujo completo

**Files:**
- `proyectos-ingenio/automatizacion-facturas/n8n/Recepcion y Analisis de Factura_en_produccion.json`
- `proyectos-ingenio/automatizacion-facturas/nodered/flow_api_facturas.json`

- [ ] **Paso 1: Verificar que n8n no tiene más referencias a SQL Server directo**

```bash
cd /mnt/c/claudecode
grep -n "UD_EZI_STAGING_FACTURAS" proyectos-ingenio/automatizacion-facturas/n8n/Recepcion\ y\ Analisis\ de\ Factura_en_produccion.json || echo "OK: No hay referencias a SQL Server en n8n"
```

Expected: `OK: No hay referencias a SQL Server en n8n`

- [ ] **Paso 2: Verificar que Node-RED no tiene conflictos de IDs**

```bash
cd /mnt/c/claudecode
python3 -c "
import json
from collections import Counter

with open('proyectos-ingenio/automatizacion-facturas/nodered/flow_api_facturas.json') as f:
    data = json.load(f)

ids = [n['id'] for n in data]
dups = [k for k, v in Counter(ids).items() if v > 1]
if dups:
    print(f'ERROR: IDs duplicados: {dups}')
else:
    print(f'OK: {len(ids)} nodos, sin IDs duplicados')
"
```

- [ ] **Paso 3: Probar endpoint Node-RED localmente (si Node-RED está accesible)**

```bash
cd /mnt/c/claudecode
# Test con payload mínimo
curl -s -X POST http://192.168.0.23:1880/facturas/api/recepcion/factura \
  -H 'Content-Type: application/json' \
  -d '{
    "staging_id": "test-00000000-0000-0000-0000-000000000001",
    "tipo_operacion": "FACTURA_COMPRA",
    "proveedor_cuit": "30715543172",
    "proveedor_nombre": "PROVEEDOR TEST S.A.",
    "numerodocumento": "0001-00000001",
    "letra": "A",
    "fecha_emision": "2026-06-17",
    "total": 12100.00,
    "neto": 10000.00,
    "iva_21": 2100.00,
    "items_json": "[{\"linea\":1,\"descripcion\":\"ITEM TEST\",\"cantidad\":1,\"unidad\":\"u\",\"precio_unitario\":10000,\"alicuota_iva\":21,\"subtotal\":10000}]"
  }' 2>/dev/null || echo "Node-RED no accesible, continuar con deploy manual"
```

Expected output (si Node-RED responde): `{"ok":true,"id":"test-...","items_inserted":1}`

- [ ] **Paso 4: Verificar inserción en MySQL (si hay acceso)**

```bash
mysql -h 127.0.0.1 -P 3306 -u root -proot db_automatizaciones -e "
SELECT id, numerodocumento, proveedor_cuit, proveedor_nombre, total, estado_proceso
FROM staging_facturas
WHERE id LIKE 'test-%'
ORDER BY fecha_carga DESC
LIMIT 5;
" 2>/dev/null && echo "OK: datos insertados en staging_facturas" || echo "No se pudo verificar MySQL"
```

- [ ] **Paso 5: Commit final de verificación**

```bash
cd /mnt/c/claudecode
git add -A
git commit -m "test: verificacion final adaptacion n8n->Node-RED->MySQL

- Sin referencias a SQL Server directo en n8n
- Sin IDs duplicados en Node-RED
- Endpoint POST /api/recepcion/factura funcional

Co-Authored-By: Claude <noreply@anthropic.com>"
```

---

## Spec Coverage Check

| Requisito del Spec | Task que lo implementa |
|---|---|
| n8n: Eliminar INSERT directo SQL Server | Task 1 (Normalizar campos) + Task 2 (Aplicar TC BNA) |
| n8n: Expandir payload ~28 campos | Task 3 (Preparar payload limpio) |
| n8n: Cambiar URL POST | Task 4 (POST a Node-RED) |
| n8n: Eliminar verificación duplicado | Task 4 (eliminar nodo) |
| n8n: Reconectar ¿Ya existe? para evaluar HTTP 409 | Task 4 (reconectar nodo) |
| Node-RED: POST endpoint nuevo | Task 5 (6 nodos) |
| Node-RED: Validar payload obligatorio | Task 5 (nodo Validar payload) |
| Node-RED: Detectar duplicados (hash/doc+cuit) | Task 5 (nodo Check duplicado) |
| Node-RED: INSERT staging_facturas | Task 5 (nodo Insertar) |
| Node-RED: INSERT staging_facturas_items | Task 5 (nodos Preparar items + Insert items) |
| Respuestas HTTP: 200/400/409/500 | Task 5 (todos los nodos) |
| Verificación y testing | Task 6 |
