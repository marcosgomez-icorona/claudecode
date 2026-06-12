# 📊 ANÁLISIS - facturas_validacion.html

**Archivo**: `web/facturas_validacion.html`  
**Líneas**: ~1500  
**Status**: ✅ Existe - Listo para integración  
**Usuario**: Asistente de Pago a Proveedores  

---

## 🏗️ Arquitectura de UI

### Estructura HTML
```
┌─────────────────────────────────────────────────────────┐
│ HEADER (46px)                                            │
│ Validación de Facturas de Proveedores  [Pendientes: N] │
└─────────────────────────────────────────────────────────┘
┌─────────────────┬────────────────────────────────────────┐
│   SIDEBAR       │        DETAIL AREA                     │
│   280px         │        (Flex - Responsive)            │
│                 │                                       │
│ Buscar:        │  ┌──────┬──────┬──────┐               │
│ [         ]    │  │FACT  │ OC   │CONST │               │
│                 │  ├──────┼──────┼──────┤               │
│ • Proveedor 1  │  │      │      │      │               │
│   Fact ABC      │  │      │      │      │               │
│ • Proveedor 2  │  │      │      │      │               │
│ • Proveedor 3  │  └──────┴──────┴──────┘               │
│                 │                                       │
│ [↺ Actualizar] │  [Guardar] [Aprobar] [Rechazar]      │
└─────────────────┴────────────────────────────────────────┘
```

### Componentes Principales

#### 1. SIDEBAR (280px ancho)
- **Función**: Lista de facturas pendientes
- **Contenido**:
  - Campo de búsqueda por proveedor/número
  - Cards de facturas con:
    - Nombre del proveedor
    - Número de factura
    - Total en pesos
    - Fecha de emisión
    - Estado (PENDIENTE, APROBADO, RECHAZADO, EN_REVISION)
    - Nivel de confianza (ALTA, MEDIA, BAJA)
    - Badge "CONST" si requiere certificación
  - Botón de actualización
  - Última actualización

- **Styling**: 
  - Fondo blanco, borde derecho gris
  - Cards con hover y efecto select azul
  - Scrolleable

#### 2. DETAIL AREA (Flex)
Tres columnas que se activan según contexto:

**COLUMNA 1: FACTURA (Siempre visible)**
- Datos de factura editable/readonly según estado
- Proveedor (CUIT, nombre)
- Documento (Letra, número, fecha)
- Condición IVA
- Importes:
  - Neto
  - IVA
  - Otros
  - Total
- Checkbox "Requiere Certificación de Servicio"
- Tabla de ítems de la factura
- Botones de acción (Guardar, Aprobar, Rechazar)

**COLUMNA 2: OC (Orden de Compra)**
- Lista de OC del proveedor
- Para cada OC:
  - Número
  - Fecha
  - Total
  - Estado (Cerrado, Abierto, Pendiente)
  - Items asociados (expandible)
  - Botón "Vincular" para asociar a factura
- Si OC comienza con "01-": puede tener certificaciones requeridas
- Badge verde si ya hay certificación

**COLUMNA 3: CONSTANCIAS (Condicional)**
- Solo visible si:
  - Hay una OC vinculada
  - La OC requiere constancia de servicio
- Muestra:
  - Cards de certificaciones disponibles
  - Estado (Ya facturada / Disponible)
  - Total
  - Botón para seleccionar
  - Info de constancia seleccionada

---

## 🔌 Endpoints API Esperados

El código JavaScript llama a estos endpoints (línea ~700+):

```javascript
GET  ${API_BASE}/api/facturas/pendientes
     → Retorna: Array de objetos factura

GET  ${API_BASE}/api/facturas/{id}
     → Retorna: Objeto factura completo

GET  ${API_BASE}/api/facturas/{id}/items
     → Retorna: Array de items de factura

GET  ${API_BASE}/api/oc/proveedor/{cuit}
     → Retorna: Array de OCs del proveedor

GET  ${API_BASE}/api/oc/{nro}/items
     → Retorna: Array de items de OC

GET  ${API_BASE}/api/constancias/oc/{nro}
     → Retorna: Array de certificaciones

POST ${API_BASE}/api/facturas/{id}/guardar
     → Body: { campos modificados }
     → Retorna: { ok: true/false }

POST ${API_BASE}/api/facturas/{id}/aprobar
     → Body: { notas_aprobacion: string }
     → Retorna: { ok: true/false }

POST ${API_BASE}/api/facturas/{id}/rechazar
     → Body: { motivo: string }
     → Retorna: { ok: true/false }
```

