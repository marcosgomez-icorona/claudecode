# Sprint 1 — Automatización Facturas de Compra
## Ingenio La Corona | Instrucciones de instalación

---

## PASO 1 — Base de datos (SSMS, 5 minutos)

1. Abrir SSMS → conectar a `serverico`
2. Seleccionar base `CORONA`
3. Abrir archivo: `sql/01_crear_staging.sql`
4. Ejecutar (F5)
5. Verificar que la tabla final muestre 6 filas con estado `OK`

---

## PASO 2 — Node-RED (servidor on-prem)

### Instalar dependencia PDF
```bash
cd ~/.node-red
npm install pdf-parse
# Reiniciar Node-RED después
```

### Importar el flow
1. Node-RED → Menú hamburguesa → Import
2. Pegar contenido de `nodered/flow_facturas_email.json`
3. Configurar nodo `Bandeja Facturas`:
   - Server: `mail.ingeniolacorona.com`
   - Port: `993` (SSL)
   - User: `ia.fabrica@ingeniolacorona.com`
   - Password: [contraseña de la cuenta]
   - Check interval: `300` (5 minutos)
4. Configurar nodo `POST a n8n`:
   - URL: `https://[URL-de-tu-n8n]/webhook/facturas-entrada`
5. Deploy

---

## PASO 3 — n8n (cloud)

1. Importar workflow desde `n8n/workflow_validacion_aprobacion.json`
2. Configurar credenciales:
   - **SQL Server**: apuntar a `serverico`, base `CORONA`
   - **Email (SMTP)**: cuenta para envío de notificaciones
3. Configurar la URL del servidor n8n en el campo `Webhook Aprobación`
4. Activar el workflow

---

## PASO 4 — Prueba del circuito completo

### Test manual (sin email)
Ejecutar directo en SSMS:
```sql
DECLARE @res INT, @msg VARCHAR(500)
EXEC UD_EZI_SP_STAGING_INSERTAR
    @UUID_OPERACION     = NEWID(),
    @TIPO_OPERACION     = 'FACTURA_COMPRA',
    @ORIGEN             = 'TEST_MANUAL',
    @USUARIO_CARGA      = 'pdietrich',
    @TIPOTRANSACCION_ID = '50829758-5905-11D5-86C4-0080AD403F5F',
    @NUMERODOCUMENTO    = '001200099999',
    @LETRA              = 'A',
    @FECHA_EMISION      = '20260424',
    @FECHA_VENCIMIENTO  = '20260524',
    @REFERENCIA         = '',
    @PROVEEDOR_CUIT     = '30711327912',
    @NETO               = 100000,
    @IVA_21             = 21000,
    @IVA_105            = 0,
    @PERCEPCIONES       = 0,
    @OTROS_IMPUESTOS    = 0,
    @TOTAL              = 121000,
    @COTIZACION         = 1,
    @CAE                = '12345678901234',
    @FECHA_VTO_CAE      = '20260504',
    @CENTROCOSTOS_CODIGO = '',
    @PDF_FILENAME       = 'test.pdf',
    @PDF_HASH           = 'abc123test',
    @EMAIL_ORIGEN       = 'test@test.com',
    @EMAIL_ASUNTO       = 'Prueba',
    @RESULTADO          = @res OUTPUT,
    @MENSAJE            = @msg OUTPUT

SELECT @res AS resultado, @msg AS mensaje
```

Resultado esperado: `resultado = 0`, mensaje = `OK: FACTURA_COMPRA A 001200099999...`

> Alternativa: abrir y ejecutar `sql/01a_ejemplo_insertar_factura_staging.sql` para ver el ejemplo completo con el `EXEC UD_EZI_SP_STAGING_INSERTAR` y el post-proceso de staging.

### Ver cola de trabajo
```sql
EXEC UD_EZI_SP_STAGING_PENDIENTES @ESTADO = 'PENDIENTE'
```

### Ver resumen dashboard
```sql
EXEC UD_EZI_SP_STAGING_RESUMEN
```

### Aprobar manualmente un registro (simular click en email)
```sql
UPDATE UD_EZI_STAGING_FACTURAS
SET ESTADO_PROCESO = 'APROBADO', APROBADO_POR = 'pdietrich'
WHERE NUMERODOCUMENTO = '001200099999'
```

### Ver pendientes de carga en Calipso
```sql
EXEC UD_EZI_SP_STAGING_PENDIENTES @ESTADO = 'APROBADO'
```

---

## FLUJO DIARIO DE pdietrich

**Mañana (10 min):**
```sql
-- 1. Ver qué hay para cargar hoy
EXEC UD_EZI_SP_STAGING_PENDIENTES

-- 2. Por cada factura: abrir Calipso, cargar con los datos de la cola
--    Registrar el ID del TR generado por Calipso

-- 3. Marcar procesado (reemplazar los UUIDs)
EXEC UD_EZI_SP_STAGING_MARCAR_PROCESADO
    @ID             = 'UUID-DEL-STAGING',
    @TR_GENERADO_ID = 'UUID-TR-CALIPSO',
    @OPERADOR       = 'pdietrich'
```

**Resumen semanal:**
```sql
-- Estado general del pipeline
EXEC UD_EZI_SP_STAGING_RESUMEN

-- Facturas procesadas esta semana
SELECT NUMERODOCUMENTO, PROVEEDOR_NOMBRE, TOTAL, FECHA_PROCESO, PROCESADO_POR
FROM UD_EZI_STAGING_FACTURAS
WHERE ESTADO_PROCESO = 'PROCESADO'
  AND FECHA_PROCESO >= CONVERT(varchar(8), DATEADD(DAY,-7,GETDATE()), 112)
ORDER BY FECHA_PROCESO DESC
```

---

## GUIDs de referencia (producción — base CORONA)

| Concepto | GUID |
|---|---|
| Compañía principal | `FC20C32D-3EFA-11D5-86AD-0080AD403F5F` |
| Moneda Pesos | `76C69765-3DAE-11D5-B059-004854841C8A` |
| Moneda Dólares | `76C69768-3DAE-11D5-B059-004854841C8A` |
| TipoTR Fact.Cpra. | `50829758-5905-11D5-86C4-0080AD403F5F` |
| TipoTR ConstServ simple | `E5887DA3-618D-11D5-931E-00E07D9040B9` |
| TipoTR ConstServ c/PtoVta | `08D2A275-E50E-47B5-90A6-5E06088DA3CA` |

---

## Archivos entregados

```
automatizacion-facturas/
├── sql/
│   └── 01_crear_staging.sql       ← Ejecutar en SSMS primero
├── nodered/
│   └── flow_facturas_email.json   ← Importar en Node-RED
├── n8n/
│   └── workflow_validacion_aprobacion.json  ← Importar en n8n
└── docs/
    └── INSTRUCCIONES_SPRINT1.md   ← Este archivo
```
