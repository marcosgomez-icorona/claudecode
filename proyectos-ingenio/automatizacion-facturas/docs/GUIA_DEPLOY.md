# Guía de Deploy — Carga Automática de Facturas

> Versión: 1.0 — 2026-06-19
> Proyecto: Automatización de Facturas de Proveedores
> Operador: pdietrich / Soporte Técnico

---

## Resumen de Deploy

| # | Acción | Servidor | Archivo |
|---|--------|----------|---------|
| 1 | Migración MySQL | MySQL (192.168.0.23) | `sql/10_migracion_completa_mysql.sql` |
| 2 | Crear tabla SQL Server | SSMS → serverico\CORONA | `sql/11_crear_staging_sqlserver.sql` |
| 3 | Instalar mssql-plus en Node-RED | Windows Server (192.168.0.23) | `npm install node-red-contrib-mssql-plus` |
| 4 | Importar flow API unificado | Node-RED (192.168.0.23:1880) | `nodered/flow_api_facturas_unificado_v2.json` |
| 5 | Importar flow sync Calipso | Node-RED (192.168.0.23:1880) | `nodered/flow_sync_calipso.json` |
| 6 | Copiar web UI | Windows Server C:\automatizacion-facturas\web\ | `web/facturas_validacion.html` |
| 7 | Probar endpoints | Postman / curl | Ver sección Testing |
| 8 | Habilitar en n8n | n8n (192.168.0.20:5678) | Importar workflow producción |

---

## Paso 1: Migración MySQL

**Dónde:** phpMyAdmin o MySQL CLI en 192.168.0.23

```bash
# Opción CLI (si hay shell)
mysql -h 127.0.0.1 -u root -p db_automatizaciones < sql/10_migracion_completa_mysql.sql
```

**O en phpMyAdmin:**
1. Log in a `http://192.168.0.23/phpmyadmin` (o donde esté configurado)
2. Seleccionar base `db_automatizaciones`
3. Ir a pestaña SQL
4. Pegar y ejecutar el contenido completo de `sql/10_migracion_completa_mysql.sql`

**Verificar:**
```sql
USE db_automatizaciones;
SHOW COLUMNS FROM staging_facturas;
-- Deberían aparecer: nro_remito, requiere_constancia, constancia_nro,
-- constancia_id_calipso, constancia_total, constancia_fecha, constancia_detalle,
-- es_dolar, cotizacion_origen, cotizacion_aviso
-- Las columnas fecha_emision, fecha_vencimiento, fecha_vto_cae deben ser tipo DATE
```

---

## Paso 2: Crear tabla en SQL Server

**Dónde:** SSMS conectado a `serverico\CORONA`

1. Abrir `sql/11_crear_staging_sqlserver.sql` en SSMS
2. Ejecutar contra base CORONA
3. Verificar en mensajes: "OK: Tabla UD_EZI_STAGING_FACTURAS creada."

---

## Paso 3: Instalar mssql-plus en Node-RED

**Dónde:** Servidor Windows 192.168.0.23, línea de comandos

```cmd
cd %USERPROFILE%\.node-red
npm install node-red-contrib-mssql-plus
```

**Post-instalación:** Reiniciar Node-RED:
```cmd
net stop Node-RED
net start Node-RED
```
O desde el menú de inicio si corre como servicio.

---

## Paso 4: Importar flow API Unificado

**Dónde:** Node-RED UI en `http://192.168.0.23:1880`

1. Menú ≡ → **Import** → **Clipboard**
2. Copiar contenido de `nodered/flow_api_facturas_unificado_v2.json`
3. **IMPORTANTE:** Verificar que los config nodes de MySQL y MSSQL existan
4. Si no existen, crearlos:
   - **MySQL:** Añadir nuevo config node → MySQL-database → host: 127.0.0.1, db: db_automatizaciones, user: usr_automatizacion
   - **MSSQL:** Añadir nuevo config node → MSSQL → apuntar a serverico\CORONA
5. Ajustar los IDs de los config nodes en los nodos MySQL y MSSQL si es necesario
6. Click **Deploy**

**Endpoints que debe servir:**

| Método | Ruta | DB | Función |
|--------|------|------|---------|
| GET | `/api/facturas/pendientes` | MySQL | Facturas pendientes de revisión |
| GET | `/api/facturas/:id` | MySQL | Detalle de factura |
| PUT | `/api/facturas/:id` | MySQL | Actualizar campos editables |
| POST | `/api/facturas/:id/aprobar` | MySQL | Aprobar + log sync |
| POST | `/api/facturas/:id/rechazar` | MySQL | Rechazar factura |
| GET | `/api/facturas/:id/items` | MySQL | Items de factura |
| GET | `/api/oc` | SQL Server | OCs por CUIT |
| GET | `/api/oc/:oc/items` | SQL Server | Items de OC |
| GET | `/api/constancias` | SQL Server | Constancias de servicio por OC |
| GET | `/api/proveedores` | SQL Server | Búsqueda de proveedores |
| GET | `/api/facturas/resumen` | MySQL | Resumen de estado |
| GET | `/api/facturas/check` | MySQL | Check duplicado |
| GET | `/api/oc/proveedor/:cuit` | SQL Server | OCs por CUIT de proveedor |
| OPTIONS | `/*` | - | CORS preflight |

