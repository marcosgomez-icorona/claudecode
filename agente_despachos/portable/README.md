# Despachos Pendientes de Facturación — Portable

Dashboard para visualizar y gestionar remitos de azúcar pendientes de facturación en Ingenio La Corona.

## Arquitectura

```
┌──────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  portable/       │────▶│  Node-RED API    │────▶│  SQL Server     │
│  (HTML/CSS/JS)   │◀────│  (1880)          │◀────│  (Calipso)      │
└──────────────────┘     └──────────────────┘     └─────────────────┘
       │                          │
       │ (mock si sin conexión)   │
       ▼                          ▼
┌──────────────┐         ┌──────────────────┐
│  mockData.js │         │  Google Sheets   │
│  (offline)   │         │  (sync futuro)   │
└──────────────┘         └──────────────────┘
```

## Modos de uso

### Desarrollo local (sin Node-RED)
```bash
bash serve.sh        # Linux / WSL
serve.bat            # Windows
```
Abrir http://localhost:8080 — usa mock data automáticamente.

### Producción — Node-RED (red local)
```bash
# 1. Copiar portable/ al servidor donde corre Node-RED
# 2. Configurar httpStatic en settings.js de Node-RED:
#    httpStatic: '/ruta/a/agente_despachos/portable'
# 3. Abrir http://192.168.0.23:1880/
```
El `config.js` con `url: ''` funciona sin cambios porque el HTML y la API están en el mismo origen.

### Producción — Apache / servidor web
```bash
# 1. Copiar portable/ a /var/www/html/despachos/
# 2. Abrir http://192.168.0.23:7070/despachos/
```
El `config.js` ya incluye el backend LAN como fallback.

### Cloud / acceso externo
```bash
# 1. Subir portable/ a hosting (VPS, Netlify, etc.)
# 2. El config.js ya tiene el backend Cloud configurado
# 3. Abrir https://midominio.com/despachos/
```

## Endpoints Node-RED requeridos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/despachos/health` | Health check |
| `GET` | `/api/despachos/pendientes?days=30` | Lista remitos pendientes |
| `GET` | `/api/despachos/pendientes/:remito` | Detalle de un remito |
| `POST` | `/api/despachos/pendientes/:remito/facturar` | Vincular factura |
| `GET` | `/api/despachos/resumen?days=30` | KPIs del período |

Flow: `../flows/flow_despachos_pendientes.json`

## Funcionalidades

- **KPIs**: total remitos, clientes, bolsas, toneladas, importe
- **Tabla** con filtro por días (7/15/30/60/90) y búsqueda textual
- **Modal** con detalle completo del remito, items, cliente, transporte
- **Clasificación** del Agente de Despachos (APTO/BLOQUEADO/PENDIENTE/APROBACION)
- **Vinculación** de factura (POST con confirmación)

## Estructura

```
portable/
├── index.html          ← Dashboard principal
├── config.js           ← Backends Node-RED (multi-backend failover)
├── css/
│   └── styles.css      ← Design System Corona
├── js/
│   ├── app.js          ← Orquestador (KPIs, filtros, estado)
│   ├── dataService.js  ← Capa de datos con failover multi-backend
│   ├── mockData.js     ← Datos de ejemplo offline
│   └── renderTables.js ← Tablas y modal de detalle
├── assets/
├── serve.sh            ← Servidor HTTP local (Linux/WSL)
├── serve.bat           ← Servidor HTTP local (Windows)
└── README.md           ← Este archivo
```
