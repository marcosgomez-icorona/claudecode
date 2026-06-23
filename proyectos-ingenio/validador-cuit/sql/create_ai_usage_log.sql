-- ============================================================================
--  Script: create_ai_usage_log.sql
--  Proyecto: Validador de CUITs — Ingenio La Corona
--  Motor: MySQL (corona_aux / 127.0.0.1:3306)
--  Descripción: Tabla de registro de uso de APIs de IA, obligatoria para
--               todo módulo backend que consuma modelos cloud (DeepSeek,
--               OpenAI, Anthropic, etc.).
--
--  REGLA: Toda tabla auxiliar va en MySQL. SQL Server solo para ERP.
--  Ver skill: backend-ai-integration.md → sección "Log de uso en MySQL"
--
--  Ejemplo de uso desde PHP (ai_extractor.php):
--    INSERT INTO corona_aux.ai_usage_log
--        (endpoint, model, prompt_tokens, completion_tokens, duration_ms,
--         estimated_cost_usd, success, error_message, module)
--    VALUES
--        ('extraerCuitsConIA', 'deepseek-chat', 150, 30, 1200,
--         0.000029, 1, NULL, 'validador-cuit');
-- ============================================================================

-- Seleccionar base de datos auxiliar
USE corona_aux;

-- ============================================================================
--  TABLA: ai_usage_log
--  Descripción: Registro de cada llamada a APIs de IA para trazabilidad,
--               control de costos y auditoría de uso.
-- ============================================================================

CREATE TABLE IF NOT EXISTS `ai_usage_log` (
    `id`                BIGINT          NOT NULL AUTO_INCREMENT,
    `endpoint`          VARCHAR(255)    NOT NULL COMMENT 'Nombre del endpoint o función llamada (ej: extraerCuitsConIA)',
    `model`             VARCHAR(100)    NULL     COMMENT 'Modelo de IA utilizado (ej: deepseek-chat, gpt-4)',
    `prompt_tokens`     INT             NOT NULL DEFAULT 0 COMMENT 'Tokens de entrada (prompt)',
    `completion_tokens` INT             NOT NULL DEFAULT 0 COMMENT 'Tokens de salida (completion)',
    `duration_ms`       INT             NULL     COMMENT 'Duración de la llamada en milisegundos',
    `estimated_cost_usd` DECIMAL(10,6)  NOT NULL DEFAULT 0 COMMENT 'Costo estimado en USD',
    `success`           TINYINT(1)      NOT NULL DEFAULT 1 COMMENT '1 = llamada exitosa, 0 = error',
    `error_message`     TEXT            NULL     COMMENT 'Mensaje de error si success = 0',
    `module`            VARCHAR(100)    NULL     COMMENT 'Módulo que realizó la llamada (ej: validador-cuit)',
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Momento de la llamada',

    PRIMARY KEY (`id`),
    INDEX `idx_created`  (`created_at`),
    INDEX `idx_module`   (`module`),
    INDEX `idx_endpoint` (`endpoint`),
    INDEX `idx_success`  (`success`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Registro de uso de APIs de IA — trazabilidad, costos y auditoría';

-- ============================================================================
--  VERIFICACIÓN
-- ============================================================================

SELECT
    'corona_aux'        AS `base`,
    'ai_usage_log'      AS `tabla`,
    COUNT(*)            AS `columnas`
FROM `information_schema`.`COLUMNS`
WHERE `TABLE_SCHEMA` = 'corona_aux'
  AND `TABLE_NAME`   = 'ai_usage_log';

-- Debe devolver: columnas = 11

-- ============================================================================
--  NOTAS DE MANTENIMIENTO
-- ============================================================================
--  - Los registros son inmutables (solo INSERT, nunca UPDATE/DELETE).
--  - Retención sugerida: 12 meses. Los registros más antiguos pueden
--    archivarse o purgarse según política de la empresa.
--  - El costo estimado se calcula en la capa de aplicación:
--      DeepSeek deepseek-chat: $0.14/1M tokens input, $0.28/1M tokens output
--      DeepSeek deepseek-coder: $0.28/1M tokens input, $1.10/1M tokens output
--    (Precios a junio 2026 — verificar en https://api-docs.deepseek.com/quick_start/pricing)
-- ============================================================================