---

## Paso 5: Importar flow Sync Calipso

**Dónde:** Node-RED UI en `http://192.168.0.23:1880`

1. Menú ≡ → **Import** → **Clipboard**
2. Copiar contenido de `nodered/flow_sync_calipso.json`
3. Importar como nueva tab "Sync Calipso"
4. Ajustar config nodes MySQL y MSSQL
5. Click **Deploy**

Este flow:
- Corre cada 5 minutos (auto)
- Busca registros en `log_sync_calipso` con estado `PENDIENTE`
- Inserta cada factura aprobada en `UD_EZI_STAGING_FACTURAS` (SQL Server)
- Marca el log como `SINCRONIZADO` o `ERROR`

---

## Paso 6: Copiar Web UI

**Dónde:** Servidor Windows 192.168.0.23

```cmd
copy C:\ruta\origen\facturas_validacion.html C:\automatizacion-facturas\web\
```

O desplegar Node-RED para servir el HTML desde `/facturas`:
- Verificar que exista endpoint `GET /facturas` en el flow
- Si se sirve desde Apache, copiar a `C:\xampp\htdocs\facturas\`

---

## Paso 7: Testing

### Probar health del endpoint

```bash
# Verificar que Node-RED responde
curl -s http://192.168.0.23:1880/

# Verificar facturas pendientes
curl -s http://192.168.0.23:1880/api/facturas/pendientes

# Verificar OC de un proveedor
curl -s "http://192.168.0.23:1880/api/oc?cuit=30715543172"

# Verificar constancias de una OC
curl -s "http://192.168.0.23:1880/api/constancias?oc=38722"
```

### Probar UI en navegador
```
http://192.168.0.23:1880/facturas
```
O si está en Apache:
```
http://192.168.0.23:7070/facturas/
```

---

## Paso 8: Habilitar n8n

**Dónde:** n8n en `http://192.168.0.20:5678`

1. Importar `Recepcion y Analisis de Factura_en_produccion.json`
2. Verificar que el POST endpoint apunta a:
   `http://192.168.0.23:1880/facturas/api/recepcion/factura`
3. Activar workflow

---

## Troubleshooting

### "Error SQL Server" en OC o Constancias
- Verificar `node-red-contrib-mssql-plus` instalado
- Verificar config node MSSQL en Node-RED
- Verificar conectividad con `serverico\CORONA`
- Test: `telnet serverico 1433` desde 192.168.0.23

### "Error MySQL" en facturas
- Verificar que las tablas existen en `db_automatizaciones`
- Ejecutar script de migración (Paso 1)
- Verificar credenciales MySQL

### "No hay facturas pendientes" pero n8n ya procesó
- Verificar en MySQL: `SELECT * FROM staging_facturas ORDER BY fecha_carga DESC`
- Si hay datos pero con `fecha_emision` NULL, ejecutar el script 09 de ampliación de fechas
- Verificar que Node-RED tiene conectividad con MySQL

### Web UI no carga
- F12 → Console: ver errores de fetch
- Verificar que el endpoint `GET /api/facturas/pendientes` responde
- Verificar CORS en OPTIONS /*

---

## Referencias

| Archivo | Ruta local |
|---------|------------|
| Migración MySQL | `proyectos-ingenio/automatizacion-facturas/sql/10_migracion_completa_mysql.sql` |
| Crear SQL Server | `proyectos-ingenio/automatizacion-facturas/sql/11_crear_staging_sqlserver.sql` |
| Flow API Unificado | `proyectos-ingenio/automatizacion-facturas/nodered/flow_api_facturas_unificado_v2.json` |
| Flow Sync Calipso | `proyectos-ingenio/automatizacion-facturas/nodered/flow_sync_calipso.json` |
| Web UI | `proyectos-ingenio/automatizacion-facturas/web/facturas_validacion.html` |
| n8n Workflow | `proyectos-ingenio/automatizacion-facturas/n8n/Recepcion y Analisis de Factura_en_produccion.json` |
| Spec de diseño | `docs/superpowers/specs/2026-06-17-recepcion-factura-n8n-nodered-mysql-staging-design.md` |
| Build script (flow) | `nodered/build_unified_flow.py` |
| Build script (sync) | `nodered/build_sync_flow.py` |
