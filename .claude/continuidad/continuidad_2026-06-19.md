# Continuidad — 2026-06-19

## Tema
Fase 1 + 2 — Arquitectura portable + Middleware ERP para Despachos Pendientes de Facturación

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
- `flows/flow_despachos_pendientes.json` — v1.0.0 → v1.1.0: HTML inline actualizado, endpoint `/facturar` migrado de UPDATE directo a SP
- `docs/node-red-despachos.md` — Documentado SP, auditoría, setup inicial

### Creados (Fase 2 — Middleware ERP)
- `sql/01_crear_audit_factura.sql` — Tabla `pr_ezi_audit_factura` (UUID, remito, factura, usuario, resultado, timestamp)
- `sql/02_crear_sp_vincular_factura.sql` — SP `pr_ezi_vincular_factura` con validación + transacción + auditoría
- `sql/03_test_sp.sql` — Pruebas unitarias del SP (casos negativos + positivo)

### Arquitectura del SP
```
POST /api/despachos/pendientes/:remito/facturar
  → Node-RED Function (validar params, generar UUID)
  → MSSQL node (EXEC pr_ezi_vincular_factura @remito, @factura, @usuario, @run_uuid)
  → SP: valida → BEGIN TRAN → UPDATE → INSERT auditoría → COMMIT
  → Function (parse resultado: success/mensaje/auditId)
  → HTTP Response
```
Reglas implementadas en el SP:
- Remito no vacío, factura no vacía
- Remito debe existir en pr_ezi_remitos
- No sobrescribe factura existente
- Transacción atómica (UPDATE + INSERT auditoría)
- Concurrency-safe (WHERE factura IS NULL en el UPDATE)
- Auditoría incluso en fallos

## Lo que NO se tocó (archivos legacy)
- `agente_despachos/index.html` — Versión original (mock-only)
- `agente_despachos/assets/` — JS/CSS originales (sin failover)
- `agente_despachos/flows/flow_despachos_pendientes_v2.json` — No modificado

## Próximos pasos

1. **Ejecutar SQL en SSMS** contra CORONA:
   - `sql/01_crear_audit_factura.sql`
   - `sql/02_crear_sp_vincular_factura.sql`
   - `sql/03_test_sp.sql`
2. **Importar flow v1.1.0** en Node-RED (192.168.0.23:1880) — incluye endpoint `/facturar` con SP
3. **Configurar httpStatic** en `settings.js` de Node-RED para servir `portable/`
4. **Fase 3 — Sync Google Sheets**: endpoint `POST /api/despachos/sync-sheets`
5. **Fase 4 — Calidad de datos**: mockData realista, filtro por estado, ordenamiento por columna

## Acciones pendientes del usuario

```bash
# 1. Ejecutar scripts SQL en SSMS conectado a CORONA (192.168.0.177)
#    Abrir y ejecutar en orden:
#      agente_despachos/sql/01_crear_audit_factura.sql
#      agente_despachos/sql/02_crear_sp_vincular_factura.sql
#      agente_despachos/sql/03_test_sp.sql

# 2. Re-importar flow en Node-RED:
#    Abrir http://192.168.0.23:1880
#    Menu → Import → Clipboard
#    Pegar contenido de agente_despachos/flows/flow_despachos_pendientes.json
#    Deploy

# 3. Probar vinculación:
#    curl -X POST http://192.168.0.23:1880/api/despachos/pendientes/0099-00001996/facturar \
#      -H "Content-Type: application/json" \
#      -d '{"factura": "000100001500"}'
```

## Riesgos identificados

- El flow actualizado en disco necesita ser re-importado manualmente en Node-RED (192.168.0.23:1880)
- Sin httpStatic, el frontend modular no funciona desde Node-RED — solo funciona el HTML inline fallback
- La carpeta `portable/` no está en producción todavía; está en el repo esperando despliegue

## Links
- [[project_sumas_saldos_arquitectura]]
- [[project_conciliacion_bancaria]]
