-- ============================================================================
-- 01_crear_audit_factura.sql (MySQL)
-- Despachos Pendientes de Facturación — Tabla de auditoría en MySQL
--
-- REGLA: TODA tabla auxiliar va en MySQL. SQL Server solo para ERP transaccional.
-- Esta tabla registra cada vinculación remito↔factura con trazabilidad completa.
--
-- Ejecutar en: MySQL db_corona (127.0.0.1:3306)
-- ============================================================================

CREATE TABLE IF NOT EXISTS `despachos_audit_factura` (
    `id`              INT           AUTO_INCREMENT PRIMARY KEY,
    `run_uuid`        VARCHAR(36)   NOT NULL COMMENT 'UUID de trazabilidad de la ejecución',
    `remito`          VARCHAR(50)   NOT NULL COMMENT 'N° de remito vinculado',
    `factura`         VARCHAR(150)  NOT NULL COMMENT 'N° de factura asignada',
    `factura_anterior` VARCHAR(150) NULL COMMENT 'Factura previa si existía',
    `usuario`         VARCHAR(50)   NOT NULL DEFAULT 'SISTEMA' COMMENT 'Quién ejecutó la acción',
    `aplicacion`      VARCHAR(50)   NOT NULL DEFAULT 'DespachosApp' COMMENT 'Origen de la acción',
    `accion`          VARCHAR(20)   NOT NULL DEFAULT 'VINCULAR' COMMENT 'Tipo de operación',
    `resultado`       TINYINT(1)    NOT NULL DEFAULT 0 COMMENT '1 = éxito, 0 = fallo',
    `mensaje`         VARCHAR(200)  NULL COMMENT 'Detalle del resultado',
    `sp_audit_id`     INT           NULL COMMENT 'ID de auditoría en SQL Server (si aplica)',
    `creado`          TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp de la operación',

    INDEX `idx_remito` (`remito`),
    INDEX `idx_factura` (`factura`),
    INDEX `idx_run_uuid` (`run_uuid`),
    INDEX `idx_creado` (`creado`),
    INDEX `idx_resultado` (`resultado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Auditoría de vinculaciones remito↔factura — Despachos Pendientes';

-- Verificación
SELECT '✓ Tabla despachos_audit_factura creada en MySQL' AS resultado;
SHOW CREATE TABLE despachos_audit_factura\G
