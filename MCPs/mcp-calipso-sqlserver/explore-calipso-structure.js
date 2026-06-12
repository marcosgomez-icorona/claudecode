/**
 * Script para explorar la estructura de CALIPSO
 * Identifica tablas para registrar facturas
 */

import sql from 'mssql';
import dotenv from 'dotenv';

dotenv.config();

const config = {
  server: process.env.MSSQL_SERVER,
  database: process.env.MSSQL_DATABASE,
  user: process.env.MSSQL_USER,
  password: process.env.MSSQL_PASSWORD,
  options: {
    encrypt: false,
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
    console.log('6️⃣  PROCEDIMIENTOS ALMACENADOS');
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
      console.log('  (Ninguno encontrado)');
    }

    // 7. Buscar campos de auditoría/control
    console.log('\n' + '═'.repeat(80));
    console.log('7️⃣  CAMPOS DE AUDITORÍA/ESTADO');
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
    if (audit.recordset.length > 0) {
      audit.recordset.forEach(t => {
        console.log(`  ${t.TABLE_NAME}: ${t.COLUMNS}`);
      });
    } else {
      console.log('  (Ninguno encontrado)');
    }

    console.log('\n' + '═'.repeat(80));
    console.log('8️⃣  DATOS DE EJEMPLO (Últimas facturas)');
    console.log('═'.repeat(80));
    
    try {
      const ejemplos = await pool.request()
        .query(`
          SELECT TOP 5 *
          FROM FACTURACOMPRA
          ORDER BY (SELECT 1) DESC
        `);
      
      if (ejemplos.recordset.length > 0) {
        console.log(`\nÚltimas ${ejemplos.recordset.length} facturas registradas:`);
        ejemplos.recordset.forEach((f, idx) => {
          console.log(`  ${idx + 1}. ${JSON.stringify(f).substring(0, 80)}...`);
        });
      } else {
        console.log('  (No hay facturas registradas aún)');
      }
    } catch (e) {
      console.log('  (No se pudo acceder a datos de ejemplo)');
    }

    console.log('\n' + '═'.repeat(80));
    console.log('✅ Análisis completado\n');

  } catch (error) {
    console.error('\n❌ Error:', error.message);
  } finally {
    await pool.close();
  }
}

await explore();
