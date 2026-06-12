/**
 * Script para explorar la estructura de CALIPSO
 * Guarda el análisis en un archivo
 */

import sql from 'mssql';
import dotenv from 'dotenv';
import fs from 'fs/promises';
import path from 'path';

dotenv.config();

const config = {
  server: process.env.MSSQL_SERVER,
  database: process.env.MSSQL_DATABASE,
  user: process.env.MSSQL_USER,
  password: process.env.MSSQL_PASSWORD,
  options: {
    encrypt: false,
    trustServerCertificate: true,
  }
};

let output = [];

const log = (msg) => {
  console.log(msg);
  output.push(msg);
};

async function explore() {
  const pool = new sql.ConnectionPool(config);
  
  try {
    await pool.connect();
    log('\n✅ Conectado a CALIPSO - ' + new Date().toISOString());

    // 1. FACTURACOMPRA
    log('\n' + '═'.repeat(80));
    log('1️⃣  ESTRUCTURA DE FACTURACOMPRA');
    log('═'.repeat(80));
    
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
    
    log(`\nTotal columnas: ${facturaSchema.recordset.length}`);
    log('\nColumnas:');
    facturaSchema.recordset.forEach(col => {
      const nullable = col.IS_NULLABLE === 'YES' ? '✓' : '✗';
      log(`  ${col.COLUMN_NAME.padEnd(30)} | ${col.DATA_TYPE.padEnd(20)} | Nullable: ${nullable}`);
    });

    // 2. ITEMFACTURACOMPRA
    log('\n' + '═'.repeat(80));
    log('2️⃣  ESTRUCTURA DE ITEMFACTURACOMPRA');
    log('═'.repeat(80));
    
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
    
    log(`\nTotal columnas: ${itemSchema.recordset.length}`);
    log('\nColumnas:');
    itemSchema.recordset.forEach(col => {
      const nullable = col.IS_NULLABLE === 'YES' ? '✓' : '✗';
      log(`  ${col.COLUMN_NAME.padEnd(30)} | ${col.DATA_TYPE.padEnd(20)} | Nullable: ${nullable}`);
    });

    // 3. Tabla de asientos contables
    log('\n' + '═'.repeat(80));
    log('3️⃣  TABLAS CONTABLES');
    log('═'.repeat(80));
    
    const contables = await pool.request()
      .query(`
        SELECT TABLE_NAME 
        FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_SCHEMA = 'dbo' 
        AND TABLE_NAME LIKE '%CONTAB%'
        ORDER BY TABLE_NAME
      `);
    
    log('\nTablas encontradas:');
    if (contables.recordset.length > 0) {
      contables.recordset.forEach(t => log(`  • ${t.TABLE_NAME}`));
    } else {
      log('  (Ninguna encontrada)');
    }

    // 4. Explorar ITEMCONTABLE
    if (contables.recordset.some(t => t.TABLE_NAME === 'ITEMCONTABLE')) {
      log('\n' + '═'.repeat(80));
      log('4️⃣  ESTRUCTURA DE ITEMCONTABLE');
      log('═'.repeat(80));
      
      const itemContableSchema = await pool.request()
        .query(`
          SELECT 
            COLUMN_NAME,
            DATA_TYPE,
            IS_NULLABLE
          FROM INFORMATION_SCHEMA.COLUMNS
          WHERE TABLE_NAME = 'ITEMCONTABLE'
          ORDER BY ORDINAL_POSITION
        `);
      
      log(`\nTotal columnas: ${itemContableSchema.recordset.length}`);
      itemContableSchema.recordset.forEach(col => {
        const nullable = col.IS_NULLABLE === 'YES' ? '✓' : '✗';
        log(`  ${col.COLUMN_NAME.padEnd(30)} | ${col.DATA_TYPE.padEnd(20)} | Nullable: ${nullable}`);
      });
    }

    log('\n' + '═'.repeat(80));
    log('✅ Análisis completado - ' + new Date().toISOString());
    log('═'.repeat(80) + '\n');

    // Guardar en archivo
    const outputPath = path.join('.', 'calipso-structure-analysis.txt');
    await fs.writeFile(outputPath, output.join('\n'));
    console.log(`✅ Análisis guardado en: ${outputPath}`);

  } catch (error) {
    const errorMsg = `❌ Error: ${error.message}`;
    log(errorMsg);
    console.error(errorMsg);
  } finally {
    await pool.close();
  }
}

await explore();
