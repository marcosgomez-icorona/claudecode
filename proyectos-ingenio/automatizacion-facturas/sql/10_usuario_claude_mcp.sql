-- ============================================================================
--  SCRIPT 10: Usuario dedicado para Claude Code MCP MySQL
--  Ejecutar como root en phpMyAdmin o consola MySQL en 192.168.0.23
--
--  Este usuario es usado por el MCP de Claude Code para consultar y operar
--  sobre las 3 bases de datos del sistema de automatización.
--
--  Permisos por base:
--    db_automatizaciones  → SELECT, INSERT, UPDATE, DELETE, DDL completo
--    db_corona            → SELECT (monitoreo molienda, lectura indicadores)
--    db_molienda          → SELECT (datos cañeros, lectura)
-- ============================================================================

-- 1. Eliminar si ya existe
DROP USER IF EXISTS 'usr_claude'@'%';

-- 2. Crear usuario (acceso desde cualquier host — necesario desde WSL)
CREATE USER 'usr_claude'@'%' IDENTIFIED BY 'ClaudeMCP2024$';

-- 3. db_automatizaciones — acceso completo (staging_facturas + migraciones DDL)
GRANT SELECT, INSERT, UPDATE, DELETE
    ON db_automatizaciones.*
    TO 'usr_claude'@'%';

-- DDL para ejecutar scripts de migración (ALTER TABLE, etc.)
GRANT CREATE, ALTER, INDEX, DROP
    ON db_automatizaciones.*
    TO 'usr_claude'@'%';

-- 4. db_corona — solo lectura (indicadores proceso, molienda, laboratorio)
GRANT SELECT
    ON db_corona.*
    TO 'usr_claude'@'%';

-- 5. db_molienda — solo lectura (cañeros, órdenes, proveedores)
GRANT SELECT
    ON db_molienda.*
    TO 'usr_claude'@'%';

-- 6. information_schema — para introspección de esquemas (necesario para el MCP)
GRANT SELECT
    ON information_schema.*
    TO 'usr_claude'@'%';

FLUSH PRIVILEGES;

-- ============================================================================
--  VERIFICACIÓN
-- ============================================================================
SELECT user, host FROM mysql.user WHERE user = 'usr_claude';
SHOW GRANTS FOR 'usr_claude'@'%';
