# MCP MS SQL Server CALIPSO - EXTENDIDO PARA CODEX

**Versión**: 0.2.0  
**Estado**: ✅ Operativo - Herramientas Extendidas Integradas  
**Fecha**: Junio 5, 2026

## 📋 Resumen Ejecutivo

Se ha extendido el MCP Node.js original con nuevas herramientas específicas para:
- Consulta de facturas de compra y órdenes de compra
- Análisis de datos contables e informes gerenciales
- Consultas especializadas para azúcar y alcohol (industria de ingenio)
- Integración con CODEX para automatizaciones controladas

**Infraestructura actual:**
- Base de datos: CORONA (SQL Server 2008 R2)
- Tablas principales: 2,270
- Acceso: Readonly (usuario powerbi)
- Modo operación: MCP Server (stdio-based, compatible con CODEX)

---

## 🔧 Herramientas Disponibles (Nuevas + Existentes)

### **Herramientas Base (Originales)**

#### `healthcheck`
Verifica la conexión a SQL Server y devuelve información del sistema.

```
Parámetros: ninguno
Respuesta: {
  database_name: "CORONA",
  sql_server_version: "Microsoft SQL Server 2008 R2 (RTM)",
  dbo_tables: 2270
}
```

#### `list_tables`
Lista todas las tablas de los schemas permitidos con estadísticas de tamaño.

```
Parámetros:
  - schema (opcional): Filtrar por schema específico

Ejemplo respuesta:
[
  {
    schema_name: "dbo",
    table_name: "FACTURACOMPRA",
    approx_rows: 172501
  },
  ...
]
```

#### `search_columns` / `describe_table` / `sample_table`
Búsqueda flexible de columnas, descripción de tablas, muestreo de datos.

---

### **Herramientas Extendidas (NUEVAS)**

#### `get_invoices_by_supplier`
Obtiene facturas de compra por proveedor y/o rango de fechas.

```javascript
Parámetros:
  - supplier_name (texto): Nombre parcial del proveedor
  - date_from (YYYY-MM-DD): Fecha inicial
  - date_to (YYYY-MM-DD): Fecha final
  - limit (número): Máximo 100 registros

Respuesta:
{
  trace_id: "uuid",
  rows: [
    {
      ID: "uuid",
      invoice_number: 12345,
      invoice_date: "2024-05-20",
      ORIGINANTE_ID: "uuid"
    },
    ...
  ],
  row_count: n,
  message: "Facturas de compra encontradas: n"
}
```

**Caso de uso**: Localizar todas las facturas de un proveedor específico para validación.

---

#### `get_purchase_orders`
Obtiene órdenes de compra activas o históricas.

```javascript
Parámetros:
  - supplier_name (texto): Nombre del proveedor
  - date_from (YYYY-MM-DD): Fecha de inicio
  - limit (número): Máximo 100 registros

Respuesta:
{
  rows: [
    {
      ID: "uuid",
      po_number: 54321,
      po_date: "2024-05-15",
      ORIGINANTE_ID: "uuid",
      TRANSACCION_ID: "uuid"
    },
    ...
  ],
  message: "Órdenes de compra encontradas: n"
}
```

**Caso de uso**: Validar que factura recibida corresponde a OC existente.

---

#### `get_invoice_items`
Obtiene todos los items/líneas de una factura específica.

```javascript
Parámetros:
  - invoice_id (UUID): ID de la factura
  - include_quantities (bool): Incluir cantidades y precios

Respuesta:
{
  rows: [
    {
      item_id: "uuid",
      item_number: 1,
      description: "Producto X",
      quantity_received: 100,
      unit_cost: 50.00,
      CUENTACONTABLE_ID: "uuid"
    },
    ...
  ],
  message: "Items de factura: n encontrados"
}
```

**Caso de uso**: Desglosar factura para auditoría contable detallada.

---

#### `search_accounting_data`
Búsqueda de datos contables, transacciones y asientos.

