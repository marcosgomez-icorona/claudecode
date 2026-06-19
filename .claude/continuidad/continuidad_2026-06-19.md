# Continuidad — 2026-06-19 (Sesión 2)

## Tema
Automatización de Facturas de Compra — Análisis completo y generación de entregables

## Issues críticos detectados y resueltos

1. **BUG CRÍTICO en aprobar/rechazar** — T-SQL (`CONVERT`, `GETDATE`, `REPLACE`) usado en contexto JavaScript en los function nodes de `api_registracion_facturas-16-06-26.json`. Rompe timestamp de aprobación. **RESUELTO** en nuevo flow usando `new Date()` nativo de JS.

2. **Data flow inconsistente** — n8n escribe en MySQL `staging_facturas`, pero el flow API completo lee de SQL Server `UD_EZI_STAGING_FACTURAS`. Nunca se encuentran datos. **RESUELTO** creando flow unificado que lee facturas de MySQL y OC/constancias de SQL Server.

3. **Múltiples versiones de flow** — 5+ versiones de flow Node-RED con diferentes endpoints y DBs. **RESUELTO** creando único flow de 75 nodos con todos los endpoints.

4. **SQL scripts pendientes** — Scripts 06-09 nunca ejecutados en MySQL (6 meses pendientes). **RESUELTO** consolidando en `10_migracion_completa_mysql.sql`.

## Archivos creados/modificados

| Archivo | Acción | Propósito |
|---------|--------|-----------|
| `sql/10_migracion_completa_mysql.sql` | CREADO | Migración MySQL única (06+07+08+09+columnas extra) |
| `sql/11_crear_staging_sqlserver.sql` | CREADO | Crear UD_EZI_STAGING_FACTURAS + UD_EZI_STAGING_ITEMS en SQL Server |
| `nodered/flow_api_facturas_unificado_v2.json` | CREADO | Flow unificado 75 nodos (MySQL staging + SQL Server OC/constancias) |
| `nodered/flow_sync_calipso.json` | CREADO | Subflow sync MySQL → SQL Server (cada 5 min) |
| `nodered/build_unified_flow.py` | CREADO | Build script para regenerar el flow |
| `nodered/build_sync_flow.py` | CREADO | Build script para flow de sync |
| `docs/GUIA_DEPLOY.md` | CREADO | Guía de deploy paso a paso |
| `docs/superpowers/proposals/2026-06-19-skills-web-uiux-propuesta.md` | CREADO | Propuesta de skills UI/UX |
| `/home/soporte/.claude/skills/frontend-design.md` | CREADO | Skill oficial Anthropic copiado localmente |

## Próximos pasos inmediatos

1. ⚠️ Ejecutar `sql/10_migracion_completa_mysql.sql` en MySQL (phpMyAdmin)
2. ⚠️ Ejecutar `sql/11_crear_staging_sqlserver.sql` en SSMS
3. ⚠️ Instalar `node-red-contrib-mssql-plus` en Node-RED
4. ⚠️ Importar `flow_api_facturas_unificado_v2.json` en Node-RED
5. ⚠️ Importar `flow_sync_calipso.json` en Node-RED
6. Probar endpoints con curl
7. Activar n8n workflow (verificar POST url)
8. Probar flujo completo: email → n8n → Node-RED → MySQL → UI → sync

## Deuda técnica
- MSSQL queries con interpolación (no parametrizadas)
- Instalación de mssql-plus requerida para OC/constancias
- Proveedor_ID placeholder en sync SQL Server
- Config nodes requieren ajuste manual al importar

## Decisiones técnicas

1. **Patrón portable** (mismo que Conciliación Bancaria): carpeta `portable/` autónoma con HTML + CSS + JS vanilla, sin build tooling
2. **config.js multi-backend**: failover automático Node-RED → LAN → Cloud → mock data
3. **MySQL para tablas auxiliares** (nueva regla global CLAUDE.md): toda tabla no-ERP va en MySQL. SQL Server solo para ERP transaccional + SPs middleware
4. **SP en SQL Server + auditoría en MySQL**: `pr_ezi_vincular_factura` (SP middleware) toca `pr_ezi_remitos` en SQL Server. La auditoría se registra en `despachos_audit_factura` en MySQL desde Node-RED (rama separada, fire-and-forget)
5. **httpStatic recomendado** para servir portable/; el endpoint `/despachos-pendientes` como fallback inline

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
- `flows/flow_despachos_pendientes.json` — v1.0.0 → v1.1.0: HTML inline actualizado, endpoint `/facturar` migrado de UPDATE directo a SP
- `docs/node-red-despachos.md` — Documentado SP, auditoría, setup inicial

