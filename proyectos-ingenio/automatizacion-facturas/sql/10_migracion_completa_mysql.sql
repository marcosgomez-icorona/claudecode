/*
=============================================================================
  SCRIPT 10: Migración completa MySQL — columnas pendientes
  Base: db_automatizaciones (MySQL)
  Ejecutar en phpMyAdmin o MySQL CLI en el servidor 192.168.0.23
  Orden seguro: ejecutar completo (IF NOT EXISTS en todas las columnas)
=============================================================================
*/

USE db_automatizaciones;

-- ===========================================================================
-- 1. Script 06 — Columna nro_remito
-- ===========================================================================
SELECT '06: nro_remito' AS resultado;

ALTER TABLE staging_facturas
    ADD COLUMN IF NOT EXISTS nro_remito  VARCHAR(30)  NULL DEFAULT NULL
        COMMENT 'Número de remito asociado a la factura'
        AFTER referencia;

-- ===========================================================================
-- 2. Script 07 — Columnas de constancia
-- ===========================================================================
SELECT '07: columnas constancia' AS resultado;

ALTER TABLE staging_facturas
    ADD COLUMN IF NOT EXISTS requiere_constancia TINYINT(1) NOT NULL DEFAULT 0
        COMMENT '1 = el operador activó validación de constancia para esta factura',
    ADD COLUMN IF NOT EXISTS constancia_nro VARCHAR(20) NULL
        COMMENT 'Número de ConstServ seleccionada (campo CONSTANCIA de Calipso)',
    ADD COLUMN IF NOT EXISTS constancia_id_calipso CHAR(36) NULL
        COMMENT 'ID (uniqueidentifier) del TR de ConstServ en Calipso',
    ADD COLUMN IF NOT EXISTS constancia_total DECIMAL(14,2) NULL
        COMMENT 'totalConstancia de la ConstServ seleccionada',
    ADD COLUMN IF NOT EXISTS constancia_fecha DATE NULL
        COMMENT 'Fecha de la ConstServ (YYYYMMDD → DATE)',
    ADD COLUMN IF NOT EXISTS constancia_detalle VARCHAR(300) NULL
        COMMENT 'Campo DETALLE de la constancia (número interno de Calipso)';

-- ===========================================================================
-- 3. Script 08 — Columnas para cotización USD
-- ===========================================================================
SELECT '08: columnas dolar' AS resultado;

ALTER TABLE staging_facturas
    ADD COLUMN IF NOT EXISTS es_dolar TINYINT(1) NOT NULL DEFAULT 0
        COMMENT '1 = factura o la OC vinculada está en dólares',
    ADD COLUMN IF NOT EXISTS cotizacion_origen VARCHAR(20) NULL
        COMMENT 'FACTURA | BNA | PESOS — de dónde se obtuvo el tipo de cambio',
    ADD COLUMN IF NOT EXISTS cotizacion_aviso VARCHAR(300) NULL
        COMMENT 'Mensaje de aviso cuando el TC proviene del BNA y no de la factura';

-- ===========================================================================
-- 4. Script 09 — Ampliar fechas a tipo DATE
-- ===========================================================================
SELECT '09: ampliar fechas a DATE' AS resultado;

-- Las columnas deben ser VARCHAR(8) actualmente. Las migramos a DATE.
-- Las filas con datos truncados ('2026-04-' etc.) quedan como NULL.
ALTER TABLE staging_facturas
    MODIFY COLUMN fecha_emision     DATE NULL,
    MODIFY COLUMN fecha_vencimiento DATE NULL,
    MODIFY COLUMN fecha_vto_cae     DATE NULL;

-- ===========================================================================
-- 5. Script 05 ya ejecutado — verificar registrado_erp
-- ===========================================================================
SELECT '05: verificar registrado_erp' AS resultado;

ALTER TABLE staging_facturas
    ADD COLUMN IF NOT EXISTS registrado_erp      TINYINT(1)  NOT NULL DEFAULT 0
        COMMENT '0=pendiente de enviar a Calipso, 1=registrado en ERP'
        AFTER estado_proceso,
    ADD COLUMN IF NOT EXISTS fecha_registro_erp  DATETIME    NULL DEFAULT NULL
        COMMENT 'Fecha en que se registró en Calipso'
        AFTER registrado_erp;

-- ===========================================================================
-- 6. Agregar columnas faltantes respecto al schema de SQL Server UD_EZI_STAGING
-- ===========================================================================
SELECT '10: columnas adicionales de staging' AS resultado;

-- Para sync a SQL Server al aprobar
ALTER TABLE staging_facturas
    ADD COLUMN IF NOT EXISTS compania_id CHAR(36) NULL
        COMMENT 'ID de compañía en Calipso'
        AFTER unidadoperativa_id,
    ADD COLUMN IF NOT EXISTS centrocostos_id CHAR(36) NULL
        COMMENT 'ID de centro de costos en Calipso',
    ADD COLUMN IF NOT EXISTS centrocostos_nombre VARCHAR(100) NULL
        COMMENT 'Nombre del centro de costos',
    ADD COLUMN IF NOT EXISTS moneda_nombre VARCHAR(20) NULL
        COMMENT 'Nombre de la moneda (Pesos/Dólares)';

-- ===========================================================================
-- 7. Crear tabla de staging para SQL Server sync (log de aprobaciones)
-- ===========================================================================
SELECT '10: tabla log_sync_calipso' AS resultado;

CREATE TABLE IF NOT EXISTS log_sync_calipso (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    factura_id          CHAR(36)        NOT NULL COMMENT 'FK a staging_facturas.id',
    tipo_operacion      VARCHAR(30)     NOT NULL DEFAULT 'INSERT',
    fecha_sync          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado              VARCHAR(20)     NOT NULL DEFAULT 'PENDIENTE',
        -- PENDIENTE | SINCRONIZADO | ERROR
    tr_generado_id      CHAR(36)        NULL COMMENT 'ID del TR en Calipso',
    error_detalle       TEXT            NULL,
    INDEX idx_factura_id (factura_id),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Log de sincronización de facturas aprobadas hacia Calipso';

-- ===========================================================================
-- 8. Verificación final
-- ===========================================================================
SELECT 'VERIFICACIÓN FINAL' AS resultado;

SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'db_automatizaciones'
  AND TABLE_NAME   = 'staging_facturas'
ORDER BY ORDINAL_POSITION;

SELECT 'OK: Migración MySQL completa' AS resultado;
