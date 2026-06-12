import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import dotenv from 'dotenv';
import sql from 'mssql';
import { v4 as uuidv4 } from 'uuid';
import { McpServer } from '@modelcontextprotocol/sdk/server/mcp.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { z } from 'zod';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
dotenv.config({ path: path.join(__dirname, '..', '.env') });

const maxRows = Number.parseInt(process.env.MSSQL_MAX_ROWS || '200', 10);
const allowedSchemas = (process.env.MSSQL_ALLOWED_SCHEMAS || 'dbo')
  .split(',')
  .map((schema) => schema.trim())
  .filter(Boolean);

const config = {
  server: requiredEnv('MSSQL_SERVER'),
  port: Number.parseInt(process.env.MSSQL_PORT || '1433', 10),
  database: requiredEnv('MSSQL_DATABASE'),
  user: requiredEnv('MSSQL_USER'),
  password: requiredEnv('MSSQL_PASSWORD'),
  options: {
    encrypt: parseBool(process.env.MSSQL_ENCRYPT, false),
    trustServerCertificate: parseBool(process.env.MSSQL_TRUST_SERVER_CERTIFICATE, true)
  },
  requestTimeout: Number.parseInt(process.env.MSSQL_REQUEST_TIMEOUT_MS || '30000', 10),
  pool: {
    max: 4,
    min: 0,
    idleTimeoutMillis: 30000
  }
};

let poolPromise;

const server = new McpServer({
  name: 'calipso-sqlserver-readonly',
  version: '0.1.0'
});

server.tool(
  'healthcheck',
  'Verifica conexion readonly contra SQL Server y devuelve base, version y usuario actual.',
  {},
  async () => {
    const result = await executeReadonly(
      `SELECT
         DB_NAME() AS database_name,
         SYSTEM_USER AS current_system_user,
         SUSER_SNAME() AS login_name,
         @@VERSION AS sql_server_version`
    );
    return asText(result);
  }
);

server.tool(
  'list_tables',
  'Lista tablas de los schemas permitidos, con cantidad aproximada de filas.',
  {
    schema: z.string().optional().describe('Schema SQL Server. Por defecto usa todos los permitidos.')
  },
  async ({ schema }) => {
    assertAllowedSchema(schema);
    const schemaFilter = schema ? 'AND s.name = @schema' : '';
    const result = await executeReadonly(
      `SELECT TOP (${maxRows})
         s.name AS schema_name,
         t.name AS table_name,
         SUM(CASE WHEN p.index_id IN (0, 1) THEN p.rows ELSE 0 END) AS approx_rows
       FROM sys.tables t
       INNER JOIN sys.schemas s ON s.schema_id = t.schema_id
       LEFT JOIN sys.partitions p ON p.object_id = t.object_id
       WHERE s.name IN (${parameterList('schema', allowedSchemas.length)})
       ${schemaFilter}
       GROUP BY s.name, t.name
       ORDER BY s.name, t.name`,
      (request) => {
        bindList(request, 'schema', allowedSchemas);
        if (schema) request.input('schema', sql.NVarChar, schema);
      }
    );
    return asText(result);
  }
);

server.tool(
  'search_columns',
  'Busca columnas por nombre para ubicar entidades Calipso sin inventar estructura.',
  {
    term: z.string().min(2).describe('Texto a buscar en nombres de columnas o tablas.'),
    schema: z.string().optional()
  },
  async ({ term, schema }) => {
    assertAllowedSchema(schema);
    const schemaFilter = schema ? 'AND TABLE_SCHEMA = @schema' : '';
    const result = await executeReadonly(
      `SELECT TOP (${maxRows})
         TABLE_SCHEMA AS schema_name,
         TABLE_NAME AS table_name,
         COLUMN_NAME AS column_name,
         DATA_TYPE AS data_type,
         CHARACTER_MAXIMUM_LENGTH AS max_length,
         IS_NULLABLE AS is_nullable
       FROM INFORMATION_SCHEMA.COLUMNS
       WHERE TABLE_SCHEMA IN (${parameterList('schema', allowedSchemas.length)})
         ${schemaFilter}
         AND (COLUMN_NAME LIKE @term OR TABLE_NAME LIKE @term)
       ORDER BY TABLE_SCHEMA, TABLE_NAME, ORDINAL_POSITION`,
      (request) => {
        bindList(request, 'schema', allowedSchemas);
        request.input('term', sql.NVarChar, `%${term}%`);
        if (schema) request.input('schema', sql.NVarChar, schema);
      }
    );
    return asText(result);
  }
);