### Creados (Fase 2 — Middleware ERP + MySQL)
- `sql/01_crear_audit_factura.sql` — ⚠ OBSOLETO: tabla movida a MySQL
- `sql/02_crear_sp_vincular_factura.sql` — SP `pr_ezi_vincular_factura` en SQL Server (middleware ERP)
- `sql/03_test_sp.sql` — Pruebas del SP
- `sql/mysql/01_crear_audit_factura.sql` — Tabla `despachos_audit_factura` en MySQL (auditoría real)
- `sql/mysql/02_crear_sync_sheets.sql` — Tablas `despachos_sync_state` + `despachos_pendientes_cache` para sync futuro

### Modificado en CLAUDE.md (regla global)
- Stack: MySQL como servidor complementario obligatorio para tablas auxiliares
- Criterios permanentes: regla explícita — NUNCA crear tablas auxiliares en SQL Server
- Nueva sección "MySQL — servidor complementario" con qué va y qué no va

### Arquitectura del endpoint /facturar (v1.2.0)
```
POST /api/despachos/pendientes/:remito/facturar
  → fn_facturar_query (validar params, generar UUID)
  → mssql_facturar (EXEC pr_ezi_vincular_factura)  ← SQL Server (ERP)
  → fn_facturar_result (parse resultado)
      ├─[out 1]→ http_resp_facturar (respuesta al cliente)
      └─[out 2]→ mysql_audit_facturar (INSERT auditoría) ← MySQL (aux)
                      → fn_audit_done (log, fire-and-forget)
```

## Lo que NO se tocó (archivos legacy)
- `agente_despachos/index.html` — Versión original (mock-only)
- `agente_despachos/assets/` — JS/CSS originales (sin failover)
- `agente_despachos/flows/flow_despachos_pendientes_v2.json` — No modificado

## Próximos pasos (adaptado con MySQL)

### Setup (usuario)
1. **SQL Server**: ejecutar `sql/02_crear_sp_vincular_factura.sql` en SSMS
2. **MySQL**: ejecutar `sql/mysql/01_crear_audit_factura.sql` y `sql/mysql/02_crear_sync_sheets.sql`
3. **Node-RED**: importar flow v1.2.0, configurar credenciales MySQL, Deploy

### Fases siguientes (adaptadas)
3. **Fase 3 — Sync Google Sheets**: SQL Server → Node-RED → MySQL (staging `despachos_pendientes_cache`) → Google Sheets API. Endpoint `POST /api/despachos/sync-sheets` con estado en `despachos_sync_state`
4. **Fase 4 — Calidad de datos**: mockData realista, lookup tables en MySQL, filtro por estado, ordenamiento
5. **Fase 5 — Agente de Despachos**: Apps Script → MySQL (lee/escribe) → propuestas de clasificación

## Acciones pendientes del usuario
```bash
# 1. SQL Server (SSMS → CORONA en 192.168.0.177):
#      agente_despachos/sql/02_crear_sp_vincular_factura.sql
#      agente_despachos/sql/03_test_sp.sql

# 2. MySQL (mysql -h 127.0.0.1 -u root db_corona):
#      agente_despachos/sql/mysql/01_crear_audit_factura.sql
#      agente_despachos/sql/mysql/02_crear_sync_sheets.sql

# 3. Node-RED (http://192.168.0.23:1880):
#    Importar: agente_despachos/flows/flow_despachos_pendientes.json
#    Configurar password en nodo "db_corona (MySQL aux)"
#    Deploy
```

## Riesgos identificados

- El flow actualizado en disco necesita ser re-importado manualmente en Node-RED (192.168.0.23:1880)
- Sin httpStatic, el frontend modular no funciona desde Node-RED — solo funciona el HTML inline fallback
- La carpeta `portable/` no está en producción todavía; está en el repo esperando despliegue

## Links
- [[project_sumas_saldos_arquitectura]]
- [[project_conciliacion_bancaria]]
