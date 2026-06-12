# Mapa de Datos - Base de Datos Corona

## Tablas de Cabecera (Ventas/Compras)
- **Base:** `FACTURAVENTA` / `FACTURACOMPRA`
  - Campos clave: `ID` (uniqueidentifier), `NUMERODOCUMENTO`, `FECHADOCUMENTO`, `DESTINATARIO_ID`.
- **Extendidas (EZI):** `UD_EZI_FACTURA_VENTA` / `UD_EZI_FACTURACOMPRA_CMPV`
  - Campos clave: `CAE`, `CBU`, `observacion`, `CBU`.

## Tablas de Detalle (Ítems)
- **Técnica:** `ITEMFACTURAVENTA` / `ITEMFACTURACOMPRA`
  - Vínculo: Se relacionan con la cabecera mediante el `ID` de la factura y `NUMERODOCUMENTO`.
  - Campos Críticos:
    - Logística: `BULTOS`, `PESO`, `VOLUMEN`, `DEPOSITOORI_ID`, `DEPOSITODES_ID`.
    - Contabilidad: `CUENTACONTABLE_ID`, `DISCRIMINADORCONTABLE1_ID` hasta el 10.
    - Financiero: `PRECIOUNITARIOFINAL`, `TOTALSINDESCUENTOS`, `CANTIDAD`.

## Relaciones
- El sistema utiliza `uniqueidentifier` (GUIDs) para casi todas las claves primarias y foráneas.
- Es imperativo usar `NEWID()` para crear nuevos registros vinculados entre tablas base y EZI.
