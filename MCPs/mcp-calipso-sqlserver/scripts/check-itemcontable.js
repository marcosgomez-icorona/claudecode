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
  // Obtener estructura de ITEMCONTABLE
  const struct = await pool.request().query(`
    SELECT TOP 20
      COLUMN_NAME,
      DATA_TYPE,
      CHARACTER_MAXIMUM_LENGTH,
      IS_NULLABLE
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'ITEMCONTABLE'
    ORDER BY ORDINAL_POSITION
  `);

  console.log('COLUMNAS DE ITEMCONTABLE:\n');
  struct.recordset.forEach(col => {
    const type = col.DATA_TYPE + (col.CHARACTER_MAXIMUM_LENGTH ? `(${col.CHARACTER_MAXIMUM_LENGTH})` : '');
    console.log(`  ${col.COLUMN_NAME.padEnd(35)} ${type.padEnd(20)} ${col.IS_NULLABLE ? 'NULL' : 'NN'}`);
  });

} catch (error) {
  console.error('❌ Error:', error.message);
} finally {
  await pool.close();
}
