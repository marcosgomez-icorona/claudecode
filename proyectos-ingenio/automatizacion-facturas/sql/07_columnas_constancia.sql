/*
=============================================================================
  SCRIPT 07 — Columnas de Certificación de Servicio
  Base: db_automatizaciones (MySQL) — tabla staging_facturas
  Ejecutar en phpMyAdmin o MySQL CLI conectado al servidor 192.168.0.23
=============================================================================
*/

USE db_automatizaciones;

-- Campo check: el usuario indica si esta OC requiere validación de constancia
ALTER TABLE staging_facturas
    ADD COLUMN IF NOT EXISTS requiere_constancia TINYINT(1) NOT NULL DEFAULT 0
        COMMENT '1 = el operador activó validación de constancia para esta factura';

-- Nro de la constancia seleccionada (ej: 00010646)
ALTER TABLE staging_facturas
    ADD COLUMN IF NOT EXISTS constancia_nro VARCHAR(20) NULL
        COMMENT 'Número de ConstServ seleccionada (campo CONSTANCIA de Calipso)';

-- GUID del TR de la constancia en Calipso (para registración en producción)
ALTER TABLE staging_facturas
    ADD COLUMN IF NOT EXISTS constancia_id_calipso CHAR(36) NULL
        COMMENT 'ID (uniqueidentifier) del TR de ConstServ en Calipso';

-- Total de la constancia certificada
ALTER TABLE staging_facturas
    ADD COLUMN IF NOT EXISTS constancia_total DECIMAL(14,2) NULL
        COMMENT 'totalConstancia de la ConstServ seleccionada';

-- Fecha de la constancia (extraída del campo FECHAACTUAL de Calipso)
ALTER TABLE staging_facturas
    ADD COLUMN IF NOT EXISTS constancia_fecha DATE NULL
        COMMENT 'Fecha de la ConstServ (YYYYMMDD → DATE)';

-- Detalle / número interno de la constancia en Calipso
ALTER TABLE staging_facturas
    ADD COLUMN IF NOT EXISTS constancia_detalle VARCHAR(300) NULL
        COMMENT 'Campo DETALLE de la constancia (número interno de Calipso)';

-- Verificación
SELECT
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'db_automatizaciones'
  AND TABLE_NAME   = 'staging_facturas'
  AND COLUMN_NAME IN (
      'requiere_constancia',
      'constancia_nro',
      'constancia_id_calipso',
      'constancia_total',
      'constancia_fecha',
      'constancia_detalle'
  )
ORDER BY ORDINAL_POSITION;
