import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import dotenv from 'dotenv';
import mysql from 'mysql2/promise';
import { v4 as uuidv4 } from 'uuid';
import { McpServer } from '@modelcontextprotocol/sdk/server/mcp.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { z } from 'zod';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
dotenv.config({ path: path.join(__dirname, '..', '.env') });

const maxRows = Number.parseInt(process.env.MYSQL_MAX_ROWS || '200', 10);
const allowedDatabases = (process.env.MYSQL_ALLOWED_DATABASES || process.env.MYSQL_DATABASE || 'db_automatizaciones')
  .split(',')
  .map((db) => db.trim())
  .filter(Boolean);

const config = {
  host: requiredEnv('MYSQL_HOST'),
  port: Number.parseInt(process.env.MYSQL_PORT || '3306', 10),
  user: requiredEnv('MYSQL_USER'),
  password: requiredEnv('MYSQL_PASSWORD'),
  database: process.env.MYSQL_DATABASE || allowedDatabases[0],
  charset: 'utf8mb4',
  waitForConnections: true,
  connectionLimit: 3,
  maxIdle: 1,
  idleTimeout: 30000
};

let pool;

const server = new McpServer({
  name: 'mysql-corona-readonly',
  version: '0.1.0'
});

// ── healthcheck ──────────────────────────────────────────────────────────────

server.tool(
  'healthcheck',
  'Verifica conexión contra MySQL y devuelve base, versión y usuario actual.',
  {},
  async () => {
    const result = await executeReadonly(
      `SELECT DATABASE() AS \`database\`, USER() AS current_user, VERSION() AS mysql_version`
    );
    return asText(result);
  }
);

// ── list_tables ───────────────────────────────────────────────────────────────

server.tool(
  'list_tables',
  'Lista tablas y vistas de las bases permitidas. Opcional: filtrar por base o nombre.',
  {
    database: z.string().optional().describe('Base de datos MySQL. Por defecto usa todas las permitidas.'),
    filter: z.string().optional().describe('Filtrar nombre de tabla (LIKE %term%).')
  },
  async ({ database, filter }) => {
    assertAllowedDatabase(database);
    const dbFilter = database
      ? 'AND TABLE_SCHEMA = ?'
      : `AND TABLE_SCHEMA IN (${allowedDatabases.map(() => '?').join(',')})`;
    const nameFilter = filter ? 'AND TABLE_NAME LIKE ?' : '';

    const dbParams = database ? [database] : allowedDatabases;
    const allParams = [...dbParams];
    if (filter) allParams.push(`%${filter}%`);

    const result = await executeReadonly(
      `SELECT TABLE_SCHEMA AS \`database\`, TABLE_NAME AS table_name, TABLE_TYPE AS table_type,
              TABLE_ROWS AS approx_rows, ENGINE, TABLE_COMMENT AS comment
       FROM INFORMATION_SCHEMA.TABLES
       WHERE TABLE_SCHEMA NOT IN ('mysql','information_schema','performance_schema','sys')
         ${dbFilter} ${nameFilter}
       ORDER BY TABLE_SCHEMA, TABLE_NAME
       LIMIT ${maxRows}`,
      allParams
    );
    return asText(result);
  }
);

// ── describe_table ────────────────────────────────────────────────────────────

server.tool(
  'describe_table',
  'Describe columnas, tipos, nullable, defaults e índices de una tabla.',
  {
    table: z.string().min(1).describe('Nombre de la tabla o vista.'),
    database: z.string().optional().describe('Base de datos (default: db_automatizaciones).')
  },
  async ({ table, database }) => {
    const db = database || config.database;
    assertAllowedDatabase(db);
    assertIdentifier(table, 'table');

    const columns = await executeReadonly(
      `SELECT ORDINAL_POSITION AS ordinal, COLUMN_NAME AS column_name,
              COLUMN_TYPE AS column_type, DATA_TYPE AS data_type,
              IS_NULLABLE AS is_nullable, COLUMN_DEFAULT AS default_value,
              COLUMN_KEY AS column_key, EXTRA, COLUMN_COMMENT AS comment
       FROM INFORMATION_SCHEMA.COLUMNS
       WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
       ORDER BY ORDINAL_POSITION`,
      [db, table]
    );

    const indexes = await executeReadonly(
      `SELECT INDEX_NAME AS index_name, COLUMN_NAME AS column_name,
              NON_UNIQUE, SEQ_IN_INDEX AS seq, INDEX_TYPE AS index_type
       FROM INFORMATION_SCHEMA.STATISTICS
       WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
       ORDER BY INDEX_NAME, SEQ_IN_INDEX`,
      [db, table]
    );

    return asText({
      database: db,
      table,
      columns: columns.rows,
      indexes: indexes.rows
    });
  }
);

// ── sample_table ──────────────────────────────────────────────────────────────

server.tool(
  'sample_table',
  'Devuelve las primeras N filas de una tabla para entender su contenido.',
  {
    table: z.string().min(1).describe('Nombre de la tabla.'),
    rows: z.number().int().min(1).max(100).optional().describe('Cantidad de filas (default 10, max 100).'),
    database: z.string().optional().describe('Base de datos (default: db_automatizaciones).')
  },
  async ({ table, rows: limit, database }) => {
    const db = database || config.database;
    assertAllowedDatabase(db);
    assertIdentifier(table, 'table');
    const rowLimit = Math.min(limit || 10, 100);

    const result = await executeReadonly(
      `SELECT * FROM \`${db}\`.\`${escapeBacktick(table)}\` LIMIT ${rowLimit}`
    );
    return asText(result);
  }
);

// ── run_query ─────────────────────────────────────────────────────────────────

