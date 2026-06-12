# 📊 ANÁLISIS - Recepcion y Analisis de Factura (n8n Workflow)

**Archivo**: `n8n/Recepcion y Analisis de Factura.json`  
**Líneas**: 680  
**Status**: ✅ Existe - Listo para integración  
**Próximo Paso**: Integrar MCP tools + Python validator  

---

## 🔄 Flujo General del Workflow

```
┌─────────────────────────────────────────────────────────────┐
│ 1. INGESTA DE EMAIL                                         │
│ Gmail Trigger (cada minuto)                                │
│ ├─ Busca: has:attachment filename:pdf + unread             │
│ └─ Filtra: Solo PDFs no leídos                             │
└─────────────────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. DESCARGA Y PARSEO DE PDF                                │
│ ├─ Obtener mensaje completo (Gmail API)                   │
│ ├─ Descargar adjunto (base64 → PDF)                       │
│ ├─ Extraer texto con OCR                                   │
│ └─ Preparar payload con metadata                           │
└─────────────────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. EXTRACCIÓN CON IA (OpenAI)                              │
│ ├─ Preparar prompt con schema JSON esperado               │
│ ├─ Enviar texto a OpenAI GPT-4                            │
│ └─ Obtener JSON con datos extraídos                       │
└─────────────────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. NORMALIZACIÓN Y VALIDACIONES                            │
│ ├─ Normalizar fechas (YYYY-MM-DD)                         │
│ ├─ Parseear importes (decimales)                          │
│ ├─ Validar CUIT (11 dígitos)                              │
│ ├─ Filtrar receptor (¿es Ingenio La Corona?)             │
│ ├─ Detectar si es en USD (necesita cotización BNA)       │
│ └─ Limpiar datos para SQL                                 │
└─────────────────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. OPERACIONES CONDICIONALES                               │
│ ├─ ¿USD? → Obtener cotización del BNA                     │
│ ├─ ¿Proveedor existente? → Buscar en base de datos       │
│ ├─ ¿OC asociada? → Consultar CALIPSO                     │
│ └─ ¿Requiere constancia? → Buscar en CALIPSO             │
└─────────────────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────────────────┐
│ 6. ALMACENAMIENTO (STAGING)                                │
│ ├─ Insertar en STAGING_FACTURAS                           │
│ ├─ Insertar items en STAGING_ITEMS                        │
│ └─ Marcar email como leído                                │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔗 Nodos del Workflow n8n

| Nodo | Tipo | Función | Entrada | Salida |
|---|---|---|---|---|
| **Gmail Trigger** | Trigger | Monitorea correos con PDF | N/A | messageId, attachmentId |
| **Marcar como leído** | Gmail | Marca email como leído | messageId | N/A |
| **Obtener mensaje completo** | HTTP | Descarga datos completos del email | messageId | payload, headers |
| **Extraer metadata adjunto** | Code | Busca todos los PDFs en email | payload.parts | attachmentId, pdf_filename |
| **Descargar adjunto Gmail API** | HTTP | Descarga PDF en base64 | attachmentId | data (base64) |
| **Preparar binario PDF** | Code | Convierte base64 a formato n8n | data | binary.data |
| **Extraer texto PDF** | Extract from File | OCR del PDF | binary | text |
| **Preparar payload** | Code | Calcula hash y prepara estructura | text | pdf_text, pdf_hash, fuente |
| **Preparar prompt OpenAI** | Code | Genera prompt con schema JSON | pdf_text | prompt para OpenAI |
| **[OpenAI Call]** | OpenAI | Extrae datos con GPT-4 | prompt | JSON parsed (factura) |
| **Normalizar campos OpenAI** | Code | Normaliza fechas, importes, CUIT | JSON parsed | Campos limpios |
| **Filtrar receptor Ingenio** | IF | ¿CUIT receptor es correcto? | cuit_receptor | ✓ Continue / ✗ Stop |
| **¿Necesita cotización BNA?** | IF | ¿Moneda es USD? | cotizacion_origen | ✓ BNA / ✗ Skip |
| **[BNA API Call]** | HTTP | Obtiene cotización USD → ARS | (condicional) | cotizacion, fecha_cot |
| **[DB Query - OC Lookup]** | SQL | Busca OC en CALIPSO | proveedor_cuit, referencia | oc_id, oc_total |
| **[DB Query - Constancias]** | SQL | Busca certificaciones | oc_id | constancia_requerida |
| **Insertar STAGING_FACTURAS** | SQL | Guarda factura en staging | Todos los datos | inserted_id |
| **Insertar STAGING_ITEMS** | SQL | Guarda items en staging | invoice_id, items | inserted_count |
| **Notificar completado** | Email/Webhook | Envía confirmación | inserted_id | N/A |

---

## 📊 Estructura de Datos

### Salida del Workflow (STAGING_FACTURAS)
```sql
INSERT INTO STAGING_FACTURAS (
  fuente,                    -- 'EMAIL_GMAIL_N8N'
  email_gmail_id,            -- ID Gmail del mensaje
  email_from,                -- Remitente
  email_subject,             -- Asunto
  email_date,                -- Fecha del email
  pdf_filename,              -- Nombre del archivo PDF
  pdf_hash,                  -- Hash SHA-256 del PDF
  
  letra,                     -- 'A', 'B', 'C'
  tipo_comprobante,          -- 'FACTURA_COMPRA'
  numero_completo,           -- 'XXXX-XXXXXXXX'
  fecha_emision,             -- YYYY-MM-DD
  fecha_vencimiento,         -- YYYY-MM-DD
  fecha_vto_cae,             -- YYYY-MM-DD
  cae,                       -- 14 dígitos
  
  cuit_emisor,               -- Proveedor
  razon_social_emisor,       -- Nombre
  cuit_receptor,             -- Comprador (30715098551)
  
  moneda,                    -- 'ARS', 'USD'
  cotizacion_usd,            -- Si es USD, cotización usada
  cotizacion_fecha,          -- Fecha de cotización BNA
  
  neto_gravado,              -- Importe neto
  iva_21,                    -- IVA 21%
  iva_105,                   -- IVA 10.5%
  percepciones,              -- Retenciones
  otros_impuestos,           -- Otros
  total,                     -- Total
  
  referencia,                -- Número de OC
  nro_remito,                -- Número de remito
  
  confianza_parseo,          -- 'ALTA', 'MEDIA', 'BAJA'
  notas_parseo,              -- Notas de OpenAI
  
  estado_proceso,            -- 'PENDIENTE' (inicial)
  fecha_registro             -- NOW()
) VALUES (...)
```

### STAGING_ITEMS (Ítems de factura)
```sql
INSERT INTO STAGING_ITEMS (
  factura_id,                -- FK a STAGING_FACTURAS
  linea,                     -- Número de línea
  descripcion,               -- Descripción del item
  cantidad,                  -- Cantidad
  unidad,                    -- 'u', 'kg', 'tn', etc
  precio_unitario,           -- Precio unitario
  alicuota_iva,              -- % IVA (0, 10.5, 21)
  subtotal                   -- cantidad * precio
) VALUES (...)
```

---

## 🔌 Puntos de Integración con MCP

### ACTUAL (n8n)
```
Gmail → PDF → OCR → OpenAI → SQL Staging
```

### PROPUESTO (con MCP + Python)
```
Gmail → PDF → OCR → OpenAI → VALIDACIÓN PYTHON ↓
                              ├─ MCP: get_purchase_orders
                              ├─ MCP: search_accounting_data
                              └─ Python: validation_engine.py
                                          ↓
                                      SQL Staging
                                      +
                                      Asientos Contables