server.tool(
  'describe_table',
  'Describe columnas, tipos e indices basicos de una tabla.',
  {
    schema: z.string().default('dbo'),
    table: z.string().min(1)
  },
  async ({ schema, table }) => {
    assertAllowedSchema(schema);
    assertIdentifier(table, 'table');
    const result = await executeReadonly(
      `SELECT
         c.ORDINAL_POSITION AS ordinal,
         c.COLUMN_NAME AS column_name,
         c.DATA_TYPE AS data_type,
         c.CHARACTER_MAXIMUM_LENGTH AS max_length,
         c.NUMERIC_PRECISION AS numeric_precision,
         c.NUMERIC_SCALE AS numeric_scale,
         c.IS_NULLABLE AS is_nullable
       FROM INFORMATION_SCHEMA.COLUMNS c
       WHERE c.TABLE_SCHEMA = @schema
         AND c.TABLE_NAME = @table
       ORDER BY c.ORDINAL_POSITION;

       SELECT
         i.name AS index_name,
         i.is_unique,
         i.type_desc,
         ic.key_ordinal,
         col.name AS column_name
       FROM sys.indexes i
       INNER JOIN sys.index_columns ic ON ic.object_id = i.object_id AND ic.index_id = i.index_id
       INNER JOIN sys.columns col ON col.object_id = ic.object_id AND col.column_id = ic.column_id
       INNER JOIN sys.tables t ON t.object_id = i.object_id
       INNER JOIN sys.schemas s ON s.schema_id = t.schema_id
       WHERE s.name = @schema
         AND t.name = @table
       ORDER BY i.name, ic.key_ordinal`,
      (request) => {
        request.input('schema', sql.NVarChar, schema);
        request.input('table', sql.NVarChar, table);
      }
    );
    return asText(result);
  }
);

server.tool(
  'sample_table',
  'Devuelve una muestra limitada de una tabla. Solo SELECT TOP, sin filtros libres.',
  {
    schema: z.string().default('dbo'),
    table: z.string().min(1),
    limit: z.number().int().positive().max(maxRows).optional()
  },
  async ({ schema, table, limit }) => {
    assertAllowedSchema(schema);
    assertIdentifier(table, 'table');
    const rowLimit = Math.min(limit || 50, maxRows);
    const result = await executeReadonly(
      `SELECT TOP (${rowLimit}) *
       FROM ${quoteName(schema)}.${quoteName(table)}`
    );
    return asText(result);
  }
);

server.tool(
  'run_readonly_query',
  'Ejecuta una consulta SELECT controlada para analisis. Bloquea escrituras y limita filas.',
  {
    query: z.string().min(8).describe('Consulta SELECT compatible con SQL Server 2008 R2.'),
    paramsJson: z.string().optional().describe('Objeto JSON opcional con parametros escalares.')
  },
  async ({ query, paramsJson }) => {
    validateSelectOnly(query);
    const params = paramsJson ? JSON.parse(paramsJson) : {};
    const limitedQuery = applyTopLimit(query);
    const result = await executeReadonly(limitedQuery, (request) => {
      for (const [key, value] of Object.entries(params)) {
        assertIdentifier(key, 'param');
        request.input(key, value);
      }
    });
    return asText(result);
  }
);

server.tool(
  'find_invoice_logic_candidates',
  'Busca nombres de tablas, columnas, vistas y procedimientos candidatos para facturas/OC/recepciones/asientos.',
  {
    term: z.string().optional().describe('Termino adicional. Ej: factura, proveedor, orden, asiento.')
  },
  async ({ term }) => {
    const terms = ['fact', 'fac', 'prove', 'oc', 'orden', 'recep', 'remito', 'asiento', 'impu', 'iva'];
    if (term) terms.push(term);

    const result = await executeReadonly(
      `SELECT TOP (${maxRows})
         'COLUMN' AS object_type,
         TABLE_SCHEMA AS schema_name,
         TABLE_NAME AS object_name,
         COLUMN_NAME AS detail_name
       FROM INFORMATION_SCHEMA.COLUMNS
       WHERE TABLE_SCHEMA IN (${parameterList('schema', allowedSchemas.length)})
         AND (${terms.map((_, index) => `COLUMN_NAME LIKE @term${index} OR TABLE_NAME LIKE @term${index}`).join(' OR ')})

       UNION ALL

       SELECT TOP (${maxRows})
         ROUTINE_TYPE AS object_type,
         ROUTINE_SCHEMA AS schema_name,
         ROUTINE_NAME AS object_name,
         NULL AS detail_name
       FROM INFORMATION_SCHEMA.ROUTINES
       WHERE ROUTINE_SCHEMA IN (${parameterList('schema', allowedSchemas.length)})
         AND (${terms.map((_, index) => `ROUTINE_NAME LIKE @term${index}`).join(' OR ')})

       ORDER BY object_type, schema_name, object_name, detail_name`,
      (request) => {
        bindList(request, 'schema', allowedSchemas);
        terms.forEach((value, index) => request.input(`term${index}`, sql.NVarChar, `%${value}%`));
      }
    );
    return asText(result);
  }
);

