-- ============================================================================
--  SCRIPT 02: Agregar soporte de ítems a staging_facturas
--  Ejecutar en MySQL sobre db_automatizaciones
-- ============================================================================

USE db_automatizaciones;

-- Agregar columna items_json a la tabla existente
ALTER TABLE staging_facturas
    ADD COLUMN items_json MEDIUMTEXT NULL
        COMMENT 'Items del PDF en JSON: [{linea,descripcion,cantidad,unidad,precio_unitario,alicuota_iva,subtotal,moneda}]'
    AFTER email_asunto;

-- Verificación
SELECT COLUMN_NAME, DATA_TYPE, COLUMN_COMMENT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'db_automatizaciones'
  AND TABLE_NAME   = 'staging_facturas'
  AND COLUMN_NAME  = 'items_json';