```

### Nodos que se AGREGARÍAN al Workflow

1. **MCP: Obtener OC**
   ```
   Node: "MCP - Get Purchase Orders"
   Input:  cuit_proveedor, referencia_oc
   Output: oc_id, oc_total, oc_items[], requiere_constancia
   ```

2. **MCP: Buscar Proveedor**
   ```
   Node: "MCP - Search Suppliers"
   Input:  cuit, razon_social
   Output: proveedor_id, historico_compras, ultima_compra
   ```

3. **Python: Validar Factura**
   ```
   Node: "Python - Validate Invoice"
   Input:  factura_data, oc_data, historico
   Output: is_valid, errors[], warnings[], varianzas[]
   ```

4. **Python: Generar Asientos**
   ```
   Node: "Python - Generate Accounting Entries"
   Input:  factura_validada, oc, cuenta_proveedora
   Output: asientos[], cuenta_debe, cuenta_haber
   ```

5. **MCP: Registrar en CALIPSO** (FASE 3)
   ```
   Node: "MCP - Register Invoice CALIPSO"
   Input:  factura_id, asientos[], constancia_nro
   Output: calipso_id, fecha_registro
   ```

---

## 📋 Checklist de Integración n8n + MCP + Python

### FASE 2A: Preparación
- [ ] Crear API wrapper para MCP en Node.js
- [ ] Crear API wrapper para Python validator
- [ ] Mapear endpoints n8n a herramientas MCP

### FASE 2B: Integración de nodos
- [ ] Agregar nodo "MCP - Get Purchase Orders"
- [ ] Agregar nodo "MCP - Search Suppliers"
- [ ] Agregar nodo "Python - Validate Invoice"
- [ ] Agregar nodo "Python - Generate Accounting Entries"

### FASE 2C: Flujo Completo
- [ ] Conectar nodos en secuencia correcta
- [ ] Manejar errores y casos especiales
- [ ] Testing con PDFs de ejemplo

### FASE 2D: Validación
- [ ] Prueba con 10 facturas de ejemplo
- [ ] Verificar precisión del parseado
- [ ] Validar importes calculados
- [ ] Confirmar asientos contables

---

## 🎯 Objetivo Final (FASE 2 Semana 2)

Flujo completo end-to-end:
```
1. Usuario envía factura PDF a correo
   ↓
