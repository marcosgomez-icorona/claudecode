# Dashboard Portable — Conciliación Bancaria

Dashboard portable para la Conciliación Bancaria de **Bioenergía La Corona S.A.**

## Arquitectura

Aplica el patrón **Dashboard Portable Corona** (ver skill `dashboard-portable-corona`):

```
Frontend (index.html + JS modular)
    ↓ fetch (HTTP)
Node-RED Backend (Cloud o LAN)
    ↓
Google Sheets (datos de conciliación)
    ↑
Carga manual o automática desde extractos bancarios + Calipso
```

## Cómo usar

### Opción 1: Abrir directo (sin Node-RED)
Solo para ver la estructura HTML/CSS:
- Windows: doble clic en `index.html` o ejecutar `serve.bat`
- Linux/WSL: `./serve.sh` o `python3 -m http.server 8080`

### Opción 2: Con Node-RED (producción)
Copiar la carpeta `portable/` al servidor Windows (192.168.0.23) y servir desde Node-RED.

## Endpoints Node-RED requeridos

| Endpoint | Descripción |
|----------|-------------|
| `GET /api/conciliacion/resumen` | Resumen ejecutivo multi-banco |
| `GET /api/conciliacion/pendientes` | Pendientes con criticidad |
| `GET /api/conciliacion/detalle` | Detalle de movimientos |
| `POST /api/conciliacion/sync-sheets` | Forzar sync a Google Sheets |

## Estructura de archivos

```
portable/
├── index.html           # Dashboard principal (autocontenido)
├── config.js            # Backends Node-RED (Cloud → LAN failover)
├── css/
│   └── corona-theme.css # Design System Corona
├── js/
│   ├── utils.js         # Formateo de montos, fechas
│   ├── charts.js        # Chart.js (doughnut, bar, line)
│   └── app.js           # Lógica principal, fetch + render
├── assets/              # Logo, favicon (pendiente)
├── serve.sh             # Servidor HTTP local (Linux/WSL)
└── serve.bat            # Servidor HTTP local (Windows)
```

## Dependencias

- **Node-RED** (con flujo `flow_dashboard_conciliacion.json` importado)
- **Google Sheets** con datos de conciliación (hojas: Resumen, Pendientes, Detalle)
- **Chart.js** vía CDN (no requiere instalación local)
- **Bootstrap 5** vía CDN
