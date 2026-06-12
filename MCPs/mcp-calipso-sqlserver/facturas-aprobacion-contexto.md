# Contexto de aprobación y registro de facturas en Calipso

Este documento guarda el análisis del flujo de facturas aprobadas en el proyecto y los puntos clave para usarlo junto con CODEX / CLAUDECODE.

## 1. Camino actual en el repositorio

El sistema no registra las facturas directamente en `TRFACTURACOMPRA` desde el flujo de validación.
Se usa una cola de staging donde las facturas aprobadas quedan en `UD_EZI_STAGING_FACTURAS`.

Los pasos son:

1. Validar factura en `n8n/workflow_validacion_aprobacion.json`.
2. Insertar en staging con `UD_EZI_SP_STAGING_INSERTAR`.
3. Ver la cola aprobada con `UD_EZI_SP_STAGING_PENDIENTES`.
4. Cargar la factura en Calipso y generar el TR definitivo.
5. Marcar el registro de staging como procesado con `UD_EZI_SP_STAGING_MARCAR_PROCESADO`.

## 2. Stored procedures clave

- `UD_EZI_SP_STAGING_INSERTAR`
  - Valida proveedor activo por CUIT.
  - Verifica duplicado en `TRFACTURACOMPRA`.
  - Verifica duplicado activo en staging.
  - Valida hash de PDF duplicado.
  - Valida Constancia de Servicio para facturas de servicio.
  - Valida CAE y cuadre de totales.
  - Inserta en `UD_EZI_STAGING_FACTURAS` con estado `PENDIENTE`.

- `UD_EZI_SP_STAGING_PENDIENTES`
  - Lista registros de staging por estado.
  - Usado por el operador para saber qué facturas están listas para cargar.

- `UD_EZI_SP_STAGING_MARCAR_PROCESADO`
  - Cambia el estado de staging a `PROCESADO`.
  - Requiere `@ID` del staging y `@TR_GENERADO_ID` del TR real cargado en Calipso.

## 3. SQL de registro de factura aprobada

El SQL real utilizado en el flujo es una llamada a `UD_EZI_SP_STAGING_INSERTAR`.

Ejemplo de uso:

```sql
DECLARE @res INT, @msg VARCHAR(500);
EXEC UD_EZI_SP_STAGING_INSERTAR
    @UUID_OPERACION      = NEWID(),
    @TIPO_OPERACION      = 'FACTURA_COMPRA',
    @ORIGEN              = 'PDF_EMAIL',
    @USUARIO_CARGA       = 'middleware',
    @TIPOTRANSACCION_ID  = '50829758-5905-11D5-86C4-0080AD403F5F',
    @NUMERODOCUMENTO     = '0001-00001234',
    @LETRA               = 'A',
    @FECHA_EMISION       = '20260501',
    @FECHA_VENCIMIENTO   = NULL,
    @REFERENCIA          = 'OC1234',
    @PROVEEDOR_CUIT      = '30712345678',
    @NETO                = 10000,
    @IVA_21              = 2100,
    @IVA_105             = 0,
    @PERCEPCIONES        = 0,
    @OTROS_IMPUESTOS     = 0,
    @TOTAL               = 12100,
    @COTIZACION          = 1,
    @CAE                 = '12345678901234',
    @FECHA_VTO_CAE       = '20260515',
    @CENTROCOSTOS_CODIGO = NULL,
    @PDF_FILENAME        = 'factura.pdf',
    @PDF_HASH            = 'ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890',
    @EMAIL_ORIGEN        = 'origen@example.com',
    @EMAIL_ASUNTO        = 'Factura aprobada',
    @RESULTADO           = @res OUTPUT,
    @MENSAJE             = @msg OUTPUT;

SELECT @res AS resultado, @msg AS mensaje;
```

> En el archivo de ejemplo `sql/01a_ejemplo_insertar_factura_staging.sql` hay una plantilla lista para actualizar con datos reales.

## 4. Comentarios sobre `GO` y comentarios

- `GO` es un separador de lotes de SSMS/SQLCMD, no una instrucción SQL.
- Las líneas que comienzan con `--` son comentarios y no se ejecutan.
- El bloque:

```sql
-- EXEC UD_EZI_SP_STAGING_MARCAR_PROCESADO
--     @ID = 'UUID-STAGING',
--     @TR_GENERADO_ID = 'UUID-TR-CALIPSO',
--     @OPERADOR = 'pdietrich';
```

  es un ejemplo de llamada y debe permanecer comentado en el archivo de ejemplo.

## 5. Recomendación para CODEX / CLAUDECODE

Para usar este contexto con CODEX / CLAUDECODE:

- Lee `mcp-calipso-sqlserver/README.md` y `mcp-calipso-sqlserver/facturas-aprobacion-contexto.md`.
- Usa `sql/01a_ejemplo_insertar_factura_staging.sql` como referencia concreta.
- Nota que el flujo real de producción usa staging, no un `INSERT` directo en `TRFACTURACOMPRA`.
- Considera la separación de entornos: `CALIPSO_TEST` para análisis y `CORONA` de producción para el despliegue final.

## 6. Archivos relevantes

- `sql/01_crear_staging.sql`
- `sql/01a_ejemplo_insertar_factura_staging.sql`
- `n8n/workflow_validacion_aprobacion.json`
- `mcp-calipso-sqlserver/calipso-structure-analysis.txt`
