/**
 * sqlserver.js — Pool de conexión a SQL Server CORONA (Calipso)
 * Usuario readonly para consultas. SQL Server 2008 R2 compatible.
 *
 * mssql se importa dinámicamente solo cuando se usa (evita bloqueos en WSL).
 * Para pruebas sin DB: DB_SKIP=true en .env
 */

import { log } from '../logger.js';

const SKIP_DB = process.env.DB_SKIP === 'true';

const config = {
  server:   process.env.MSSQL_HOST     || '192.168.0.177',
  port:     parseInt(process.env.MSSQL_PORT, 10) || 1433,
  database: process.env.MSSQL_DATABASE || 'CORONA',
  user:     process.env.MSSQL_USER     || 'powerbi',
  password: process.env.MSSQL_PASS     || '',
  options: {
    trustServerCertificate: true,
    encrypt:                false,
    connectTimeout:         10000,
    requestTimeout:         30000,
  },
  pool: {
    max: 5,
    min: 1,
    idleTimeoutMillis: 60000,
  },
};

let pool = null;
let _sql = null;

async function getSql() {
  if (_sql) return _sql;
  _sql = await import('mssql');
  return _sql;
}

export async function getPool() {
  if (SKIP_DB) throw new Error('DB_SKIP=true');
  if (pool) return pool;
  const sql = await getSql();
  pool = new sql.ConnectionPool(config);
  await pool.connect();
  log.info(`MSSQL conectado a ${config.server}:${config.port}/${config.database}`);
  return pool;
}

export async function query(sqlText, params = {}) {
  if (SKIP_DB) return [];
  const sql = await getSql();
  const p = await getPool();
  const req = p.request();
  for (const [k, v] of Object.entries(params)) {
    if (k.startsWith('_int_')) {
      req.input(k.replace('_int_', ''), sql.Int, v);
    } else if (k.startsWith('_dec_')) {
      req.input(k.replace('_dec_', ''), sql.Decimal(22, 10), v);
    } else if (k.startsWith('_uid_')) {
      req.input(k.replace('_uid_', ''), sql.UniqueIdentifier, v);
    } else {
      req.input(k, sql.VarChar, String(v));
    }
  }
  const result = await req.query(sqlText);
  return result.recordset;
}

export { getSql };
