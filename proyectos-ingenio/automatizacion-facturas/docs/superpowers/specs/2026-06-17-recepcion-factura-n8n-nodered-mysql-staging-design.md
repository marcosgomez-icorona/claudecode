# Especificación de Diseño: Adaptación n8n → Node-RED → MySQL Staging

**Fecha:** 2026-06-17  
**Versión:** 1.0  
**Estado:** Aprobado para implementación  
**Proyecto:** Automatización de Facturas — Carga Automática de Facturas de Proveedores

---

## 1. Objetivo

Adaptar el workflow n8n "Recepción y Análisis de Factura v9" para que **no escriba directamente en SQL Server Calipso** (regla de middleware obligatoria) y en su lugar envíe los datos extraídos vía POST al Node-RED (`http://192.168.0.23:1880/facturas/api/recepcion/factura`), quien los inserta en **MySQL staging_facturas** para que la UI de validación (`facturas_validacion.html`) pueda operar normalmente.

## 2. Arquitectura Final

```
Gmail → n8n (OCR + OpenAI)
              │
              ▼  POST /facturas/api/recepcion/factura
         ┌─────┴──────┐
         │  Node-RED   │
         │  (middleware)│
         └─────┬──────┘
               │ INSERT
               ▼
         ┌──────────────┐      ┌──────────────────┐
         │ MySQL staging │◄────│ UI Validación     │
         │ facturas      │      │ facturas_valida-  │
         │ + items       │ ───►│ cion.html         │
         └──────────────┘      └──────────────────┘
               │
               │ (futuro: aprobación → middleware → Calipso)
               ▼
         ┌──────────────┐
         │ SQL Server    │
         │ CORONA        │
         └──────────────┘
```

## 3. Cambios Detallados

### 3.1 En n8n — `Recepcion y Analisis de Factura_en_produccion.json`

#### Nodo "Normalizar campos OpenAI" (id: `22c87937-...`)
- **Eliminar:** generación de `sql_query` (INSERT directo a `UD_EZI_STAGING_FACTURAS` en SQL Server)
- **Agregar al output:** `fecha_vencimiento`, `fecha_vto_cae`, `cae`, `es_dolar`, `cotizacion_val`, `cotizacion_origen`, `email_subject`, `fuente`, `pdf_text`, `notas_parseo`

#### Nodo "Preparar payload limpio" (id: `a3ee1e64-...`)
- **Expandir** de 14 → ~28 campos enviados a Node-RED:
  - `staging_id`, `tipo_operacion`
  - `proveedor_cuit`, `proveedor_nombre`
  - `numerodocumento`, `letra`
  - `fecha_emision`, `fecha_vencimiento`
  - `cae`, `fecha_vto_cae`
  - `neto`, `iva_21`, `iva_105`, `percepciones`, `otros_impuestos`, `total`
  - `es_dolar`, `cotizacion`, `cotizacion_origen`, `cotizacion_aviso`
  - `referencia`, `nro_remito`
  - `items_json`, `items_count`
  - `pdf_filename`, `pdf_hash`
  - `email_origen`, `email_asunto`
  - `pdf_text`, `confianza_parseo`, `confianza_parseo_label`, `notas_parseo`, `fuente`

#### Nodo "POST a Node-RED" (id: `01470f05-...`)
- **URL:** `http://192.168.0.23:1880/facturas/api/recepcion/factura` (nuevo endpoint)
- **Body:** el payload expandido completo

#### Nodo "Verificar duplicado staging" (id: `760969eb-...`)
- **Eliminar** — Node-RED verifica duplicados y responde 409 si existe

#### Nodo "¿Ya existe en staging?" (id: `bee8ecfe-...`)
- **Re-conectar:** en lugar de recibir del check HTTP, recibe la respuesta del POST a Node-RED. Si status 409 → duplicado → descartar. Si status 200 → OK.

### 3.2 En Node-RED — `flow_api_facturas.json`

#### Nuevo endpoint: `POST /api/recepcion/factura`

##### Nodos agregados (6-7 nodos):

| # | Tipo | Nombre | Función |
|---|---|---|---|
| 1 | HTTP In | "POST recepcion factura" | url: `/api/recepcion/factura`, method: POST |
| 2 | Function | "Validar payload + check dup" | Valida campos obligatorios, prepara query de duplicado (por hash o doc+cuit) |
| 3 | MySQL | "Check duplicado" | SELECT EXISTS en staging_facturas |
| 4 | Function | "Insertar o responder dup" | Si existe → 409. Si no → INSERT en staging_facturas |
| 5 | Function | "Preparar items" | Desglosa items_json → INSERT en staging_facturas_items |
| 6 | MySQL | "INSERT items" | Ejecuta el INSERT |
| 7 | HTTP Response | (response) | 200 OK con id e items_inserted |

##### Campos obligatorios en payload:
- `staging_id`, `proveedor_cuit`, `proveedor_nombre`, `numerodocumento`, `fecha_emision`, `total`

##### Detección de duplicados:
```sql
SELECT id FROM staging_facturas 
WHERE (pdf_hash = ? AND pdf_hash IS NOT NULL) 
   OR (numerodocumento = ? AND proveedor_cuit = ?)
LIMIT 1
```

##### Respuestas del endpoint:
| Código | Body | Significado |
|---|---|---|
| 200 | `{ ok: true, id, items_inserted }` | Factura creada en staging |
| 400 | `{ ok: false, error: "..." }` | Payload inválido |
| 409 | `{ ok: false, error: "...", existente_id }` | Factura duplicada |
| 500 | `{ ok: false, error: "..." }` | Error interno |

## 4. Flujo End-to-End Resultante

```
1. Llega email a sectorcompras con PDF/imagen adjunto
2. n8n Gmail Trigger detecta (cada 1 minuto)
3. n8n descarga adjunto, extrae texto (PDF) o usa Vision (imagen)
4. n8n envía texto a OpenAI GPT-4o → recibe JSON estructurado
5. n8n normaliza campos (fechas, importes, CUITs)
6. n8n filtra: solo FACTURA_COMPRA sigue adelante
7. n8n: si es USD sin cotización → consulta BCRA (BNA)
8. n8n: confianza >= 50%? → sí → POST a Node-RED
9. Node-RED valida payload, checkea duplicado
10. Node-RED INSERT en MySQL staging_facturas + items
11. Node-RED responde 200 { ok: true }
12. n8n marca email como leído
13. UI facturas_validacion.html muestra la nueva factura en GET /api/facturas/pendientes
```

## 5. Archivos Afectados

| Archivo | Ruta | Acción |
|---|---|---|
| n8n workflow | `n8n/Recepcion y Analisis de Factura_en_produccion.json` | Modificar |
| Node-RED flow | `nodered/flow_api_facturas.json` | Agregar endpoint POST |
| Design doc | (este archivo) | Crear |

## 6. Riesgos y Controles

| Riesgo | Mitigación |
|---|---|
| n8n envía payload incompleto | Node-RED valida campos obligatorios → 400 |
| Se pierde data si Node-RED está caído | n8n manejará timeout con retry o log |
| Duplicado por diferencia de hash | Validación dual: hash + (documento + CUIT) |
| Items malformateados | try/catch en parseo JSON + insert por separado |
| Inconsistencia si falla INSERT items pero no factura | Se inserta factura primero, items después; respuesta indica cuántos items OK |

## 7. Próximos Pasos (Implementación)

1. Modificar n8n workflow (3 nodos)
2. Agregar endpoint POST en Node-RED (6-7 nodos)
3. Desplegar en test
4. Probar con factura real
5. Desplegar a producción
