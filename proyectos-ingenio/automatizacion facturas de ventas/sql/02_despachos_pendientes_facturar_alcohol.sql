/*
Objetivo:
  Listar remitos/despachos de alcohol pendientes de facturar.

Fuente validada por MCP readonly contra CORONA el 2026-06-05:
  - dbo.pr_ezi_remitos_alcohol
  - dbo.pr_ezi_remitos_alcohol_items

Regla operativa inicial:
  Pendiente = remito importado/no anulado con campo factura vacio o NULL.

Compatibilidad:
  SQL Server 2008 R2.
  Solo lectura. No ejecutar escrituras directas sobre ERP.
*/

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
    i.orden;
