-- ============================================================================
-- 01_crear_audit_factura.sql
-- Despachos Pendientes de Facturación — Tabla de auditoría
--
-- Objetivo: Registrar toda vinculación remito↔factura con trazabilidad completa.
-- Cada UPDATE en pr_ezi_remitos.factura debe tener su registro acá.
--
-- Ejecutar en: CORONA (SQL Server 2008 R2)
-- Orden: 01 → 02 → 03
-- ============================================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES
               WHERE TABLE_SCHEMA = 'dbo' AND TABLE_NAME = 'pr_ezi_audit_factura')
BEGIN
    CREATE TABLE dbo.pr_ezi_audit_factura (
        id              INT IDENTITY(1,1) PRIMARY KEY,
        run_uuid        NVARCHAR(36)   NOT NULL,          -- UUID de ejecución
        remito          NVARCHAR(50)   NOT NULL,          -- N° de remito
        factura         NVARCHAR(150)  NOT NULL,          -- N° de factura vinculada
        factura_anterior NVARCHAR(150) NULL,              -- Factura previa (si existía)
        usuario         NVARCHAR(50)   NOT NULL,          -- Quién ejecutó
        aplicacion      NVARCHAR(50)   DEFAULT 'DespachosApp', -- Origen
        accion          NVARCHAR(20)   DEFAULT 'VINCULAR',-- Tipo de operación
        resultado       BIT            NOT NULL,          -- 1 = éxito, 0 = fallo
        mensaje         NVARCHAR(200)  NULL,              -- Detalle del resultado
        rows_affected   INT            DEFAULT 0,         -- Filas afectadas
        creado          DATETIME       DEFAULT GETDATE()  -- Timestamp
    );

    PRINT '✓ Tabla pr_ezi_audit_factura creada.';
END
ELSE
BEGIN
    PRINT '→ Tabla pr_ezi_audit_factura ya existe.';
END
GO
