-- ============================================================================
--  SCRIPT 03: Crear / reconfigurar usuario MySQL para el proyecto
--  Ejecutar como root en el servidor MySQL (192.168.0.23)
--  Base objetivo: db_automatizaciones
-- ============================================================================
--
--  PERMISOS OTORGADOS (mínimos necesarios para el proyecto):
--
--  Proceso          | Nodo                        | Operación
--  -----------------|-----------------------------|-----------------------------
--  n8n Entrada      | INSERT MySQL                | INSERT
--  n8n Aprobación   | Obtener registro / UPDATE   | SELECT, UPDATE
--  n8n Test         | (no toca MySQL directamente)| —
--  Node-RED API     | GET pendientes / GET by id  | SELECT
--  Node-RED API     | PUT editar / POST aprobar   | UPDATE
--  Node-RED API     | POST rechazar               | UPDATE
--  Administración   | DELETE registro de prueba   | DELETE
--
-- ============================================================================

-- 1. Eliminar usuario si ya existe (para recrear con permisos limpios)
DROP USER IF EXISTS 'usr_automatizacion'@'localhost';
DROP USER IF EXISTS 'usr_automatizacion'@'%';

-- 2. Crear usuario con acceso local y remoto
CREATE USER 'usr_automatizacion'@'localhost' IDENTIFIED BY 'Corona1234$';
CREATE USER 'usr_automatizacion'@'%'          IDENTIFIED BY 'Corona1234$';

-- 3. Permisos específicos sobre staging_facturas
--    SELECT  → lectura de registros pendientes / aprobados
--    INSERT  → carga de nuevas facturas desde n8n
--    UPDATE  → cambio de estado (PENDIENTE → APROBADO / RECHAZADO / ERROR)
--    DELETE  → limpieza de registros de prueba (solo administración)
GRANT SELECT, INSERT, UPDATE, DELETE
    ON db_automatizaciones.staging_facturas
    TO 'usr_automatizacion'@'localhost';

GRANT SELECT, INSERT, UPDATE, DELETE
    ON db_automatizaciones.staging_facturas
    TO 'usr_automatizacion'@'%';

-- 4. Permiso de visualización de tablas del esquema
--    Necesario para que n8n pueda verificar la conexión correctamente
GRANT SELECT
    ON information_schema.*
    TO 'usr_automatizacion'@'localhost';

GRANT SELECT
    ON information_schema.*
    TO 'usr_automatizacion'@'%';

FLUSH PRIVILEGES;

-- ============================================================================
--  VERIFICACIÓN
-- ============================================================================

-- Usuarios creados
SELECT user, host, password_expired
FROM mysql.user
WHERE user = 'usr_automatizacion';
-- Debe devolver 2 filas: localhost y %

-- Permisos otorgados
SHOW GRANTS FOR 'usr_automatizacion'@'localhost';
SHOW GRANTS FOR 'usr_automatizacion'@'%';
-- Debe mostrar: GRANT SELECT, INSERT, UPDATE, DELETE ON db_automatizaciones.staging_facturas

-- Test de acceso (ejecutar como usr_automatizacion para confirmar)
-- SELECT COUNT(*) FROM db_automatizaciones.staging_facturas;
