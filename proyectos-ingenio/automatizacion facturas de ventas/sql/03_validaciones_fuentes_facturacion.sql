/*
Objetivo:
  Validaciones de diagnostico para confirmar fuentes antes de automatizar.

Notas MCP 2026-06-05:
  - dbo.V_EZI_REMITOS_AZUCAR_ENVIAR existe pero falla por dependencia rota:
    INTERFACE.dbo.REMITOS_ITEMS_CALIPSO.
  - Las tablas PLANILLADESPACHO/TRPLANILLADESPACHO estaban vacias en CORONA.
  - Las tablas pr_ezi_remitos y pr_ezi_remitos_alcohol contienen remitos reales.
  - TRFACTURAVENTA enlaza con UD_EZI_FACTVENTA por BOEXTENSION_ID.
*/

SELECT 'azucar_remitos_total' AS metrica, COUNT(*) AS cantidad
FROM dbo.pr_ezi_remitos
UNION ALL
SELECT 'azucar_remitos_pendientes', COUNT(*)
FROM dbo.pr_ezi_remitos
WHERE (factura IS NULL OR LTRIM(RTRIM(factura)) = '')
  AND ISNULL(estado, '') NOT IN ('ANULADO', 'Anulado', 'ANULADA', 'Anulada')
UNION ALL
SELECT 'alcohol_remitos_total', COUNT(*)
FROM dbo.pr_ezi_remitos_alcohol
UNION ALL
SELECT 'alcohol_remitos_pendientes', COUNT(*)
FROM dbo.pr_ezi_remitos_alcohol
WHERE (factura IS NULL OR LTRIM(RTRIM(factura)) = '')
  AND ISNULL(estado, '') NOT IN ('ANULADO', 'Anulado', 'ANULADA', 'Anulada')
UNION ALL
SELECT 'facturas_venta_tr_total', COUNT(*)
FROM dbo.TRFACTURAVENTA
UNION ALL
SELECT 'facturas_venta_ud_ezi_factventa', COUNT(*)
FROM dbo.TRFACTURAVENTA tr
INNER JOIN dbo.UD_EZI_FACTVENTA ud
    ON ud.ID = tr.BOEXTENSION_ID;
