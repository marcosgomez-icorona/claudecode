# Sumas y Saldos — Dashboard Gerencial | Ingenio La Corona

Dashboard live de Sumas y Saldos con datos acumulados desde 2025-01-01, servido desde Node-RED + Google Sheets, con fallback automático y próximamente snapshots históricos para comparación.

## Estado actual (2026-06-23)

### ✅ Funcionando (v5.1.0)
- **Dashboard webapp** en `webapp/index.html` — versión canónica modular (6 módulos JS)
- **Node-RED backend** — `flujos/node-red/back-end-sumas-saldos.json` (364 KB, desplegado)
- **Google Sheets sync** — `flujos/node-red/sync_gsheet_sumas-saldos.json` (schedule 06:00 diario)
- **Fallback API** — primary (ingcorona.ddns.net:4040) → fallback (192.168.0.23:1880) → mock data
- **11 endpoints** REST: sumas-saldos, movimientos, mayor, libro-diario, egresos, ingresos, proveedores-saldos, proveedores-pendientes, imputaciones, interempresas, health

### 🔨 En construcción
- **Sistema de snapshots históricos** — extracción MCP readonly → MySQL → comparación entre períodos
- **Motor de alertas por variación** — reglas configurables de severidad
- **Dashboard de comparación** — nueva pestaña de variaciones y tendencias

## Arquitectura

```
Apache (puerto 7070)              Node-RED (puerto 1880)
  ┌──────────────────┐              ┌──────────────────────┐
  │ webapp/index.html │  ──fetch──▶ │ GET /api/sumas-saldos │
  │ + 6 módulos JS   │  ◀──json─── │ + 10 endpoints más   │
  └──────────────────┘              └────────┬─────────────┘
                                             │
                                  ┌──────────┴──────────┐
                                  │ MSSQL (readonly)    │
                                  │ Google Sheets (rw)  │
                                  │ MySQL (aux, local)  │
                                  └─────────────────────┘
```

## Estructura de archivos

```
gerencia_sumas_y_saldos/
├── index.html              # Redirect a webapp/index.html
├── README.md               # Este archivo
├── webapp/                 # 🎯 Dashboard canónico
│   ├── index.html          # HTML principal
│   └── assets/
│       ├── css/styles.css  # Design System Corona
│       └── js/
│           ├── config.js   # Endpoints, umbrales
│           ├── api.js      # fetchWithFallback, loadFromAPI
│           ├── app.js      # Estado global, init, movimientos
│           ├── ui.js       # Render de todas las secciones
│           ├── format.js   # Formateo, badges, toast, export
│           └── mock.js     # Datos demo offline
├── flujos/node-red/        # Flujos Node-RED (fuente canónica)
│   ├── back-end-sumas-saldos.json
│   └── sync_gsheet_sumas-saldos.json
├── database/               # Schema MySQL
│   └── schema_mysql.sql
├── mds/                    # Documentación técnica (14 archivos)
├── docs/superpowers/       # Specs y planes
└── info/                   # Archivos Excel de referencia
```

## Endpoints Node-RED (v5.1.0)

| Endpoint | Fuente | Descripción |
|----------|--------|-------------|
| `/api/sumas-saldos` | GSheet | Datos consolidados |
| `/api/sumas-saldos/movimientos` | MSSQL | Detalle por cuenta |
| `/api/sumas-saldos/mayor` | GSheet | Mayor analítico |
| `/api/sumas-saldos/egresos` | GSheet | Egresos de valores |
| `/api/sumas-saldos/ingresos` | GSheet | Ingresos de valores |
| `/api/sumas-saldos/libro-diario` | GSheet | Libro diario |
| `/api/sumas-saldos/proveedores-saldos` | GSheet | Saldos por proveedor |
| `/api/sumas-saldos/proveedores-pendientes` | GSheet | Pendientes |
| `/api/sumas-saldos/imputaciones` | GSheet | Imputaciones |
| `/api/sumas-saldos/interempresas` | GSheet | Interempresas |
| `/api/sumas-saldos/health` | — | Health check |

## Cómo desplegar

1. **Desarrollo**: abrí `webapp/index.html` con Live Server o Python http.server
2. **Producción**: copiá la carpeta `webapp/` al Apache en `192.168.0.23:7070`
3. **Node-RED**: los flows ya están desplegados en `192.168.0.23:1880`
4. **Google Sheets**: sync automático diario a las 06:00

## Principios

- No escribir en Calipso
- No generar SQL libre contra ERP
- Mantener compatibilidad SQL Server 2008 R2
- Trazabilidad por snapshot, UUID y fecha
- Separación test/prod obligatoria
- MySQL para tablas auxiliares, SQL Server solo lectura ERP
