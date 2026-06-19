# Continuidad — 2026-06-19

## Tema
Fase 1 — Arquitectura portable para Despachos Pendientes de Facturación

## Decisiones técnicas

1. **Patrón portable** (mismo que Conciliación Bancaria): carpeta `portable/` autónoma con HTML + CSS + JS vanilla, sin build tooling
2. **config.js multi-backend**: failover automático Node-RED → LAN → Cloud → mock data. Detecta protocolo `file://` para saltar backends sin URL
3. **dataService.js con failover**: itera backends en orden, timeout por backend, fallback a mock si todos fallan
4. **Inline HTML en Node-RED**: se mantuvo como fallback autónomo (no depende de archivos), pero actualizado al design system Corona
5. **httpStatic es el método recomendado** para producción; el endpoint `/despachos-pendientes` sirve como fallback

## Archivos modificados o creados

### Creados (`agente_despachos/portable/`)
- `config.js` — Multi-backend: `{name, url, timeout}[]`
- `index.html` — Dashboard completo (sidebar, KPIs, filtros, tabla, modal)
- `js/app.js` — Orquestador con estado, KPIs, filtros, manejo de errores
- `js/dataService.js` — Failover multi-backend + fallback mock
- `js/renderTables.js` — Copiado, import path corregido `../config.js`
- `js/mockData.js` — Copiado del original
- `css/styles.css` — Copiado del original (design system Corona)
- `serve.sh` / `serve.bat` — Servidores HTTP locales para desarrollo
- `README.md` — Documentación de modos A/B/C

### Modificados
- `flows/flow_despachos_pendientes.json` — Versión 1.0.0, HTML inline actualizado al design system Corona, header con documentación de httpStatic

## Lo que NO se tocó (archivos legacy)
- `agente_despachos/index.html` — Versión original (mock-only)
- `agente_despachos/assets/` — JS/CSS originales (sin failover)
- `agente_despachos/flows/flow_despachos_pendientes_v2.json` — No modificado

## Próximos pasos

1. **Importar el flow actualizado en Node-RED** — El endpoint `/despachos-pendientes` ahora sirve HTML inline con design system Corona v1.0.0
2. **Configurar httpStatic** en `settings.js` de Node-RED para servir `portable/` → modo A con JS modules
3. **Fase 2 — Middleware ERP**: crear SP `pr_ezi_vincular_factura`, migrar POST `/facturar` de UPDATE directo a SP
4. **Fase 3 — Sync Google Sheets**: endpoint `POST /api/despachos/sync-sheets`
5. **Fase 4 — Calidad de datos**: mockData realista, filtro por estado, ordenamiento por columna

## Riesgos identificados

- El flow actualizado en disco necesita ser re-importado manualmente en Node-RED (192.168.0.23:1880)
- Sin httpStatic, el frontend modular no funciona desde Node-RED — solo funciona el HTML inline fallback
- La carpeta `portable/` no está en producción todavía; está en el repo esperando despliegue

## Links
- [[project_sumas_saldos_arquitectura]]
- [[project_conciliacion_bancaria]]