---

## 📊 Estructura de Datos

### Objeto Factura (retornado por API)
```javascript
{
  id: "fact_001",                          // ID único
  proveedor_cuit: "20123456789",          // CUIT del proveedor
  proveedor_nombre: "PROVEEDOR S.A.",     // Nombre
  letra: "C",                              // Letra de factura
  numerodocumento: "0001234",              // Número
  fecha_emision: "2026-01-15",            // Fecha ISO
  condicion_iva: "IVA Exento",            // Condición
  neto: 10000.00,                         // Monto neto
  iva: 2100.00,                           // IVA
  otros: 0.00,                            // Otros descuentos
  total: 12100.00,                        // Total
  estado_proceso: "PENDIENTE",            // PENDIENTE|APROBADO|RECHAZADO|EN_REVISION
  confianza_parseo: "ALTA",               // ALTA|MEDIA|BAJA
  requiere_constancia: false,             // Si necesita certificación
  referencia: "01-2345",                  // OC vinculada
  items_json: "[{...}]",                  // Items en JSON
  constancia_nro: null,                   // Constancia seleccionada
  constancia_id_calipso: null,            // ID en CALIPSO
  constancia_total: null,                 // Monto
  constancia_fecha: null,                 // Fecha
  constancia_detalle: null                // Descripción
}
```

### Objeto Item Factura
```javascript
{
  nro_linea: 1,                           // Número de línea
  descripcion: "ITEM DESCRIPCIÓN",        // Descripción
  cantidad: 10.00,                        // Cantidad
  precio_unitario: 1000.00,               // Precio
  iva_percent: 21,                        // % IVA (0 o 21)
  subtotal: 10000.00                      // Total línea
}
```

### Objeto OC (Orden Compra)
```javascript
{
  nro: "01-2345",                         // Número OC
  fecha: "2025-12-01",                    // Fecha ISO
  total: 50000.00,                        // Total OC
  estado: "A",                            // A=Abierta, C=Cerrada, P=Pendiente
  items: [                                // Items (opcional, si se expande)
    {
      nro: 1,
      descripcion: "...",
      cantidad: 10,
      precio_unitario: 1000,
      subtotal: 10000
    }
  ],
  tiene_constancias: false                // Flag si requiere constancia
}
```

### Objeto Constancia (Certificación)
```javascript
{
  nro: "CONST-2025-001",                  // Número
  id_calipso: 123456,                     // ID en CALIPSO
  fecha: "2025-12-15",                    // Fecha certificación
  detalle: "Descripción de trabajo",      // Detalle
  total: 25000.00,                        // Monto
  facturada: false                        // Si ya fue facturada
}
```

---

## 🎨 Características de UI

### Estados Visuales
| Estado | Color | Fondo |
|---|---|---|
| PENDIENTE | Naranja | #fef9c3 |
| APROBADO | Verde | #dcfce7 |
| RECHAZADO | Rojo | #fee2e2 |
| EN_REVISION | Azul | #dbeafe |

### Confianza Parseo
| Nivel | Color | Fondo |
|---|---|---|
| ALTA | Verde | #dcfce7 |
| MEDIA | Naranja | #fef9c3 |
| BAJA | Rojo | #fee2e2 |

### Modos
- **Lectura**: Si estado es APROBADO o RECHAZADO
- **Edición**: Si estado es PENDIENTE o EN_REVISION

### Toast (Notificaciones)
```javascript
showToast("Mensaje", "ok|error|warning", 3500)
```

---

## 🔧 JavaScript Functions Clave

```javascript
loadList()                  // Carga lista de facturas pendientes
renderList(invoices)        // Renderiza sidebar
filterList()                // Filtra por búsqueda
loadDetail(id)              // Carga detalle de factura
renderColFactura(f, items)  // Renderiza columna factura
loadOCs(cuit, oc_vinculada) // Carga OCs del proveedor
renderOCs(ocs)              // Renderiza tabla OCs
loadConstancias(oc_nro)     // Carga certificaciones
vinculareOC(oc_nro)         // Vincula OC a factura
aprobar()                   // Aprueba factura
rechazar()                  // Rechaza factura
guardar()                   // Guarda cambios
```

