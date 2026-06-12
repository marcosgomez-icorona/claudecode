# Contexto del proyecto Sumas y Saldos – Gerencia

## Visión y alcance
Construir un tablero gerencial que permita a la Gerencia Administrativa del Ingenio La Corona visualizar **Sumas y Saldos**, **Mayores por cuenta**, **Libro Diario**, **Imputaciones**, **Egresos/Ingresos Calipso**, **Saldos y pendientes de proveedores**, y **Movimientos inter‑empresas**. El tablero consumirá inicialmente datos **mock** y quedará preparado para sincronización cada 8 h con Google Sheets a través de Node‑RED y consultas MCP a SQL Server 2008 R2.

## Arquitectura propuesta
- **Frontend**: HTML + Bootstrap 5, CSS custom, JavaScript Vanilla (modular).
- **Módulos JS** bajo `/assets/js/`:
  - `config.js` – parámetros de alertas y cuentas críticas.
  - `mockData.js` – datos de ejemplo para todas las hojas.
  - `dataService.js` – capa de acceso a datos (mock → futura API).
  - `alertsEngine.js` – motor de reglas de alerta.
  - `renderTables.js` – generación de tablas dinámicas.
  - `charts.js` – gráficos con Chart.js.
  - `app.js` – orquestador UI, manejo de filtros y navegación.
- **Backend**: `js/db_query.js` (Express + MSSQL) expone `/query` y se mantiene para pruebas posteriores.
- **Node‑RED**: flujo `flow_sumas_y_saldos.json` (GET `/api/sumas_y_saldos`).
- **Datos**: mock en `/assets/data/mockData.js` (JSON‑like) y `/assets/data/*.json` opcional.

## Documentación fuente (consolidada)
| Archivo | Contenido principal |
|--------|----------------------|
| `PROJECT_CONTEXT.md` | Alcance, criterios permanentes y arquitectura de referencia. |
| `FASE_0_RELEVAMIENTO.md` | Plan operativo de la primera etapa, preguntas abiertas. |
| `LAYOUT_SUMAS_SALDOS.md` | Wireframes y requisitos UI. |
| `ALERT_RULES.md` | Reglas candidatas de alertas contables. |
| `DATA_MODEL.md` | Modelo de datos esperado para cada hoja. |
| `MCP_CALIPSO_ANALISIS.md` | Análisis de vistas read‑only y consultas MCP. |
| `SNAPSHOT_MVP_SPEC.md` | Especificación del snapshot inicial a validar. |
| `DECISION_LOG.md` | Registro de decisiones técnicas y funcionales. |
| `README.md` | Resumen ejecutivo y guía de ejecución. |

## Supuestos y decisiones adoptadas
- El backend `db_query.js` sigue escuchando en `http://192.168.0.23:3000` (IP del servidor).  
- Los umbrales de alerta se definen en `config.js` y pueden modificarse sin redeploy.
- Se prioriza **no romper** la estructura actual; los archivos de UI se moverán a `/assets/…` y `index.html` se actualizará con referencias nuevas.
- No se implementa conexión real a SQL Server ni Google Sheets en esta fase; los placeholders estarán presentes.

## Pendientes y próximos pasos
- Validar mock contra la **planilla mayor_2026.xlsx**.
- Implementar consultas MCP definitivas en `db_query.js`.
- Integrar Node‑RED para sincronizar Google Sheets cada 8 h.
- Realizar pruebas de usuario con la Gerencia y ajustar alertas.

*Este documento constituye la única fuente de verdad del proyecto. Cualquier discrepancia con otros archivos deberá resolverse aquí.*
