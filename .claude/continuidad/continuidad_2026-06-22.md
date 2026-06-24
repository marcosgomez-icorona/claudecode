# Compactado Técnico — 2026-06-22 (ACTUALIZADO 16:30)

## Tema: Fix bug filtro de fechas Sumas y Saldos + REPARACIÓN de emergencia

---

## Decisiones técnicas

### 1. Regla: adaptar flujos existentes, no regenerar
- **Qué**: Todos los flujos de Sumas y Saldos se adaptan desde `gerencia_sumas_y_saldos/flujos/node-red/`. No se crean flows nuevos.
- **Por qué**: El usuario pidió explícitamente reutilizar los flows existentes.
- **Memory actualizada**: `project_sumas_saldos_arquitectura.md`

### 2. (MAÑANA) Fix MSSQL directo — ROLLBACKEADO
- El cambio v3→v4 agregó query MSSQL directa al endpoint principal. **El JOIN de 5 vistas provocó timeout de 15s** en Node-RED.
- **Rollback**: Restaurada ruta GSheet como primary. MSSQL queda desconectado (output 0 inactivo).
- **Estado**: v5.1.0 — GSheet primary, MSSQL solo para movimientos.

### 3. (TARDE) Reparación completa del sistema
- **Endpoint principal**: Restaurado a GSheet (`return [null, msg]` → output 1). Funciona con 810 cuentas.
- **Query movimientos**: `=` → `LIKE` para soportar código numérico parcial (`01.01.01.01.01` matchea `01.01.01.01.01 - Caja Ingenio`).
- **Health check**: v5.0.0 → v5.1.0, arquitectura real: "gsheet-primary-mssql-detail".
- **Google Sheets**: Verificado sync diario funcionando (hoy 09:00, 810 cuentas, 1869 egresos, 1869 libro diario).

---

## Archivos modificados (sesión tarde)

| Archivo | Cambio |
|---------|--------|
| `gerencia_sumas_y_saldos/flujos/node-red/back-end-sumas-saldos.json` | Health check v5.1.0, movements query LIKE, Validar params restaurado GSheet |
| `.mcp.json` | Browser domains (+`192.168.0.23`, +`localhost`), fresh Node-RED token |
| Node-RED deployed (tab `b1eb41e7e3c411ad`) | 3 nodos: Validar params (`5d0b805d`), Health (`2043dbfd`), Movements (`2e8c1f4b`) |

### Nodos desplegados modificados

| Node ID | Nombre | Cambio |
|---------|--------|--------|
| `5d0b805d33c5d088` | 1. Validar params (GSheet) | `return [null, msg]` → GSheet path |
| `2043dbfd293ed447` | Health check | v5.0.0 → v5.1.0 |
| `2e8c1f4bd48a8f1f` | Validar codigo → query MSSQL | `=` → `LIKE '%'` |

---

## Google Sheets — Estado verificado

- **Spreadsheet**: `<ID>` (configurada en Google Sheets, no versionada)
- **SA**: `<service-account>@<project>.iam.gserviceaccount.com` (configurado en `.secrets/`, no versionado)
- **Último sync**: 2026-06-22 09:00:48 (automático, exitoso)
- **Schedule**: Diario 06:00 + 06:30 (tab "SYNC - Tablero Sys a GSheet", 79 nodos)
- **Hojas activas**: SumasSaldos, LibroDiario, EgresosValores, IngresosValores, ProveedoresSaldos, ProveedoresPendientes, Imputaciones, Interempresas
- **Datos**: 810 cuentas en SumasSaldos, 1869 egresos, 1869 libro diario

---

## Próximos pasos inmediatos

1. **Deployar webapp a Apache** (puerto 7070) — carpeta `webapp/` lista, JS modular (6 módulos). Actualmente solo se sirve desde Node-RED en `/sumas-saldos`.
2. **Verificar sync mañana 07:00** — Confirmar que el sync de las 06:00/06:30 se ejecutó correctamente.
3. **Investigar timeout MSSQL** — Si se quiere reactivar ruta directa, optimizar JOIN (vista materializada, stored procedure, o indexación).
4. **Corregir sync granularidad** — El PERIODO en GSheet es `20241230-YYYYMMDD` (rango fijo acumulado). Para filtro por mes real, cambiar a granularidad mensual.
5. **Conectar frontend con endpoints modulares** — El `index.html` monolítico (2348 líneas) no consume los endpoints individuales (mayor, libroDiario, etc.). La webapp modular (`webapp/`) sí lo hace.
6. **Corregir column swap** — UNIDAD_NEGOCIO ↔ SYNC_UUID intercambiadas en sync GSheet.

---

## Deuda técnica

- MSSQL nodes (`f885760fbbe34d26`, `21441eb16e8d8767`) quedan en el flow pero output 0 inactivo. Evaluar eliminarlos si no se reactivan.
- Query MSSQL movimientos sin timeout explícito; el nodo usa 25s default.
- Browser MCP domains requieren reinicio de sesión para tomar efecto.
- Token Node-RED expira 2026-06-29. Refrescar: `bash MCPs/mcp-nodered/refresh_token.sh`.
- Flow local vs deployed tienen node IDs diferentes (export vs import). Si se reimporta el JSON local, se regeneran IDs.