---

## 🔗 Integración Necesaria

### PASO 1: Crear Backend Node.js con Endpoints

```javascript
// File: backend/routes/facturas.js
import express from 'express';
import { mcpClient } from '../mcp-client.js';
import { PythonValidator } from '../python-validator.js';

const router = express.Router();

// GET /api/facturas/pendientes
router.get('/pendientes', async (req, res) => {
  try {
    const facturas = await mcpClient.call('get_facturas_pendientes', {
      limit: 100
    });
    // Enriquecer con datos de validación
    res.json(facturas);
  } catch(error) {
    res.status(500).json({ error: error.message });
  }
});

// GET /api/facturas/{id}
router.get('/:id', async (req, res) => {
  try {
    const factura = await mcpClient.call('get_factura_by_id', {
      factura_id: req.params.id
    });
    res.json(factura);
  } catch(error) {
    res.status(500).json({ error: error.message });
  }
});

// POST /api/facturas/{id}/validar
router.post('/:id/validar', async (req, res) => {
  try {
    const factura = await mcpClient.call('get_factura_by_id', {
      factura_id: req.params.id
    });
    const oc = await mcpClient.call('get_purchase_orders', {
      po_number: req.body.oc_nro
    });
    const validation = await PythonValidator.validate(factura, oc);
    res.json(validation);
  } catch(error) {
    res.status(500).json({ error: error.message });
  }
});

// POST /api/facturas/{id}/aprobar
router.post('/:id/aprobar', async (req, res) => {
  try {
    // 1. Validar factura
    // 2. Generar asientos contables
    // 3. Registrar en CALIPSO
    // 4. Actualizar estado a APROBADO
    res.json({ ok: true, message: 'Factura aprobada' });
  } catch(error) {
    res.status(500).json({ error: error.message });
  }
});

export default router;
```

### PASO 2: Conectar MCP Tools

Mapeo de herramientas MCP existentes a endpoints API:

| Endpoint API | Herramienta MCP | Parámetros |
|---|---|---|
| `/api/facturas/pendientes` | `get_invoices_by_supplier` | limit=100, estado='PENDIENTE' |
| `/api/facturas/{id}` | `get_invoices_by_supplier` + DB query | invoice_id |
| `/api/facturas/{id}/items` | `get_invoice_items` | invoice_id |
| `/api/oc/proveedor/{cuit}` | `get_purchase_orders` | supplier_cuit |
| `/api/oc/{nro}/items` | `get_invoice_items` | order_id |
| `/api/constancias/oc/{nro}` | Query directo a CALIPSO | oc_nro |

### PASO 3: Integrar Python Validator

```javascript
// File: backend/python-validator.js
import { spawn } from 'child_process';

export class PythonValidator {
  static async validate(factura, oc) {
    return new Promise((resolve, reject) => {
      const python = spawn('python', [
        'c:\\claudecode\\automatizacion-facturas\\python\\validation_engine.py'
      ]);
      
      let output = '';
      let error = '';
      
      python.stdout.on('data', (data) => { output += data.toString(); });
      python.stderr.on('data', (data) => { error += data.toString(); });
      
      python.on('close', (code) => {
        if (code === 0) {
          resolve(JSON.parse(output));
        } else {
          reject(new Error(error));
        }
      });
      
      python.stdin.write(JSON.stringify({ factura, oc }));
      python.stdin.end();
    });
  }
}
```

---

## 📋 Checklist de Integración

- [ ] Crear backend Express.js
- [ ] Implementar rutas `/api/facturas/*`
- [ ] Conectar MCP Node.js client
- [ ] Integrar Python validator
- [ ] Configurar CORS (localhost)
- [ ] Crear base de datos staging para estados
- [ ] Implementar autenticación básica (usuario/contraseña)
- [ ] Setup de logging
- [ ] Testing de endpoints
- [ ] Documentación de API (OpenAPI)

---

## 🚀 Próximos Pasos

1. **Crear backend** con Express en `backend/api-server.js`
2. **Mapear endpoints** a MCP tools
3. **Implementar rutas** CRUD de facturas
4. **Testing** de flujo end-to-end
5. **Documentar** API final

---

**Analizado por**: GitHub Copilot  
**Fecha**: 6 de Junio 2026  
**Status**: Listo para backend
