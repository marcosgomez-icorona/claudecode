-- ============================================================================
-- 03_test_sp.sql
-- Pruebas del SP pr_ezi_vincular_factura
--
-- ⚠ SOLO TEST — No ejecutar en producción sin revisar.
-- Usa un remito REAL que esté sin factura para la prueba positiva.
-- Descomentar la prueba positiva SOLO si se quiere testear con datos reales.
-- ============================================================================

-- 1. Ver estructura de la tabla de auditoría
SELECT '=== Estructura pr_ezi_audit_factura ===' AS info;
EXEC sp_help 'pr_ezi_audit_factura';
GO

-- 2. Ver estructura del SP
SELECT '=== Código del SP ===' AS info;
EXEC sp_helptext 'pr_ezi_vincular_factura';
GO

-- 3. Prueba negativa: remito vacío
DECLARE @success BIT, @mensaje NVARCHAR(200), @audit_id INT;
EXEC dbo.pr_ezi_vincular_factura
    @remito = '',
    @factura = '000100001500',
    @usuario = 'TEST',
    @success = @success OUTPUT,
    @mensaje = @mensaje OUTPUT,
    @audit_id = @audit_id OUTPUT;
SELECT 'Prueba 3 — Remito vacío' AS test, @success AS success, @mensaje AS mensaje, @audit_id AS audit_id;
GO

-- 4. Prueba negativa: factura vacía
DECLARE @success BIT, @mensaje NVARCHAR(200), @audit_id INT;
EXEC dbo.pr_ezi_vincular_factura
    @remito = '0008-00005615',
    @factura = '',
    @usuario = 'TEST',
    @success = @success OUTPUT,
    @mensaje = @mensaje OUTPUT,
    @audit_id = @audit_id OUTPUT;
SELECT 'Prueba 4 — Factura vacía' AS test, @success AS success, @mensaje AS mensaje, @audit_id AS audit_id;
GO

-- 5. Prueba negativa: remito inexistente
DECLARE @success BIT, @mensaje NVARCHAR(200), @audit_id INT;
EXEC dbo.pr_ezi_vincular_factura
    @remito = '9999-99999999',
    @factura = '000100001500',
    @usuario = 'TEST',
    @success = @success OUTPUT,
    @mensaje = @mensaje OUTPUT,
    @audit_id = @audit_id OUTPUT;
SELECT 'Prueba 5 — Remito inexistente' AS test, @success AS success, @mensaje AS mensaje, @audit_id AS audit_id;
GO

-- 6. Ver registros de auditoría generados por las pruebas
SELECT '=== Auditoría generada ===' AS info;
SELECT id, run_uuid, remito, factura, usuario, resultado, mensaje, creado
FROM dbo.pr_ezi_audit_factura
ORDER BY id DESC;
GO

-- ============================================================================
-- ⚠ PRUEBA POSITIVA — Descomentar SOLO para test controlado
--    Requiere reemplazar 'REMITO_REAL_SIN_FACTURA' por un remito existente.
-- ============================================================================
/*
DECLARE @success BIT, @mensaje NVARCHAR(200), @audit_id INT;
EXEC dbo.pr_ezi_vincular_factura
    @remito = 'REMITO_REAL_SIN_FACTURA',
    @factura = 'TEST-FACT-001',
    @usuario = 'TEST',
    @run_uuid = 'TEST-1234-5678',
    @success = @success OUTPUT,
    @mensaje = @mensaje OUTPUT,
    @audit_id = @audit_id OUTPUT;
SELECT 'Prueba positiva' AS test, @success AS success, @mensaje AS mensaje, @audit_id AS audit_id;

-- Verificar que el UPDATE se aplicó
SELECT remito, factura FROM pr_ezi_remitos WHERE remito = 'REMITO_REAL_SIN_FACTURA';

-- ⚠ LIMPIEZA: revertir el cambio de prueba
-- UPDATE pr_ezi_remitos SET factura = NULL WHERE remito = 'REMITO_REAL_SIN_FACTURA' AND factura = 'TEST-FACT-001';
-- DELETE FROM pr_ezi_audit_factura WHERE remito = 'REMITO_REAL_SIN_FACTURA' AND factura = 'TEST-FACT-001';
*/
