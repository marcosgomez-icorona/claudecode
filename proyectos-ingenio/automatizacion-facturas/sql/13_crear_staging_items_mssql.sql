/*
=============================================================================
  SCRIPT 13: Tabla UD_EZI_STAGING_ITEMS en SQL Server
  Base: CORONA | SQL Server 2008 R2
  Para ítems de facturas recibidos desde n8n
=============================================================================
*/

USE CORONA
GO

IF NOT EXISTS (SELECT 1 FROM sys.tables WHERE name = 'UD_EZI_STAGING_ITEMS')
BEGIN
    CREATE TABLE UD_EZI_STAGING_ITEMS (
        ID               int IDENTITY(1,1) PRIMARY KEY,
        FACTURA_ID       uniqueidentifier    NOT NULL,
        LINEA            int                 NOT NULL DEFAULT 1,
        DESCRIPCION      varchar(500)        NULL,
        CANTIDAD         decimal(15,4)       NULL,
        UNIDAD           varchar(50)         NULL DEFAULT 'u',
        PRECIO_UNITARIO  decimal(15,4)       NULL,
        ALICUOTA_IVA     int                 NULL,
        SUBTOTAL         decimal(15,2)       NULL,
        FECHA_CARGA      datetime            NOT NULL DEFAULT GETDATE()
    )

    CREATE INDEX IX_STAGING_ITEMS_FACTURA ON UD_EZI_STAGING_ITEMS (FACTURA_ID)

    PRINT 'OK: Tabla UD_EZI_STAGING_ITEMS creada.'
END
ELSE
    PRINT 'INFO: Tabla UD_EZI_STAGING_ITEMS ya existe.'
GO

-- Verificación
SELECT
    COLUMN_NAME,
    DATA_TYPE,
    CHARACTER_MAXIMUM_LENGTH
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'UD_EZI_STAGING_ITEMS'
ORDER BY ORDINAL_POSITION

PRINT '========================================'
PRINT 'Script 13 completado.'
PRINT '========================================'
