# Dashboard de Conciliación Bancaria — Standalone

Dashboard portable para visualizar la conciliación bancaria de Bioenergía La Corona.

## ⚠ Importante — No abrir directo con `file://`

Los browsers modernos (Chrome, Edge) bloquean `fetch()` desde archivos abiertos con `file://`.
El dashboard **debe servirse desde un servidor HTTP local**.

## Cómo usar

### Opción 1 — Script automático (recomendado)

**Windows:** hacé doble clic en `serve.bat`
**WSL/Linux:** ejecutá `bash serve.sh`

Esto abre el dashboard en `http://localhost:8080`.

### Opción 2 — Manual

```bash
cd ruta/dashboard/
python3 -m http.server 8080
# o: python -m http.server 8080
# o: npx serve .
```

Después abrí `http://localhost:8080` en el browser.

### Opción 3 — Servidor Node-RED existente

Si Node-RED está corriendo, también podés usar el endpoint original:
`http://192.168.0.23:1880/conciliacion` (se conserva como respaldo).

## Una vez abierto

El dashboard intenta conectar automáticamente al backend Node-RED:
- **Cloud** (primario): `http://ingcorona.ddns.net:4040`
- **LAN** (secundario): `http://192.168.0.23:1880`

El badge en el topbar indica qué backend está activo:
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