// ============================================================================
// HERRAMIENTAS EXTENDIDAS PARA FACTURAS, OC Y CONTABILIDAD
// ============================================================================

server.tool(
  'get_invoices_by_supplier',
  'Obtiene facturas de compra por proveedor o rango de fechas. Herramienta para análisis de facturas.',
  {
    supplier_name: z.string().optional().describe('Nombre parcial del proveedor a buscar'),
    date_from: z.string().optional().describe('Fecha inicial (YYYY-MM-DD)'),
    date_to: z.string().optional().describe('Fecha final (YYYY-MM-DD)'),
    limit: z.number().int().positive().optional().describe('Límite de registros (max 100)')
  },
  async ({ supplier_name, date_from, date_to, limit = 50 }) => {
    const limitRows = Math.min(limit, 100);
    let query = `
      SELECT TOP (${limitRows})
        fc.ID,
        fc.NUMERODOCUMENTO AS invoice_number,
        fc.FECHAACTUAL AS invoice_date,
        fc.TRANSACCION_ID,
        fc.CENTROCOSTOS_ID,
        fc.ORIGINANTE_ID
      FROM FACTURACOMPRA fc
      WHERE 1=1
    `;

    const params = {};

    if (supplier_name) {
      query += ` AND fc.ORIGINANTE_ID IN (
        SELECT ID FROM [entidad] WHERE NOMBRE LIKE @supplier_name
      )`;
      params.supplier_name = `%${supplier_name}%`;
    }

    if (date_from) {
      query += ` AND CAST(fc.FECHAACTUAL AS DATE) >= @date_from`;
      params.date_from = date_from;
    }

    if (date_to) {
      query += ` AND CAST(fc.FECHAACTUAL AS DATE) <= @date_to`;
      params.date_to = date_to;
    }

    query += ` ORDER BY fc.FECHAACTUAL DESC`;

    const result = await executeReadonly(query, (request) => {
      Object.entries(params).forEach(([key, value]) => {
        request.input(key, sql.NVarChar, value);
      });
    });

    return asText({
      ...result,
      message: `Facturas de compra encontradas: ${result.row_count}`
    });
  }
);

server.tool(
  'get_purchase_orders',
  'Obtiene órdenes de compra activas o por rango de fechas.',
  {
    supplier_name: z.string().optional().describe('Nombre del proveedor'),
    status: z.string().optional().describe('Estado de la OC (ej: ABIERTA, CERRADA)'),
    date_from: z.string().optional().describe('Fecha inicial (YYYY-MM-DD)'),
    limit: z.number().int().positive().optional().describe('Límite de registros')
  },
  async ({ supplier_name, status, date_from, limit = 50 }) => {
    const limitRows = Math.min(limit, 100);
    
    let query = `
      SELECT TOP (${limitRows})
        oc.ID,
        oc.NUMERODOCUMENTO AS po_number,
        oc.FECHAACTUAL AS po_date,
        oc.ORIGINANTE_ID,
        oc.TRANSACCION_ID,
        oc.CENTROCOSTOS_ID
      FROM ORDENCOMPRA oc
      WHERE 1=1
    `;

    const params = {};

    if (supplier_name) {
      query += ` AND oc.ORIGINANTE_ID IN (
        SELECT ID FROM [entidad] WHERE NOMBRE LIKE @supplier_name
      )`;
      params.supplier_name = `%${supplier_name}%`;
    }

    if (date_from) {
      query += ` AND CAST(oc.FECHAACTUAL AS DATE) >= @date_from`;
      params.date_from = date_from;
    }

    query += ` ORDER BY oc.FECHAACTUAL DESC`;

    const result = await executeReadonly(query, (request) => {
      Object.entries(params).forEach(([key, value]) => {
        request.input(key, sql.NVarChar, value);
      });
    });

    return asText({
      ...result,
      message: `Órdenes de compra encontradas: ${result.row_count}`
    });
  }
);

