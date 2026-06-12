/**
 * Script para explorar la estructura de CALIPSO
 * Identifica tablas para registrar facturas
 */

const sql = require('mssql');
require('dotenv').config({ path: '../.env' });

const config = {
  server: process.env.MSSQL_SERVER,
  database: process.env.MSSQL_DATABASE,
  authentication: {
    type: 'default',
    options: {
      userName: process.env.MSSQL_USER,
      password: process.env.MSSQL_PASSWORD,
    }
  },
  options: {
    trustServerCertificate: true,
    enableKeepAlive: true,
    connectionTimeout: 30000,
    requestTimeout: 30000,
  }
};

async function explore() {
  const pool = new sql.ConnectionPool(config);
  
  try {
    await pool.connect();
    console.log('✅ Conectado a CALIPSO\n');

    // 1. Buscar tablas de compras/facturas
    console.log('═'.repeat(80));
    console.log('1️⃣  TABLAS DE FACTURAS DE COMPRA');
    console.log('═'.repeat(80));
    const facturas = await pool.request()
      .query(`
        SELECT TABLE_NAME 
        FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_SCHEMA = 'dbo' 
        AND (
          TABLE_NAME LIKE '%FACTUR%' 
          OR TABLE_NAME LIKE '%COMPRA%' 
          OR TABLE_NAME LIKE '%PROVEEDOR%'
          OR TABLE_NAME LIKE '%INVOICE%'
        )
        ORDER BY TABLE_NAME
      `);
    
    console.log('\nTablas encontradas:');
    facturas.recordset.forEach(t => console.log(`  • ${t.TABLE_NAME}`));

    // 2. Explorar FACTURACOMPRA en detalle
    console.log('\n' + '═'.repeat(80));
    console.log('2️⃣  ESTRUCTURA DE FACTURACOMPRA');
    console.log('═'.repeat(80));
    
    const facturaSchema = await pool.request()
      .query(`
        SELECT 
          COLUMN_NAME,
          DATA_TYPE,
          IS_NULLABLE,
          COLUMN_DEFAULT
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = 'FACTURACOMPRA'
        ORDER BY ORDINAL_POSITION
      `);
    
    console.log('\nColumnas:');
    facturaSchema.recordset.forEach(col => {
      const nullable = col.IS_NULLABLE === 'YES' ? '✓' : '✗';
      console.log(`  ${col.COLUMN_NAME.padEnd(25)} | ${col.DATA_TYPE.padEnd(15)} | Nullable: ${nullable} | Default: ${col.COLUMN_DEFAULT || 'N/A'}`);
    });

    // 3. Explorar ITEMFACTURACOMPRA
    console.log('\n' + '═'.repeat(80));
    console.log('3️⃣  ESTRUCTURA DE ITEMFACTURACOMPRA');
    console.log('═'.repeat(80));
    
    const itemSchema = await pool.request()
      .query(`
        SELECT 
          COLUMN_NAME,
          DATA_TYPE,
          IS_NULLABLE,
          COLUMN_DEFAULT
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = 'ITEMFACTURACOMPRA'
        ORDER BY ORDINAL_POSITION
      `);
    
    console.log('\nColumnas:');
    itemSchema.recordset.forEach(col => {
      const nullable = col.IS_NULLABLE === 'YES' ? '✓' : '✗';
      console.log(`  ${col.COLUMN_NAME.padEnd(25)} | ${col.DATA_TYPE.padEnd(15)} | Nullable: ${nullable}`);
    });

    // 4. Buscar tabla de asientos/comprobantes
    console.log('\n' + '═'.repeat(80));
    console.log('4️⃣  TABLAS CONTABLES');
    console.log('═'.repeat(80));
    
    const contables = await pool.request()
      .query(`
        SELECT TABLE_NAME 
        FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_SCHEMA = 'dbo' 
        AND (
          TABLE_NAME LIKE '%CONTAB%' 
          OR TABLE_NAME LIKE '%ASIENT%'
          OR TABLE_NAME LIKE '%COMPRO%'
          OR TABLE_NAME LIKE '%DIARIO%'
          OR TABLE_NAME LIKE '%ITEM%'
        )
        ORDER BY TABLE_NAME
      `);
    
    console.log('\nTablas encontradas:');
    contables.recordset.forEach(t => console.log(`  • ${t.TABLE_NAME}`));

    // 5. Explorar ITEMCONTABLE
    console.log('\n' + '═'.repeat(80));
    console.log('5️⃣  ESTRUCTURA DE ITEMCONTABLE');
    console.log('═'.repeat(80));
    
    const itemContableSchema = await pool.request()
      .query(`
        SELECT 
          COLUMN_NAME,
          DATA_TYPE,
          IS_NULLABLE,
          COLUMN_DEFAULT
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = 'ITEMCONTABLE'
        ORDER BY ORDINAL_POSITION
      `);
    
    console.log('\nColumnas:');
    itemContableSchema.recordset.forEach(col => {
      const nullable = col.IS_NULLABLE === 'YES' ? '✓' : '✗';
      console.log(`  ${col.COLUMN_NAME.padEnd(25)} | ${col.DATA_TYPE.padEnd(15)} | Nullable: ${nullable}`);
    });

    // 6. Buscar stored procedures
    console.log('\n' + '═'.repeat(80));
    console.log('6️⃣  PROCEDIMIENTOS ALMACENADOS (Almacenamiento)');
    console.log('═'.repeat(80));
    
    const procedures = await pool.request()
      .query(`
        SELECT ROUTINE_NAME 
        FROM INFORMATION_SCHEMA.ROUTINES 
        WHERE ROUTINE_SCHEMA = 'dbo' 
        AND ROUTINE_TYPE = 'PROCEDURE'
        AND (
          ROUTINE_NAME LIKE '%FACTUR%' 
          OR ROUTINE_NAME LIKE '%COMPRA%'
          OR ROUTINE_NAME LIKE '%INSERT%'
          OR ROUTINE_NAME LIKE '%REGISTR%'
        )
        ORDER BY ROUTINE_NAME
      `);
    
    console.log('\nProcedimientos encontrados:');
    if (procedures.recordset.length > 0) {
      procedures.recordset.forEach(p => console.log(`  • ${p.ROUTINE_NAME}`));
    } else {
      console.log('  (Ninguno encontrado, usaremos INSERT directo)');
    }

    // 7. Constraints y Foreign Keys
    console.log('\n' + '═'.repeat(80));
    console.log('7️⃣  RELACIONES Y CONSTRAINTS');
    console.log('═'.repeat(80));
    
    const fks = await pool.request()
      .query(`
        SELECT 
          KCU1.CONSTRAINT_NAME,
          KCU1.TABLE_NAME,
          KCU1.COLUMN_NAME,
          KCU2.TABLE_NAME AS REFERENCED_TABLE,
          KCU2.COLUMN_NAME AS REFERENCED_COLUMN
        FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS RC
        JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE KCU1 
          ON KCU1.CONSTRAINT_NAME = RC.CONSTRAINT_NAME
        JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE KCU2 
          ON KCU2.CONSTRAINT_NAME = RC.UNIQUE_CONSTRAINT_NAME
        WHERE KCU1.TABLE_NAME IN ('FACTURACOMPRA', 'ITEMFACTURACOMPRA', 'ITEMCONTABLE')
        ORDER BY KCU1.TABLE_NAME, KCU1.COLUMN_NAME
      `);
    
    console.log('\nForeign Keys:');
    fks.recordset.forEach(fk => {
      console.log(`  ${fk.TABLE_NAME}.${fk.COLUMN_NAME} → ${fk.REFERENCED_TABLE}.${fk.REFERENCED_COLUMN}`);
    });

    // 8. Buscar campos de auditoría/control
    console.log('\n' + '═'.repeat(80));
    console.log('8️⃣  CAMPOS DE AUDITORÍA/ESTADO');
    console.log('═'.repeat(80));
    
    const audit = await pool.request()
      .query(`
        SELECT 
          TABLE_NAME,
          STRING_AGG(COLUMN_NAME, ', ') AS COLUMNS
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME IN ('FACTURACOMPRA', 'ITEMFACTURACOMPRA')
        AND (
          COLUMN_NAME LIKE '%ESTADO%'
          OR COLUMN_NAME LIKE '%FECHA%'
          OR COLUMN_NAME LIKE '%USUARIO%'
          OR COLUMN_NAME LIKE '%REGISTRO%'
          OR COLUMN_NAME LIKE '%ACTIVO%'
        )
        GROUP BY TABLE_NAME
      `);
    
    console.log('\nCampos de auditoría encontrados:');
    audit.recordset.forEach(t => {
      console.log(`  ${t.TABLE_NAME}: ${t.COLUMNS}`);
    });

    // 9. Datos de ejemplo
    console.log('\n' + '═'.repeat(80));
    console.log('9️⃣  DATOS DE EJEMPLO (Últimas facturas)');
    console.log('═'.repeat(80));
    
    const ejemplos = await pool.request()
      .query(`
        SELECT TOP 5
          ID_FACTURA,
          NUMERO_FACTURA,
          FECHA_FACTURA,
          CUIT_PROVEEDOR,
          NOMBRE_PROVEEDOR,
          MONTO_TOTAL,
          ESTADO
        FROM FACTURACOMPRA
        ORDER BY FECHA_FACTURA DESC
      `);
    
    console.log('\nÚltimas facturas registradas:');
    ejemplos.recordset.forEach(f => {
      console.log(`  ID: ${f.ID_FACTURA} | Nro: ${f.NUMERO_FACTURA} | Monto: ${f.MONTO_TOTAL} | Estado: ${f.ESTADO}`);
    });

    console.log('\n' + '═'.repeat(80));
    console.log('✅ Análisis completado\n');

  } catch (error) {
    console.error('❌ Error:', error.message);
    if (error.code === 'ENOTFOUND') {
      console.error('   → No se pudo conectar al servidor SQL');
    }
  } finally {
    await pool.close();
  }
}

explore();