2. n8n descarga, extrae texto, llama OpenAI
   ↓
3. n8n consulta MCP para OC y proveedores
   ↓
4. Python validator valida factura vs OC
   ↓
5. Python genera asientos contables
   ↓
6. Datos guardados en STAGING + asientos en tabla
   ↓
7. Interfaz web muestra factura con validación
   ↓
8. Usuario aprueba → MCP registra en CALIPSO
```

---

## 🔧 Parámetros Importantes

| Parámetro | Valor Actual | Nota |
|---|---|---|
| Gmail polling | Cada 1 minuto | Configurable |
| Tamaño máx PDF | No limitado | Considerar |
| OpenAI Model | gpt-4-turbo | Configurable |
| CUIT Receptor | 30715098551 | Ingenio La Corona |
| CUIT Moneda | ARS (default) | Si USD → BNA |
| Constancia Requerida | OC comienza '01-' | Lógica actual |
| Email de notificación | compras@ingenio.ar | Configurable |

---

## 📞 Próximos Pasos

**Semana 1 - Días 3-5:**
1. [ ] Instalar Python en C:\claudecode\python
2. [ ] Crear Node.js API wrapper para MCP
3. [ ] Crear Node.js API wrapper para Python
4. [ ] Documentar endpoints disponibles

**Semana 2 - Días 6-7:**
1. [ ] Agregar nodo MCP al workflow n8n
2. [ ] Agregar nodo Python al workflow n8n
3. [ ] Testing de conexiones

**Semana 2 - Días 8-14:**
1. [ ] Testing end-to-end
2. [ ] Validación de casos
3. [ ] Refinamiento y optimización

---

**Analizado por**: GitHub Copilot  
**Fecha**: 6 de Junio 2026  
**Estado**: Listo para integración  
**Próxima Acción**: Crear wrappers de Node.js