server.tool(
  'get_invoice_items',
  'Obtiene los items/líneas de una factura de compra específica.',
  {
    invoice_id: z.string().describe('ID de la factura (UUID)'),
    include_quantities: z.boolean().optional().describe('Incluir cantidades y precios')
  },
  async ({ invoice_id, include_quantities = true }) => {
    assertIdentifier(invoice_id, 'invoice_id');

    let query = `
      SELECT
        ifc.ID AS item_id,
        ifc.NUMEROORDENITEM AS item_number,
        ifc.DESCRIPCION AS description,
        ifc.CANTIDADRECIBIDA AS quantity_received,
        ifc.CANTIDADADICIONAL AS quantity_additional,
        ifc.COSTO AS unit_cost,
        ifc.CUENTACONTABLE_ID
      FROM ITEMFACTURACOMPRA ifc
      WHERE ifc.ID = @invoice_id
      ORDER BY ifc.NUMEROORDENITEM
    `;

    const result = await executeReadonly(query, (request) => {
      request.input('invoice_id', sql.NVarChar, invoice_id);
    });

    return asText({
      ...result,
      message: `Items de factura: ${result.row_count} encontrados`
    });
  }
);

server.tool(
  'search_accounting_data',
  'Busca datos contables, asientos y transacciones relacionadas a facturas.',
  {
    search_term: z.string().min(2).describe('Término de búsqueda (factura, OC, asiento, etc)'),
    date_from: z.string().optional().describe('Fecha inicial'),
    accounting_type: z.enum(['FACTURA', 'ASIENTO', 'TRANSACCION', 'COSTO']).optional()
  },
  async ({ search_term, date_from, accounting_type }) => {
    const maxRows = 200;
    
    let query = `
      SELECT TOP (${maxRows})
        ic.ID,
        ic.DESCRIPCION AS description,
        ic.VALOR2_MOMENTO AS transaction_date,
        ic.VALOR2_NOMBRE AS transaction_type,
        ic.VALOR2_IMPORTE AS amount,
        ic.DETALLE AS details,
        ic.CENTROCOSTOS_ID
      FROM ITEMCONTABLE ic
      WHERE UPPER(ic.DESCRIPCION) LIKE @search_term
        OR UPPER(ic.DETALLE) LIKE @search_term
    `;

    const params = {
      search_term: `%${search_term.toUpperCase()}%`
    };

    if (date_from) {
      query += ` AND CAST(ic.VALOR2_MOMENTO AS DATE) >= @date_from`;
      params.date_from = date_from;
    }

    query += ` ORDER BY ic.VALOR2_MOMENTO DESC`;

    const result = await executeReadonly(query, (request) => {
      Object.entries(params).forEach(([key, value]) => {
        request.input(key, sql.NVarChar, value);
      });
    });

    return asText({
      ...result,
      search_term,
      message: `Registros contables encontrados: ${result.row_count}`
    });
  }
);

server.tool(
  'get_sugar_sales_orders',
  'Obtiene órdenes de venta de azúcar (especialización para ingenio).',
  {
    date_from: z.string().optional().describe('Fecha inicial (YYYY-MM-DD)'),
    date_to: z.string().optional().describe('Fecha final (YYYY-MM-DD)'),
    customer_name: z.string().optional().describe('Nombre del cliente'),
    limit: z.number().optional().describe('Límite de registros')
  },
  async ({ date_from, date_to, customer_name, limit = 50 }) => {
    const limitRows = Math.min(limit, 100);
    
    let query = `
      SELECT TOP (${limitRows})
        v.ID,
        v.NUMERODOCUMENTO AS order_number,
        v.FECHAACTUAL AS order_date,
        v.ORIGINANTE_ID AS customer_id,
        v.CANTIDADTOTAL AS total_quantity,
        v.IMPORTETOTAL AS total_amount
      FROM [V_UD_EZI_ORDEN_VTA_AZ_ITEM] v
      WHERE 1=1
    `;

    const params = {};

    if (date_from) {
      query += ` AND CAST(v.FECHAACTUAL AS DATE) >= @date_from`;
      params.date_from = date_from;
    }

    if (date_to) {
      query += ` AND CAST(v.FECHAACTUAL AS DATE) <= @date_to`;
      params.date_to = date_to;
    }

    if (customer_name) {
      query += ` AND v.NOMBRECLIENT LIKE @customer_name`;
      params.customer_name = `%${customer_name}%`;
    }

    query += ` ORDER BY v.FECHAACTUAL DESC`;

    const result = await executeReadonly(query, (request) => {
      Object.entries(params).forEach(([key, value]) => {
        request.input(key, sql.NVarChar, value);
      });
    });

    return asText({
      ...result,
      message: `Órdenes de venta de azúcar encontradas: ${result.row_count}`
    });
  }
);

