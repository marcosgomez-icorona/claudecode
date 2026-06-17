# Dashboard de Conciliación Bancaria — Standalone

Dashboard portable para visualizar la conciliación bancaria de Bioenergía La Corona.

## Cómo usar

1. Abrí `index.html` en cualquier browser (Chrome, Edge, Firefox)
2. El dashboard intenta conectar automáticamente al backend Node-RED:
   - **Cloud** (primario): `http://ingcorona.ddns.net:4040`
   - **LAN** (secundario): `http://192.168.0.23:1880`
3. El badge en el topbar indica qué backend está activo:
   - 🟢 Cloud — conectado al servidor cloud
   - 🟡 LAN — conectado al servidor local
   - 🔴 Offline — sin conexión

## Configuración

Para cambiar URLs o timeouts, editá `config.js`.

## Requisitos

- Conexión a internet (para CDNs de Bootstrap/Chart.js) **o** que ya estén en cache del browser
- Al menos uno de los backends Node-RED operativo
- Los CORS headers ya están configurados en los endpoints Node-RED

## APIs consumidas

| Endpoint | Descripción |
|----------|-------------|
| `GET /api/conciliacion/resumen` | Resumen por banco |
| `GET /api/conciliacion/pendientes` | Pendientes con criticidad |
| `GET /api/conciliacion/detalle` | Detalle del cruce |

## Estructura

```
dashboard/
├── index.html    # Dashboard (abrirlo en browser)
├── config.js     # URLs de backends
└── README.md     # Este archivo
```

## Notas

- El endpoint original `GET /conciliacion` en Node-RED se conserva como respaldo
- Los datos se leen siempre de Google Sheets a través de las APIs Node-RED
