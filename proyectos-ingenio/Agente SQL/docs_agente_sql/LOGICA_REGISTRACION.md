# Lógica de Registración Automatizada

## Algoritmo de Inserción en Cascada
Para evitar la corrupción de datos, el agente debe seguir estrictamente este orden:

1. **Búsqueda de Identificadores:**
   - Buscar `ProveedorID` en `PROVEEDOR` mediante el nombre.
   - Buscar `CuentaContableID` en `CUENTASCONTABLES` mediante el nombre.
   - Obtener `PlaceID` y `UserID` por defecto.

2. **Transacción SQL (`BEGIN TRANSACTION`):**
   - **Paso A:** Insertar en `FACTURACOMPRA` / `FACTURAVENTA`.
   - **Paso B:** Insertar en `UD_EZI_FACTURACOMPRA_CMPV` / `UD_EZI_FACTURA_VENTA` usando el mismo ID.
   - **Paso C:** Insertar cada ítem en `ITEMFACTURACOMPRA` / `ITEMFACTURAVENTA`.

3. **Validación:**
   - Si cualquier paso falla, ejecutar `ROLLBACK`.
   - Si todo es correcto, ejecutar `COMMIT`.

## Script Maestro Implementado
Se desarrolló un script SQL que utiliza variables de entrada (`@NombreProveedor`, `@NroDoc`, etc.) y realiza las búsquedas de IDs automáticamente mediante subconsultas `SELECT TOP 1`.
