# Continuidad — Sumas y Saldos v5.2.0 (2026-06-18)

## Decisiones técnicas de la sesión

1. **Vista por defecto últimos 10 días**: todas las secciones excepto Sumas y Saldos y Mayor muestran los últimos 10 días por defecto. Sumas y Saldos mantiene rango completo desde 2025-01-01. Los date pickers globales arrancan en `hoy - 10 días`.

2. **Filtro de fechas en Mayor por Cuenta**: se agregaron inputs `mayor-startDate` y `mayor-endDate` en el header de la sección, con valores default (10 días). El endpoint Node-RED `/api/sumas-saldos/mayor` ahora soporta filtrado por `startDate` y `endDate`.

3. **Imputaciones e Interempresas implementados**: endpoints nuevos con consultas MSSQL directas — sin pasar por Google Sheets en esta versión. El usuario puede agregar sync a GSheet después siguiendo el patrón de los otros módulos.

4. **Imputaciones**: consulta `V_EZI_IMPUTACIONES_C` (vista contable existente). Columnas: DESCRIPCION, COMPROBANTE, FECHAEMISION, NOMBREOPERADORCOMERCIAL, NETO, VALORTOTAL, NOMCLASIFICADOR. El tipo (VENTA/COMPRA/GASTO/AJUSTE) se deriva por prefijo de DESCRIPCION.

5. **Interempresas**: consulta `V_TRANSACCION` filtrando movimientos entre empresas del grupo (Bioenergia La Corona ↔ Sucroalcoholera del Sur). Nombres simplificados: "Ingenio La Corona" y "Destilería Corona S.A."

6. **Migración a GSheet**: Se crearon sync flows (Clear+Restore+Append) y se reescribió la API para leer de Google Sheets. Mismo patrón que los otros 7 módulos. Tabs nuevas: Imputaciones (A-J), Interempresas (A-J)..

## Archivos creados/modificados

### Modificados
- `webapp/assets/js/app.js` — Default startDate 10 días atrás
- `webapp/assets/js/api.js` — loadFromAPI() hardcodea startDate='20250101' para Sumas y Saldos
- `webapp/assets/js/config.js` — +2 endpoints: imputaciones, interempresas
- `webapp/assets/js/ui.js` — cargarMayor() con filtro fechas, STATE_IMP, STATE_INTER, cargarImputaciones(), cargarInterempresas(), renderAll() actualizado
- `webapp/index.html` — Filtros fecha en Mayor (mayor-startDate, mayor-endDate)
- `flujos/node-red/flow_sumas_y_saldos.json` — Endpoint Mayor actualizado con soporte de filtro por fecha

### Creados
- `flujos/node-red/flow_imputaciones_interempresas.json` — API flow v2.0.0 GSheet (10 nodos, 1 pestaña): endpoints `/api/sumas-saldos/imputaciones` y `/api/sumas-saldos/interempresas` leyendo de Google Sheets
- `flujos/node-red/sync_gsheet/flow_sync_imputaciones_gsheet_v1.json` — Sync flow: MSSQL V_EZI_IMPUTACIONES_C → GSheet Imputaciones (Clear+Append)
- `flujos/node-red/sync_gsheet/flow_sync_interempresas_gsheet_v1.json` — Sync flow: MSSQL V_TRANSACCION → GSheet Interempresas (Clear+Append)

## Próximos pasos

1. **Crear tabs en Google Sheet** `<SPREADSHEET_ID>` (configurada en el proyecto, no versionada):
   - Tab `Imputaciones` con headers: FECHA | TIPO | COMPROBANTE | ENTE | CLASIFICADOR | NETO | IVA | TOTAL | UNIDAD_NEGOCIO | SYNC_UUID
   - Tab `Interempresas` con headers: FECHA | DESDE | HACIA | CONCEPTO | COMPROBANTE | IMPORTE | TIPO | MONEDA | UNIDAD_NEGOCIO | SYNC_UUID
2. Importar en Node-RED 192.168.0.23:1880 los 3 flows nuevos
3. Ejecutar sync de Imputaciones e Interempresas (botón ▶ en cada flow)
4. Re-deployar `flow_sumas_y_saldos.json` (Mayor con filtro fechas)
5. Probar webapp: `cd webapp && python3 -m http.server 7070`
6. Deploy a Apache 192.168.0.23:7070

## Riesgos

- Interempresas: query filtra solo entre Bioenergia↔Sucroalcoholera. Si hay más entidades del grupo, no aparecen.
- Imputaciones: la derivación del tipo por prefijo de DESCRIPCION puede fallar en casos borde.
- Ambos endpoints leen MSSQL directo → si hay muchos datos, considerar mover a GSheet (misma arquitectura que los otros módulos).
- Si el endpoint Mayor recibe muchas filas sin filtro de fecha, puede ser lento → el frontend ahora siempre envía fechas.
