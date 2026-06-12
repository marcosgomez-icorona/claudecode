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
  console.log('=== TABLAS BASE EN CORONA (ESTRUCTURA CALIPSO) ===\n');

  // Obtener tablas base
  const baseTables = await pool.request().query(`
    SELECT TOP 30
      t.name AS table_name,
      SUM(CASE WHEN p.index_id IN (0, 1) THEN p.rows ELSE 0 END) AS row_count
    FROM sys.tables t
    LEFT JOIN sys.partitions p ON p.object_id = t.object_id
    WHERE t.name NOT LIKE 'sys%'
      AND t.name NOT LIKE 'V_%'
      AND (t.name LIKE '%act%' 
        OR t.name LIKE '%prov%'
        OR t.name LIKE '%oc%'
        OR t.name LIKE '%orden%'
        OR t.name LIKE '%recep%'
        OR t.name LIKE '%item%'
        OR t.name LIKE '%compr%'
        OR t.name LIKE '%remt%'
        OR t.name LIKE '%asient%')
    GROUP BY t.name
    ORDER BY row_count DESC
  `);

  console.log('📊 TABLAS BASE (Facturas, OC, Recepción, Items, etc):');
  console.log('─'.repeat(70));
  baseTables.recordset.forEach(row => {
    console.log(`  ${row.table_name.padEnd(45)} (${row.row_count?.toLocaleString() || '?'} registros)`);
  });

  // Describir principales tablas
  const keyTables = ['FACTURA', 'FACTURACOMPRA', 'FACTURAVENTA', 'ORDENCOMPRA', 'ORDENC', 'RECEPMERCADERIA', 'ITEMFACTURA'];
  
  for (const table of keyTables) {
    try {
      const cols = await pool.request().query(`
        SELECT TOP 1 1 FROM sys.tables WHERE name LIKE '${table}%'
      `);
      
      if (cols.recordset.length > 0) {
        console.log(`\n📋 ESTRUCTURA DE ${table}:`);
        console.log('─'.repeat(70));
        
        const desc = await pool.request().query(`
          SELECT 
            COLUMN_NAME,
            DATA_TYPE,
            CHARACTER_MAXIMUM_LENGTH,
            IS_NULLABLE
          FROM INFORMATION_SCHEMA.COLUMNS
          WHERE TABLE_NAME LIKE '${table}%'
          ORDER BY ORDINAL_POSITION
        `);
        
        desc.recordset.slice(0, 10).forEach(col => {
          const type = col.DATA_TYPE + (col.CHARACTER_MAXIMUM_LENGTH ? `(${col.CHARACTER_MAXIMUM_LENGTH})` : '');
          console.log(`    ${col.COLUMN_NAME.padEnd(35)} ${type.padEnd(20)} ${col.IS_NULLABLE ? 'NULL' : 'NOT NULL'}`);
        });
        if (desc.recordset.length > 10) console.log(`    ... + ${desc.recordset.length - 10} más columnas`);
      }
    } catch (e) {
      // Tabla no encontrada
    }
  }

  console.log('\n✅ Análisis completado\n');

} catch (error) {
  console.error('❌ Error:', error.message);
} finally {
  await pool.close();
}
