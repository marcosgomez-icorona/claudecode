/*
=============================================================================
  SCRIPT 08 — Columnas para cotización USD y moneda de factura
  Base: db_automatizaciones (MySQL) — tabla staging_facturas
  Ejecutar en phpMyAdmin conectado al servidor 192.168.0.23
=============================================================================
*/

USE db_automatizaciones;

-- Indica si la factura/OC es en dólares (OC en USD o factura nominada en USD)
ALTER TABLE staging_facturas
    ADD COLUMN IF NOT EXISTS es_dolar TINYINT(1) NOT NULL DEFAULT 0
        COMMENT '1 = factura o la OC vinculada está en dólares';

-- Origen de la cotización USD usada
ALTER TABLE staging_facturas
    ADD COLUMN IF NOT EXISTS cotizacion_origen VARCHAR(20) NULL
        COMMENT 'FACTURA | BNA | PESOS — de dónde se obtuvo el tipo de cambio';

-- Aviso cuando la cotización vino del BNA (no de la factura)
ALTER TABLE staging_facturas
    ADD COLUMN IF NOT EXISTS cotizacion_aviso VARCHAR(300) NULL
        COMMENT 'Mensaje de aviso cuando el TC proviene del BNA y no de la factura';

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
  AND COLUMN_NAME IN ('es_dolar', 'cotizacion_origen', 'cotizacion_aviso')
ORDER BY ORDINAL_POSITION;
