-- ============================================================================
--  SCRIPT 06: Agregar columna nro_remito en staging_facturas
--  Ejecutar en MySQL sobre db_automatizaciones
-- ============================================================================

USE db_automatizaciones;

ALTER TABLE staging_facturas
    ADD COLUMN IF NOT EXISTS nro_remito  VARCHAR(30)  NULL DEFAULT NULL
        COMMENT 'Número de remito asociado a la factura'
        AFTER referencia;

-- Verificación
SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'db_automatizaciones'
  AND TABLE_NAME   = 'staging_facturas'
  AND COLUMN_NAME  IN ('referencia','nro_remito');
