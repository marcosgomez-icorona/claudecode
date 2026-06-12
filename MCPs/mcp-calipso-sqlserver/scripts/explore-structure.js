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
  console.log('=== ANÁLISIS DE ESTRUCTURA CORONA ===\n');

  // Buscar tablas relacionadas a facturas
  const invoiceSearch = await pool.request().query(`
    SELECT 
      TABLE_SCHEMA AS schema_name,
      TABLE_NAME AS table_name,
      COUNT(*) AS column_count
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME LIKE '%fact%' 
       OR TABLE_NAME LIKE '%factura%'
       OR TABLE_NAME LIKE '%fac%'
       OR TABLE_NAME LIKE '%prove%'
       OR TABLE_NAME LIKE '%oc%'
       OR TABLE_NAME LIKE '%orden%'
       OR TABLE_NAME LIKE '%recep%'
       OR TABLE_NAME LIKE '%item%'
       OR TABLE_NAME LIKE '%asiento%'
       OR TABLE_NAME LIKE '%compra%'
    GROUP BY TABLE_SCHEMA, TABLE_NAME
    ORDER BY TABLE_NAME
  `);

  console.log('📋 TABLAS RELACIONADAS A FACTURAS/OC/PROVEEDORES:');
  console.log('─'.repeat(60));
  invoiceSearch.recordset.forEach(row => {
    console.log(`  ${row.table_name.padEnd(40)} (${row.column_count} columnas)`);
  });

  // Procesos almacenados
  const procedures = await pool.request().query(`
    SELECT 
      ROUTINE_SCHEMA AS schema_name,
      ROUTINE_NAME AS procedure_name,
      ROUTINE_TYPE AS type
    FROM INFORMATION_SCHEMA.ROUTINES
    WHERE ROUTINE_NAME LIKE '%fact%' 
       OR ROUTINE_NAME LIKE '%factura%'
       OR ROUTINE_NAME LIKE '%prove%'
       OR ROUTINE_NAME LIKE '%oc%'
       OR ROUTINE_NAME LIKE '%item%'
       OR ROUTINE_NAME LIKE '%compra%'
    ORDER BY ROUTINE_NAME
  `);

  console.log('\n\n🔧 PROCEDIMIENTOS/FUNCIONES RELACIONADAS:');
  console.log('─'.repeat(60));
  procedures.recordset.forEach(row => {
    console.log(`  ${row.procedure_name.padEnd(50)} [${row.type}]`);
  });

  // Vistas
  const views = await pool.request().query(`
    SELECT 
      TABLE_SCHEMA AS schema_name,
      TABLE_NAME AS view_name
    FROM INFORMATION_SCHEMA.VIEWS
    WHERE TABLE_NAME LIKE '%fact%' 
       OR TABLE_NAME LIKE '%factura%'
       OR TABLE_NAME LIKE '%prove%'
       OR TABLE_NAME LIKE '%oc%'
       OR TABLE_NAME LIKE '%compra%'
    ORDER BY TABLE_NAME
  `);

  console.log('\n\n📊 VISTAS RELACIONADAS:');
  console.log('─'.repeat(60));
  views.recordset.forEach(row => {
    console.log(`  ${row.view_name}`);
  });

  // Estadísticas de tablas principales
  console.log('\n\n📈 ESTADÍSTICAS DE TABLAS PRINCIPALES:');
  console.log('─'.repeat(60));
  
  const stats = await pool.request().query(`
    SELECT TOP 20
      t.name AS table_name,
      SUM(CASE WHEN p.index_id IN (0, 1) THEN p.rows ELSE 0 END) AS row_count
    FROM sys.tables t
    LEFT JOIN sys.partitions p ON p.object_id = t.object_id
    WHERE t.name NOT LIKE 'sys%'
      AND (t.name LIKE '%fact%' 
        OR t.name LIKE '%factura%'
        OR t.name LIKE '%prove%'
        OR t.name LIKE '%oc%'
        OR t.name LIKE '%item%'
        OR t.name LIKE '%compra%')
    GROUP BY t.name
    ORDER BY row_count DESC
  `);

  stats.recordset.forEach(row => {
    console.log(`  ${row.table_name.padEnd(40)} (${row.row_count?.toLocaleString() || '?'} registros)`);
  });

  console.log('\n✅ Análisis completado\n');

} catch (error) {
  console.error('❌ Error:', error.message);
} finally {
  await pool.close();
}