```javascript
Parámetros:
  - search_term (texto): Término a buscar
  - date_from (YYYY-MM-DD): Fecha inicial (opcional)
  - accounting_type: FACTURA | ASIENTO | TRANSACCION | COSTO

Respuesta:
{
  rows: [
    {
      ID: "uuid",
      description: "...",
      transaction_date: "2024-05-20",
      transaction_type: "FACTURA",
      amount: 15000.00
    },
    ...
  ],
  message: "Registros contables encontrados: n"
}
```

**Caso de uso**: Análisis contable experto para gerencial.

---

#### `get_sugar_sales_orders`
Especializado: Órdenes de venta de azúcar.

```javascript
Parámetros:
  - date_from / date_to (YYYY-MM-DD)
  - customer_name (texto)
  - limit (número)

Respuesta:
{
  rows: [
    {
      order_number: 98765,
      order_date: "2024-05-25",
      total_quantity: 500000,  // kg
      total_amount: 125000.00
    },
    ...
  ]
}
```

**Caso de uso**: Análisis de ventas de azúcar para gestión de producción.

---

#### `get_alcohol_sales_data`
Especializado: Órdenes de venta de alcohol.

```javascript
Parámetros:
  - date_from / date_to (YYYY-MM-DD)
  - limit (número)

Respuesta: {
  rows: [
    {
      order_number: 65432,
      order_date: "2024-05-25",
      quantity: 5000,    // litros
      amount: 87500.00
    },
    ...
  ]
}
```

---

#### `get_accounting_reports`
Acceso a informes contables gerenciales.

```javascript
Parámetros:
  - report_type: CUENTAS_PAGAR | CUENTAS_COBRAR | CASHFLOW | PRESUPUESTO | COSTOS
  - date_from / date_to (YYYY-MM-DD)
  - business_unit (opcional)

Ejemplos de respuesta por tipo:

CUENTAS_PAGAR:
{
  rows: [
    {
      supplier: "PROVEEDOR ABC",
      amount: 50000,
      currency: "ARS",
      status: "ABIERTA",
      due_date: "2024-06-15"
    },
    ...
  ]
}

CUENTAS_COBRAR:
{
  rows: [
    {
      customer: "CLIENTE XYZ",
      amount: 100000,
      currency: "ARS",
      status: "VENCIDA",
      due_date: "2024-05-31"
    },
    ...
  ]
}

CASHFLOW:
{
  rows: [
    {
      description: "Ingresos por venta",
      inflow: 250000,
      outflow: 0,
      net: 250000,
      date: "2024-05-25"
    },
    ...
  ]
}
```

**Caso de uso**: Informes gerenciales contables y análisis de tesorería.

---

## 🚀 Cómo Usar el MCP con CODEX

### Inicio del Servidor

```bash
cd c:\claudecode\proyectos-ingenio\automatizacion-facturas\mcp-calipso-sqlserver

# Opción 1: Con ruta completa
"C:\Program Files\nodejs\node.exe" src/server.js

# Opción 2: Con npm
npm start
```

### Desde CODEX (Ejemplo)

```javascript
// CODEX puede invocar herramientas así:

// 1. Obtener facturas de un proveedor
const facturas = await mcpClient.call('get_invoices_by_supplier', {
  supplier_name: 'ACME Corp',
  date_from: '2024-05-01',
  date_to: '2024-05-31'
});

// 2. Validar factura contra OC
const ocs = await mcpClient.call('get_purchase_orders', {
  supplier_name: 'ACME Corp'
});

// 3. Obtener detalles de factura
const items = await mcpClient.call('get_invoice_items', {
  invoice_id: facturas.rows[0].ID
});

// 4. Generar informe de cuentas por pagar
const cxp = await mcpClient.call('get_accounting_reports', {
  report_type: 'CUENTAS_PAGAR',
  date_from: '2024-05-01'
});
```

---

## 📊 Arquitectura de Base de Datos

