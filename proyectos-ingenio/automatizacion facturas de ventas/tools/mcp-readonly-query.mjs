import { createRequire } from 'module';

const mcpRequire = createRequire('C:/claudecode/MCPs/mcp-calipso-sqlserver/package.json');
const sql = mcpRequire('mssql');
const dotenv = mcpRequire('dotenv');

const envPath = process.env.MCP_ENV_PATH || 'C:/claudecode/MCPs/mcp-calipso-sqlserver/.env';
dotenv.config({ path: envPath });

const query = process.argv.slice(2).join(' ');

if (!query || !/^\s*SELECT\b/i.test(query)) {
  console.error('Uso: node tools/mcp-readonly-query.mjs "SELECT TOP 10 ..."');
  process.exit(1);
}

const cleaned = query
  .replace(/\/\*[\s\S]*?\*\//g, ' ')
  .replace(/--.*$/gm, ' ')
  .trim();

if (/[;]/.test(cleaned) || /\b(INSERT|UPDATE|DELETE|MERGE|DROP|ALTER|CREATE|TRUNCATE|EXEC|EXECUTE|GRANT|REVOKE|DENY|BACKUP|RESTORE|DBCC|BULK)\b/i.test(cleaned)) {
  console.error('Consulta bloqueada: solo se permite una sentencia SELECT readonly.');
  process.exit(1);
}

const config = {
  server: process.env.MSSQL_SERVER,
  port: Number(process.env.MSSQL_PORT || 1433),
  database: process.env.MSSQL_DATABASE,
  user: process.env.MSSQL_USER,
  password: process.env.MSSQL_PASSWORD,
  options: {
    encrypt: String(process.env.MSSQL_ENCRYPT || 'false').toLowerCase() === 'true',
    trustServerCertificate: String(process.env.MSSQL_TRUST_SERVER_CERTIFICATE || 'true').toLowerCase() === 'true',
  },
};

const pool = new sql.ConnectionPool(config);

try {
  await pool.connect();
  const result = await pool.request().query(cleaned);
  console.log(JSON.stringify({
    database: process.env.MSSQL_DATABASE,
    row_count: result.recordset?.length || 0,
    rows: result.recordset || [],
  }, null, 2));
} finally {
  await pool.close();
}