server.tool(
  'get_alcohol_sales_data',
  'Obtiene datos de ventas de alcohol (especialización para ingenio).',
  {
    date_from: z.string().optional().describe('Fecha inicial'),
    date_to: z.string().optional().describe('Fecha final'),
    limit: z.number().optional().describe('Límite de registros')
  },
  async ({ date_from, date_to, limit = 50 }) => {
    const limitRows = Math.min(limit, 100);
    
    let query = `
      SELECT TOP (${limitRows})
        v.ID,
        v.NUMERODOCUMENTO AS order_number,
        v.FECHAACTUAL AS order_date,
        v.CANTIDADTOTAL AS quantity,
        v.IMPORTETOTAL AS amount,
        v.UNIDAD AS unit
      FROM [V_UD_EZI_ORDEN_ALCOHOL] v
      WHERE 1=1
    `;

    const params = {};

    if (date_from) {
      query += ` AND CAST(v.FECHAACTUAL AS DATE) >= @date_from`;
      params.date_from = date_from;
    }

    if (date_to) {
      query += ` AND CAST(v.FECHAACTUAL AS DATE) <= @date_to`;
      params.date_to = date_to;
    }

    query += ` ORDER BY v.FECHAACTUAL DESC`;

    const result = await executeReadonly(query, (request) => {
      Object.entries(params).forEach(([key, value]) => {
        request.input(key, sql.NVarChar, value);
      });
    });

    return asText({
      ...result,
      message: `Datos de venta de alcohol encontrados: ${result.row_count}`
    });
  }
);

server.tool(
  'get_accounting_reports',
  'Accede a informes contables y datos de análisis gerencial.',
  {
    report_type: z.enum(['CUENTAS_PAGAR', 'CUENTAS_COBRAR', 'CASHFLOW', 'PRESUPUESTO', 'COSTOS']).describe('Tipo de informe'),
    date_from: z.string().optional().describe('Fecha inicial'),
    date_to: z.string().optional().describe('Fecha final'),
    business_unit: z.string().optional().describe('Unidad de negocio')
  },
  async ({ report_type, date_from, date_to, business_unit }) => {
    let query = '';
    const params = {};

    switch (report_type) {
      case 'CUENTAS_PAGAR':
        query = `
          SELECT TOP 100
            v.ID,
            v.PROVEEDOR AS supplier,
            v.MONTO AS amount,
            v.MONEDA AS currency,
            v.ESTADO AS status,
            v.FECHA_VENCIMIENTO AS due_date
          FROM [V_UD_UOCUENTASPAGAR] v
          WHERE 1=1
        `;
        break;

      case 'CUENTAS_COBRAR':
        query = `
          SELECT TOP 100
            v.ID,
            v.CLIENTE AS customer,
            v.MONTO AS amount,
            v.MONEDA AS currency,
            v.ESTADO AS status,
            v.FECHA_VENCIMIENTO AS due_date
          FROM [V_UD_UOCUENTASCOBRAR] v
          WHERE 1=1
        `;
        break;

      case 'CASHFLOW':
        query = `
          SELECT TOP 100
            v.ID,
            v.DESCRIPCION AS description,
            v.ENTRADA AS inflow,
            v.SALIDA AS outflow,
            v.NETO AS net,
            v.FECHA AS transaction_date
          FROM [V_UD_UOCASHFLOW] v
          WHERE 1=1
        `;
        break;

      case 'COSTOS':
        query = `
          SELECT TOP 100
            v.ID,
            v.CONCEPTO AS concept,
            v.MONTO AS amount,
            v.TIPO AS type,
            v.FECHA AS date
          FROM [ITEMCONTABLE] v
          WHERE UPPER(v.DESCRIPCION) LIKE '%COSTO%'
        `;
        break;

      default:
        throw new Error('Tipo de informe no soportado');
    }

    if (date_from) {
      query += ` AND CAST(v.FECHA AS DATE) >= @date_from`;
      params.date_from = date_from;
    }

    if (date_to) {
      query += ` AND CAST(v.FECHA AS DATE) <= @date_to`;
      params.date_to = date_to;
    }

    query += ` ORDER BY v.FECHA DESC`;

    const result = await executeReadonly(query, (request) => {
      Object.entries(params).forEach(([key, value]) => {
        request.input(key, sql.NVarChar, value);
      });
    });

    return asText({
      ...result,
      report_type,
      message: `Informe ${report_type} generado: ${result.row_count} registros`
    });
  }
);

