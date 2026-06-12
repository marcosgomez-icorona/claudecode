# Contexto MCP - Facturas de venta de azucar y alcohol

Fecha de analisis: 2026-06-05
Base consultada por MCP: `CORONA`
Modo usado: readonly, solo `SELECT`
SQL Server: 2008 R2

## Objetivo

Automatizar la mayor parte posible del proceso de facturacion de venta de azucar y alcohol, partiendo de despachos/remitos pendientes de facturar y pasando por una interfaz de validacion de usuario autorizado antes de generar la factura definitiva en Calipso.

## Regla permanente

- No escribir directo en tablas del ERP Calipso.
- Toda registracion debe pasar por middleware, staging, validacion, logs y autorizacion humana.
- Para desarrollo de escritura se debe usar TEST/restaurada, no `CORONA`.
- Toda consulta debe ser compatible con SQL Server 2008 R2.

## Hallazgos confirmados

### Fuente operativa de pendientes

Las tablas con movimiento real para pendientes de facturacion son:

- Azucar:
  - `dbo.pr_ezi_remitos`
  - `dbo.pr_ezi_remitos_items`
- Alcohol:
  - `dbo.pr_ezi_remitos_alcohol`
  - `dbo.pr_ezi_remitos_alcohol_items`

Regla inicial de pendiente:

```sql
(factura IS NULL OR LTRIM(RTRIM(factura)) = '')
AND ISNULL(estado, '') NOT IN ('ANULADO', 'Anulado', 'ANULADA', 'Anulada')
```

En las muestras reales, `importado = 1` aparece en pendientes recientes. Confirmar si debe ser filtro obligatorio.

### Tablas Calipso/UD relevantes

Facturas de venta:

- `dbo.TRFACTURAVENTA`
- `dbo.UD_EZI_FACTVENTA`
- `dbo.UD_EZI_FACTVENTA_ITEM`
- `dbo.UD_EZI_FACTURA_VTA_ALCOHOL`

Relacion validada:

```sql
TRFACTURAVENTA.BOEXTENSION_ID = UD_EZI_FACTVENTA.ID
```

Remitos/extensiones:

- `dbo.UD_EZI_REMITO_VTA_AZ`
- `dbo.UD_EZI_REMITO_VTA_ITEM`
- `dbo.UD_EZI_REMITO_VTA_ALC`
- `dbo.UD_EZI_REMITO_VTA_AL_ITEM`
- `dbo.UD_EZI_REMITO_VTA_AL_ITEM1`

Tablas `PLANILLADESPACHO`, `TRPLANILLADESPACHO`, `ITEMPLANILLADESPACHO` existian pero estaban vacias en `CORONA` durante el analisis.

### Vista rota

`dbo.V_EZI_REMITOS_AZUCAR_ENVIAR` existe y parece funcionalmente prometedora, pero no debe usarse hoy porque falla por dependencia rota:

```text
Invalid object name 'INTERFACE.dbo.REMITOS_ITEMS_CALIPSO'
Could not use view or function 'dbo.V_EZI_REMITOS_AZUCAR_ENVIAR' because of binding errors.
```

## Consulta base - azucar pendiente

```sql
SELECT
    r.id,
    r.remito,
    r.numeroremito,
    r.fecha,
    r.razonsocial,
    r.cuit,
    r.factura,
    r.confirmado,
    r.cumplido,
    r.importado,
    r.estado,
    r.pesoremito,
    r.cantidadremito,
    r.unidadmedida,
    r.patente,
    r.chasis,
    r.chofer,
    r.dniChofer,
    i.orden,
    i.producto,
    i.descripcion,
    i.cantidad,
    i.unidadmedida AS item_unidadmedida,
    i.cantidad2,
    i.unidamedida2,
    i.precio
FROM dbo.pr_ezi_remitos r
LEFT JOIN dbo.pr_ezi_remitos_items i
    ON i.Remito = r.remito
WHERE
    (r.factura IS NULL OR LTRIM(RTRIM(r.factura)) = '')
    AND ISNULL(r.estado, '') NOT IN ('ANULADO', 'Anulado', 'ANULADA', 'Anulada')
ORDER BY
    r.fecha DESC,
    r.remito DESC,
    i.orden
```

## Consulta base - alcohol pendiente

```sql
SELECT
    r.id,
    r.remito,
    r.numeroremito,
    r.fecha,
    r.razonsocial,
    r.cuit,
    r.factura,
    r.confirmado,
    r.cumplido,
    r.importado,
    r.estado,
    r.nroAnalisis,
    r.gl,
    r.ltsAlcohol,
    r.neto,
    r.bruto,
    r.pesoremito,
    r.cantidadremito,
    r.unidadmedida,
    r.patente,
    r.chasis,
    r.chofer,
    r.dniChofer,
    i.orden,
    i.producto,
    i.descripcion,
    i.cantidad,
    i.unidadmedida AS item_unidadmedida
FROM dbo.pr_ezi_remitos_alcohol r
LEFT JOIN dbo.pr_ezi_remitos_alcohol_items i
    ON i.Remito = r.remito
WHERE
    (r.factura IS NULL OR LTRIM(RTRIM(r.factura)) = '')
    AND ISNULL(r.estado, '') NOT IN ('ANULADO', 'Anulado', 'ANULADA', 'Anulada')
ORDER BY
    r.fecha DESC,
    r.remito DESC,
    i.orden
```

## Diseno recomendado MVP

1. API readonly consulta pendientes de azucar y alcohol.
2. Interfaz interna muestra pendientes con filtros por producto, fecha, cliente, CUIT, orden y remito.
3. Usuario selecciona remitos compatibles.
4. Sistema valida:
   - CUIT presente y valido.
   - Cliente presente.
   - Precio mayor a cero o aprobacion reforzada.
   - Cantidad mayor a cero.
   - Unidad de medida esperada.
   - Remito sin factura.
   - Alcohol con datos requeridos: GL, litros, analisis/protocolo si aplica.
5. Sistema crea prefactura/staging con UUID.
6. Usuario autorizado aprueba.
7. Middleware genera factura en Calipso por mecanismo autorizado.
8. Se registra resultado, numero de factura o error.

## Estados sugeridos para staging

- `PENDIENTE`
- `EN_REVISION`
- `APROBADO`
- `ENVIADO_CALIPSO`
- `FACTURADO`
- `ERROR`
- `ANULADO`

## Preguntas pendientes

1. La factura debe ser una por remito, una por orden, o agrupada por cliente?
2. Quienes son usuarios autorizados para aprobar?
3. `importado = 1` debe ser filtro obligatorio para facturar?
4. `confirmado` y `cumplido` deben bloquear o solo informar?
5. Que hacer con CUIT `XX` o NULL?
6. Que hacer con precio cero?
7. Para alcohol, que campos son obligatorios: GL, litros absolutos, protocolo, nro analisis, neto/bruto?
8. Para azucar, cual es la unidad final de facturacion: bolsa, kg, tonelada u otra?
9. El campo `factura` lo completa Calipso, un proceso externo, o debe completarlo el middleware?
10. Existe base TEST actualizada para probar staging y alta de facturas?

## Archivos espejo en proyecto

En el repo `C:\claudecode\proyectos-ingenio\automatizacion facturas de ventas` se dejaron:

- `sql/01_despachos_pendientes_facturar_azucar.sql`
- `sql/02_despachos_pendientes_facturar_alcohol.sql`
- `sql/03_validaciones_fuentes_facturacion.sql`
- `docs/arquitectura_automatizacion_facturas_venta.md`
- `tools/mcp-readonly-query.mjs`
