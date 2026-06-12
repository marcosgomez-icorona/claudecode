# Despachos Pendientes de Facturación

Tablero para visualizar y gestionar remitos de azúcar pendientes de facturación en Ingenio La Corona.

## Arquitectura

```
┌──────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  Frontend    │────▶│   Node-RED API   │────▶│  SQL Server     │
│  (HTML/JS)   │◀────│  (Middleware)    │◀────│  (Calipso)      │
└──────────────┘     └──────────────────┘     └─────────────────┘
       │                                                │
       │ (mock data para desarrollo)                    │
       ▼                                                │
┌──────────────┐                                        │
│  mockData.js │     ┌──────────────────────────┐       │
└──────────────┘     │  pr_ezi_remitos           │       │
                     │  pr_ezi_remitos_items     │       │
                     └──────────────────────────┘       │
                                                        │
                          ┌─────────────────────────┐   │
                          │  Agente de Despachos     │   │
                          │  (Apps Script - Sheets) │   │
                          │  Clasificación IA        │   │
                          └─────────────────────────┘   │
                                   │                     │
                                   ▼                     │
                          ┌─────────────────────────┐   │
                          │  Google Sheets           │   │
                          │  (Pedidos, Stock, etc.)  │   │
                          └─────────────────────────┘   │
```

## Estructura

```
agente_despachos/
├── index.html                          ← Frontend principal
├── assets/
│   ├── css/styles.css                  ← Estilos
│   └── js/
│       ├── app.js                      ← Orquestador
│       ├── config.js                   ← Configuración
│       ├── dataService.js              ← Capa de datos (API + mock)
│       ├── mockData.js                 ← Datos de ejemplo
│       └── renderTables.js             ← Renderizado de tablas y modales
├── flows/
│   └── flow_despachos_pendientes.json  ← Flow Node-RED
├── docs/
│   └── node-red-despachos.md           ← Documentación Node-RED
└── README_despachos_pendientes.md      ← Este archivo
```

## Funcionalidades

### Visualización
- KPIs: total remitos, clientes, bolsas, toneladas, importe total
- Tabla de remitos pendientes con columnas: remito, fecha, cliente, producto, cantidad, precio, total, transporte
- Productos agrupados por tipo

### Filtros
- Rango de días (7, 15, 30, 60, 90)
- Búsqueda textual por remito, cliente, producto, CUIT o transportista

### Detalle
- Modal con datos completos del remito
- Items del remito (producto, cantidad, precio, total)
- Datos de cliente, transporte, observaciones

### Integración Agente de Despachos
- Columna "Agente" muestra clasificación (APTO/BLOQUEADO/PENDIENTE/APROBACION)
- Visible en el detalle del remito
- Integración futura desde Google Apps Script vía API

### Vinculación con facturación
- Botón "Vincular con factura" en cada remito
- Modal de detalle también permite vincular
- Operación requiere confirmación del usuario

## Modo desarrollo

Sin Node-RED, el frontend usa `mockData.js` automáticamente. Solo abrir `index.html` en el navegador:

```bash
# Servir con cualquier servidor HTTP
python3 -m http.server 8080
```

## Modo producción

1. Importar `flows/flow_despachos_pendientes.json` en Node-RED
2. Configurar conexión SQL Server
3. Configurar `httpStatic` para servir el frontend
4. Abrir `http://node-red-host:1880/despachos-pendientes`

## Pendientes / Próximas versiones

- [ ] Autenticación en endpoints POST
- [ ] Integración real con Agente de Despachos (Apps Script)
- [ ] Exportar a Excel
- [ ] Dashboard de facturación completada
- [ ] Reporte PDF