const transport = new StdioServerTransport();
await server.connect(transport);

function requiredEnv(name) {
  const value = process.env[name];
  if (!value) throw new Error(`Falta variable de entorno ${name}`);
  return value;
}

function parseBool(value, defaultValue) {
  if (value === undefined) return defaultValue;
  return ['1', 'true', 'yes', 'si'].includes(String(value).toLowerCase());
}

async function getPool() {
  if (!poolPromise) poolPromise = sql.connect(config);
  return poolPromise;
}

async function executeReadonly(query, bindParams) {
  validateReadonlyBatch(query);
  const traceId = uuidv4();
  const startedAt = new Date();
  const pool = await getPool();
  const request = pool.request();
  if (bindParams) bindParams(request);
  const result = await request.query(query);
  await writeAuditLog({ traceId, startedAt, query, rows: result.recordset?.length || 0 });
  return {
    trace_id: traceId,
    rows: result.recordset || [],
    row_count: result.recordset?.length || 0,
    recordsets: result.recordsets?.length || 1
  };
}

function validateReadonlyBatch(query) {
  const forbidden = /\b(INSERT|UPDATE|DELETE|MERGE|DROP|ALTER|CREATE|TRUNCATE|EXEC|EXECUTE|GRANT|REVOKE|DENY|BACKUP|RESTORE|DBCC|BULK)\b/i;
  if (forbidden.test(stripComments(query))) {
    throw new Error('Consulta bloqueada: el MCP Calipso solo permite lectura.');
  }
}

function validateSelectOnly(query) {
  const normalized = stripComments(query).trim();
  if (!/^SELECT\b/i.test(normalized)) {
    throw new Error('Solo se permiten consultas SELECT.');
  }
  if (normalized.includes(';')) {
    throw new Error('No se permiten multiples sentencias en run_readonly_query.');
  }
}

function applyTopLimit(query) {
  const normalized = query.trim();
  if (/^SELECT\s+TOP\s*\(/i.test(normalized) || /^SELECT\s+TOP\s+\d+/i.test(normalized)) return normalized;
  return normalized.replace(/^SELECT\s+/i, `SELECT TOP (${maxRows}) `);
}

function stripComments(query) {
  return query
    .replace(/\/\*[\s\S]*?\*\//g, ' ')
    .replace(/--.*$/gm, ' ');
}

function assertAllowedSchema(schema) {
  if (schema && !allowedSchemas.includes(schema)) {
    throw new Error(`Schema no permitido: ${schema}`);
  }
}

function assertIdentifier(value, label) {
  if (!/^[A-Za-z_][A-Za-z0-9_@$#]*$/.test(value)) {
    throw new Error(`Identificador invalido para ${label}: ${value}`);
  }
}

function quoteName(value) {
  assertIdentifier(value, 'sql identifier');
  return `[${value.replace(/]/g, ']]')}]`;
}

function parameterList(prefix, count) {
  return Array.from({ length: count }, (_, index) => `@${prefix}${index}`).join(', ');
}

function bindList(request, prefix, values) {
  values.forEach((value, index) => request.input(`${prefix}${index}`, sql.NVarChar, value));
}

async function writeAuditLog(entry) {
  const logDir = process.env.MSSQL_LOG_DIR || './logs';
  await fs.mkdir(logDir, { recursive: true });
  const line = JSON.stringify({
    ...entry,
    startedAt: entry.startedAt.toISOString(),
    finishedAt: new Date().toISOString()
  });
  await fs.appendFile(path.join(logDir, 'mcp-calipso-sqlserver.jsonl'), `${line}\n`, 'utf8');
}

function asText(payload) {
  return {
    content: [
      {
        type: 'text',
        text: JSON.stringify(payload, null, 2)
      }
    ]
  };
}
