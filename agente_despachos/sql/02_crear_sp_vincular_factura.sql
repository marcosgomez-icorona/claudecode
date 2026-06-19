-- ============================================================================
-- 02_crear_sp_vincular_factura.sql
-- Despachos Pendientes de Facturación — Stored Procedure de vinculación
--
-- Objetivo: Middleware ERP para vincular remito↔factura con validación,
--           registro de auditoría y control transaccional.
--
-- Reglas:
--   1. Solo UPDATE si factura IS NULL — no sobrescribe facturas existentes
--   2. Registra auditoría antes y después (factura_anterior)
--   3. Transacción atómica: UPDATE + INSERT auditoría
--   4. Compatible SQL Server 2008 R2
--
-- Ejecutar en: CORONA
-- Orden: 01 → 02 → 03
-- ============================================================================

IF OBJECT_ID('dbo.pr_ezi_vincular_factura', 'P') IS NOT NULL
    DROP PROCEDURE dbo.pr_ezi_vincular_factura;
GO

CREATE PROCEDURE dbo.pr_ezi_vincular_factura
    @remito     NVARCHAR(50),           -- N° de remito (obligatorio)
    @factura    NVARCHAR(150),          -- N° de factura a vincular (obligatorio)
    @usuario    NVARCHAR(50)  = 'SISTEMA',  -- Usuario que ejecuta la acción
    @aplicacion NVARCHAR(50)  = 'DespachosApp', -- Aplicación origen
    @run_uuid   NVARCHAR(36)  = NULL,   -- UUID de trazabilidad (opcional, se genera si es NULL)
    @success    BIT            OUTPUT,  -- 1 = éxito, 0 = fallo
    @mensaje    NVARCHAR(200)  OUTPUT,  -- Detalle del resultado
    @audit_id   INT            OUTPUT   -- ID del registro de auditoría creado
WITH EXECUTE AS CALLER
AS
BEGIN
    SET NOCOUNT ON;

    -- Inicializar outputs
    SET @success = 0;
    SET @mensaje = N'';
    SET @audit_id = 0;

    -- Validar parámetros obligatorios
    IF @remito IS NULL OR LTRIM(RTRIM(@remito)) = ''
    BEGIN
        SET @mensaje = N'Remito es obligatorio.';
        RETURN;
    END

    IF @factura IS NULL OR LTRIM(RTRIM(@factura)) = ''
    BEGIN
        SET @mensaje = N'Factura es obligatorio.';
        RETURN;
    END

    -- Limpiar espacios
    SET @remito = LTRIM(RTRIM(@remito));
    SET @factura = LTRIM(RTRIM(@factura));

    -- Generar UUID si no vino
    IF @run_uuid IS NULL
        SET @run_uuid = NEWID();

    DECLARE @factura_anterior NVARCHAR(150);
    DECLARE @remito_existe     BIT = 0;
    DECLARE @rows_updated      INT = 0;

    -- Verificar que el remito existe
    IF EXISTS (SELECT 1 FROM dbo.pr_ezi_remitos WHERE remito = @remito)
        SET @remito_existe = 1;

    IF @remito_existe = 0
    BEGIN
        SET @mensaje = N'Remito ' + @remito + N' no encontrado.';
        GOTO REGISTRAR_AUDITORIA;
    END

    -- Leer factura actual (para auditoría)
    SELECT @factura_anterior = factura
    FROM dbo.pr_ezi_remitos
    WHERE remito = @remito;

    -- Validar que no tenga ya una factura asignada
    IF @factura_anterior IS NOT NULL AND LTRIM(RTRIM(@factura_anterior)) != ''
    BEGIN
        SET @mensaje = N'Remito ' + @remito + N' ya tiene factura ' + @factura_anterior + N'.';
        GOTO REGISTRAR_AUDITORIA;
    END

    -- ========================================================================
    -- TRANSACCIÓN: UPDATE + auditoría
    -- ========================================================================
    BEGIN TRY
        BEGIN TRANSACTION;

            -- UPDATE condicional: solo si factura sigue NULL
            UPDATE dbo.pr_ezi_remitos
            SET factura = @factura
            WHERE remito = @remito
              AND (factura IS NULL OR LTRIM(RTRIM(factura)) = '');

            SET @rows_updated = @@ROWCOUNT;

            IF @rows_updated = 0
            BEGIN
                -- Alguien más asignó factura entre el SELECT y el UPDATE
                SET @mensaje = N'El remito fue facturado por otra operación concurrente.';
                ROLLBACK TRANSACTION;
                GOTO REGISTRAR_AUDITORIA;
            END

            -- Éxito
            SET @success = 1;
            SET @mensaje = N'Remito ' + @remito + N' vinculado con factura ' + @factura + N'.';

            -- Insertar auditoría (dentro de la transacción)
            INSERT INTO dbo.pr_ezi_audit_factura (
                run_uuid, remito, factura, factura_anterior,
                usuario, aplicacion, accion,
                resultado, mensaje, rows_affected
            ) VALUES (
                @run_uuid, @remito, @factura, @factura_anterior,
                ISNULL(@usuario, 'SISTEMA'), ISNULL(@aplicacion, 'DespachosApp'), 'VINCULAR',
                1, @mensaje, @rows_updated
            );

            SET @audit_id = SCOPE_IDENTITY();

        COMMIT TRANSACTION;
        RETURN;

    END TRY
    BEGIN CATCH
        IF @@TRANCOUNT > 0
            ROLLBACK TRANSACTION;

        SET @success = 0;
        SET @mensaje = N'Error: ' + CAST(ERROR_NUMBER() AS NVARCHAR(10))
                     + N' - ' + ERROR_MESSAGE();
    END CATCH

    -- ========================================================================
    -- REGISTRAR_AUDITORIA: punto común para fallos (fuera de transacción)
    -- ========================================================================
    REGISTRAR_AUDITORIA:
    BEGIN TRY
        INSERT INTO dbo.pr_ezi_audit_factura (
            run_uuid, remito, factura, factura_anterior,
            usuario, aplicacion, accion,
            resultado, mensaje, rows_affected
        ) VALUES (
            @run_uuid, @remito, @factura, @factura_anterior,
            ISNULL(@usuario, 'SISTEMA'), ISNULL(@aplicacion, 'DespachosApp'), 'VINCULAR',
            0, @mensaje, @rows_updated
        );

        SET @audit_id = SCOPE_IDENTITY();
    END TRY
    BEGIN CATCH
        -- Si falla la auditoría, al menos registrar en output
        SET @mensaje = @mensaje + N' (auditoría falló: ' + ERROR_MESSAGE() + N')';
    END CATCH
END
GO

-- Verificar creación
IF OBJECT_ID('dbo.pr_ezi_vincular_factura', 'P') IS NOT NULL
    PRINT '✓ SP pr_ezi_vincular_factura creado correctamente.';
ELSE
    PRINT '✗ ERROR: No se pudo crear el SP.';
GO