```
CALIPSO (CORONA)
├── Módulo Compras
│   ├── ORDENCOMPRA (Órdenes de compra)
│   ├── FACTURACOMPRA (Facturas de compra)
│   ├── ITEMFACTURACOMPRA (Items de facturas)
│   └── RECEPMERCADERIA (Recepción de mercadería)
├── Módulo Ventas
│   ├── FACTURAVENTA
│   ├── REMITO_*
│   └── V_UD_EZI_ORDEN_VTA_AZ_ITEM (Azúcar)
│   └── V_UD_EZI_ORDEN_ALCOHOL (Alcohol)
├── Módulo Contable
│   ├── ITEMCONTABLE (Transacciones contables)
│   ├── V_UD_UOCUENTASPAGAR (CXP)
│   ├── V_UD_UOCUENTASCOBRAR (CXC)
│   ├── V_UD_UOCASHFLOW (Cash flow)
│   └── V_UD_UOCONTABILIDAD (Resúmenes contables)
└── Datos Complementarios
    ├── DATOCOMPLEMENTARIO
    ├── DATOCOMPVALOR
    └── ITEMDATOCOMPV* (Variantes)
```

---

## 📝 Logs y Auditoría

Todas las consultas se registran en:
```
./logs/mcp-calipso-sqlserver.jsonl
```

Cada entrada contiene:
- `trace_id`: UUID único de la consulta
- `startedAt` / `finishedAt`: Timestamps
- `query`: SQL ejecutado
- `rows`: Cantidad de filas retornadas

---

## ⚙️ Configuración (.env)

```
MSSQL_SERVER=192.168.0.177
MSSQL_PORT=1433
MSSQL_DATABASE=CORONA
MSSQL_USER=powerbi
MSSQL_PASSWORD=Bi478
MSSQL_ENCRYPT=false
MSSQL_TRUST_SERVER_CERTIFICATE=true
MSSQL_REQUEST_TIMEOUT_MS=30000
MSSQL_MAX_ROWS=200
MSSQL_ALLOWED_SCHEMAS=dbo
MSSQL_LOG_DIR=./logs
```

---

## 🔒 Seguridad

✅ **Protecciones implementadas:**
- Acceso readonly solamente
- Filtro de comandos INSERT/UPDATE/DELETE
- Validación de identificadores SQL
- Limites de filas por consulta
- Auditoría de todas las consultas
- Schema whitelist (dbo)

⚠️ **Consideraciones:**
- Las credenciales NO deben estar en git
- Usar .env local (ya excluido en .gitignore)
- Acceso limitado a usuario powerbi (recomendado)
- Certificados SSL para producción

---

## 🔄 Próximos Pasos (Fase 2)

### 1. Componente Python para Análisis Inteligente
```
Crear: src/validation_engine.py
Funciones:
  - validate_invoice_against_po() - Validar factura vs OC
  - calculate_invoice_totals() - Cálculos de costos
  - detect_anomalies() - Detectar inconsistencias
  - generate_accounting_entries() - Generar asientos contables
```

### 2. Web Interface (facturas_validacion.html)
Integración con MCP para:
- Búsqueda de facturas
- Validación en tiempo real
- Carga masiva de facturas
- Generación de reportes

### 3. Integración n8n/Node-RED
Workflows automatizados para:
- Ingesta de correos con facturas
- Extracción OCR
- Validación automática
- Registro en CALIPSO

---

## 📞 Soporte

**Herramientas del MCP:**
- `search_columns` - Buscar columnas desconocidas
- `describe_table` - Explorar estructura de tablas
- `find_invoice_logic_candidates` - Descubrir tablas relacionadas
- `run_readonly_query` - Consultas custom (SQL genérico)

**Scripts de diagnóstico:**
- `scripts/test-connection.js` - Verifica conexión a BD
- `scripts/explore-structure.js` - Análisis completo de tablas
- `scripts/test-mcp-tools.js` - Pruebas de todas las herramientas

---

**Desarrollado para**: Automatización de Facturas - Ingenio de Azúcar  
**Versión MCP**: 1.13.0  
**Node.js**: v20+  
**SQL Server**: 2008 R2 compatible
