-- ============================================================================
-- 02_crear_sync_sheets.sql (MySQL)
-- Despachos Pendientes — Tablas para sincronización con Google Sheets
--
-- REGLA: MySQL como staging/sync/caché. Nunca en SQL Server.
-- ============================================================================

-- Estado de la última sincronización a Google Sheets
CREATE TABLE IF NOT EXISTS `despachos_sync_state` (
    `id`              INT           AUTO_INCREMENT PRIMARY KEY,
    `run_uuid`        VARCHAR(36)   NOT NULL COMMENT 'UUID de la ejecución de sync',
    `sheet_name`      VARCHAR(100)  NOT NULL COMMENT 'Nombre de la hoja en Google Sheets',
    `rows_synced`     INT           NOT NULL DEFAULT 0 COMMENT 'Filas sincronizadas',
    `status`          VARCHAR(20)   NOT NULL DEFAULT 'OK' COMMENT 'OK / ERROR / PARTIAL',
    `error_message`   TEXT          NULL COMMENT 'Mensaje de error si falló',
    `started_at`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `completed_at`    TIMESTAMP     NULL,

    INDEX `idx_sheet_name` (`sheet_name`),
    INDEX `idx_started_at` (`started_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Estado de sincronización a Google Sheets';

-- Caché local de los datos enviados a Sheets (espejo para consultas rápidas)
CREATE TABLE IF NOT EXISTS `despachos_pendientes_cache` (
    `id`              INT           AUTO_INCREMENT PRIMARY KEY,
    `remito`          VARCHAR(50)   NOT NULL,
    `fecha`           DATETIME      NULL,
    `cliente`         VARCHAR(150)  NULL,
    `cuit`            VARCHAR(20)   NULL,
    `producto`        VARCHAR(100)  NULL,
    `cantidad`        DECIMAL(12,2) NULL,
    `unidad`          VARCHAR(50)   NULL,
    `precio`          DECIMAL(12,2) NULL,
    `total`           DECIMAL(14,2) NULL,
    `transportista`   VARCHAR(150)  NULL,
    `clasificacion`   VARCHAR(50)   NULL,
    `sync_run_uuid`   VARCHAR(36)   NULL COMMENT 'UUID del sync que insertó esta fila',
    `synced_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY `uk_remito_producto` (`remito`, `producto`),
    INDEX `idx_fecha` (`fecha`),
    INDEX `idx_cliente` (`cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Caché local de despachos pendientes (espejo de SQL Server → Sheets)';

SELECT '✓ Tablas de sync creadas en MySQL' AS resultado;