server.tool(
  'run_query',
  'Ejecuta una consulta SELECT controlada. Bloquea escrituras y limita filas.',
  {
    query: z.string().min(8).describe('Consulta SELECT de MySQL.'),
    params: z.string().optional().describe('JSON array opcional con valores para placeholders ?.')
  },
  async ({ query, params }) => {
    validateSelectOnly(query);
    const paramArray = params ? JSON.parse(params) : [];
    if (!Array.isArray(paramArray)) throw new Error('params debe ser un JSON array');

    let limitedQuery = query.trim();
    if (!/LIMIT\s+\d+/i.test(limitedQuery)) {
      limitedQuery = limitedQuery.replace(/;?\s*$/, ` LIMIT ${maxRows}`);
    }

    const result = await executeReadonly(limitedQuery, paramArray);
    return asText(result);
  }
);

// ── search_tables ─────────────────────────────────────────────────────────────

server.tool(
  'search_tables',
  'Busca tablas o columnas cuyo nombre contenga el texto dado. Útil para explorar.',
  {
    term: z.string().min(2).describe('Texto a buscar en nombres de tabla o columna.'),
    database: z.string().optional().describe('Base de datos (default: todas las permitidas).')
  },
  async ({ term, database }) => {
    assertAllowedDatabase(database);
    const dbFilter = database
      ? 'AND c.TABLE_SCHEMA = ?'
      : `AND c.TABLE_SCHEMA IN (${allowedDatabases.map(() => '?').join(',')})`;
    const dbParams = database ? [database] : allowedDatabases;

    const result = await executeReadonly(
      `SELECT c.TABLE_SCHEMA AS \`database\`, c.TABLE_NAME AS table_name,
              c.COLUMN_NAME AS column_name, c.DATA_TYPE AS data_type,
              c.COLUMN_TYPE AS column_type, c.COLUMN_COMMENT AS comment
       FROM INFORMATION_SCHEMA.COLUMNS c
       WHERE c.TABLE_SCHEMA NOT IN ('mysql','information_schema','performance_schema','sys')
         ${dbFilter}
         AND (c.COLUMN_NAME LIKE ? OR c.TABLE_NAME LIKE ?)
       ORDER BY c.TABLE_SCHEMA, c.TABLE_NAME, c.ORDINAL_POSITION
       LIMIT ${maxRows}`,
      [...dbParams, `%${term}%`, `%${term}%`]
    );
    return asText(result);
  }
);

// ── list_databases ────────────────────────────────────────────────────────────

server.tool(
  'list_databases',
  'Lista las bases de datos accesibles por el usuario.',
  {},
  async () => {
    const result = await executeReadonly(
      `SELECT SCHEMA_NAME AS \`database\`
       FROM INFORMATION_SCHEMA.SCHEMATA
       WHERE SCHEMA_NAME NOT IN ('mysql','information_schema','performance_schema','sys')
       ORDER BY SCHEMA_NAME`
    );
    return asText(result);
  }
);

// ── Startup ───────────────────────────────────────────────────────────────────

const transport = new StdioServerTransport();
await server.connect(transport);

// ═══════════════════════════════════════════════════════════════════════════════
// Helpers
// ═══════════════════════════════════════════════════════════════════════════════

function requiredEnv(name) {
  const value = process.env[name];
  if (!value) throw new Error(`Falta variable de entorno ${name}`);
  return value;
}

async function getPool() {
  if (!pool) {
    pool = mysql.createPool(config);
  }
  return pool;
}

async function executeReadonly(query, params = []) {
  validateReadonly(query);
  const traceId = uuidv4();
  const startedAt = new Date();
  const p = await getPool();
  const [rows] = await p.execute(query, params);
  await writeAuditLog({ traceId, startedAt, query, params, rowCount: rows.length });
  return {
    trace_id: traceId,
    rows: rows,
    row_count: rows.length
  };
}

function validateReadonly(query) {
  const forbidden = /\b(INSERT|UPDATE|DELETE|REPLACE|MERGE|DROP|ALTER|CREATE|TRUNCATE|EXECUTE|GRANT|REVOKE|LOCK|UNLOCK|CALL|LOAD|RENAME|BACKUP|RESTORE|FLUSH|SET\s+PASSWORD)\b/i;
  if (forbidden.test(query)) {
    throw new Error('Consulta bloqueada: el MCP MySQL solo permite lectura.');
  }
}

function validateSelectOnly(query) {
  const normalized = query.trim();
  if (!/^(SELECT|SHOW|DESCRIBE|EXPLAIN|WITH)\b/i.test(normalized)) {
    throw new Error('Solo se permiten consultas SELECT, SHOW, DESCRIBE, EXPLAIN o WITH.');
  }
}

function assertAllowedDatabase(database) {
  if (database && !allowedDatabases.includes(database)) {
    throw new Error(`Base de datos no permitida: ${database}. Permitidas: ${allowedDatabases.join(', ')}`);
  }
}

function assertIdentifier(value, label) {
  if (!/^[A-Za-z_][A-Za-z0-9_$]*$/.test(value)) {
    throw new Error(`Identificador inválido para ${label}: ${value}`);
  }
}

function escapeBacktick(value) {
  return value.replace(/`/g, '``');
}

async function writeAuditLog(entry) {
  try {
    const logDir = process.env.MYSQL_LOG_DIR || './logs';
    await fs.mkdir(logDir, { recursive: true });
    const line = JSON.stringify({
      ...entry,
      startedAt: entry.startedAt.toISOString(),
      finishedAt: new Date().toISOString()
    });
    await fs.appendFile(path.join(logDir, 'mcp-mysql-corona.jsonl'), `${line}\n`, 'utf8');
  } catch {
    // Log silencioso — no interrumpe la herramienta
  }
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
