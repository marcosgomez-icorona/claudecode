import 'dotenv/config';
import sql from 'mssql';

const config = {
  server: process.env.MSSQL_SERVER,
  port: Number(process.env.MSSQL_PORT || 1433),
  database: process.env.MSSQL_DATABASE,
  user: process.env.MSSQL_USER,
  password: process.env.MSSQL_PASSWORD,
  options: {
    encrypt: String(process.env.MSSQL_ENCRYPT).toLowerCase() === 'true',
    trustServerCertificate: String(process.env.MSSQL_TRUST_SERVER_CERTIFICATE).toLowerCase() === 'true'
  },
  requestTimeout: Number(process.env.MSSQL_REQUEST_TIMEOUT_MS || 30000)
};

const pool = await sql.connect(config);
try {
  const result = await pool.request().query(`
    SELECT
      DB_NAME() AS database_name,
      SYSTEM_USER AS current_system_user,
      SUSER_SNAME() AS login_name,
      @@VERSION AS sql_server_version;

    SELECT COUNT(*) AS dbo_tables
    FROM sys.tables t
    INNER JOIN sys.schemas s ON s.schema_id = t.schema_id
    WHERE s.name = 'dbo';
  `);

  const health = result.recordsets[0][0];
  const tables = result.recordsets[1][0];
  console.log(JSON.stringify({
    database: health.database_name,
    current_system_user: health.current_system_user,
    login_name: health.login_name,
    sql_server: String(health.sql_server_version).split(' - ')[0],
    dbo_tables: tables.dbo_tables
  }, null, 2));
} finally {
  await pool.close();
}
