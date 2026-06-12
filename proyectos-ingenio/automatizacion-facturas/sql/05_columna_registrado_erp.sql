-- ============================================================================
--  SCRIPT 05: Agregar control de registración en ERP Calipso
--  Ejecutar en MySQL sobre db_automatizaciones
-- ============================================================================

USE db_automatizaciones;

ALTER TABLE staging_facturas
    ADD COLUMN registrado_erp      TINYINT(1)  NOT NULL DEFAULT 0
        COMMENT '0=pendiente de enviar a Calipso, 1=registrado en ERP'
        AFTER estado_proceso,
    ADD COLUMN fecha_registro_erp  DATETIME    NULL DEFAULT NULL
        COMMENT 'Fecha en que se registró en Calipso'
        AFTER registrado_erp;

-- Verificación
SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'db_automatizaciones'
  AND TABLE_NAME   = 'staging_facturas'
  AND COLUMN_NAME  IN ('registrado_erp','fecha_registro_erp');
