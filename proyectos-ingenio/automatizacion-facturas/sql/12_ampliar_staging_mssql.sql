/*
=============================================================================
  SCRIPT 12: Ampliar UD_EZI_STAGING_FACTURAS para recepción desde n8n
  Base: CORONA | SQL Server 2008 R2
  Columnas adicionales que el flujo n8n necesita
=============================================================================
*/

USE CORONA
GO

-- Columna para items en formato JSON
IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE name = 'ITEMS_JSON' AND object_id = OBJECT_ID('UD_EZI_STAGING_FACTURAS'))
BEGIN
    ALTER TABLE UD_EZI_STAGING_FACTURAS ADD ITEMS_JSON varchar(4000) NULL
    PRINT 'OK: Columna ITEMS_JSON agregada.'
END
ELSE
    PRINT 'INFO: Columna ITEMS_JSON ya existe.'

-- Flag para facturas en dólares
IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE name = 'ES_DOLAR' AND object_id = OBJECT_ID('UD_EZI_STAGING_FACTURAS'))
BEGIN
    ALTER TABLE UD_EZI_STAGING_FACTURAS ADD ES_DOLAR int NULL DEFAULT 0
    PRINT 'OK: Columna ES_DOLAR agregada.'
END
ELSE
    PRINT 'INFO: Columna ES_DOLAR ya existe.'

-- Origen de la cotización (FACTURA, BNA, PESOS)
IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE name = 'COTIZACION_ORIGEN' AND object_id = OBJECT_ID('UD_EZI_STAGING_FACTURAS'))
BEGIN
    ALTER TABLE UD_EZI_STAGING_FACTURAS ADD COTIZACION_ORIGEN varchar(20) NULL
    PRINT 'OK: Columna COTIZACION_ORIGEN agregada.'
END
ELSE
    PRINT 'INFO: Columna COTIZACION_ORIGEN ya existe.'

-- Aviso de cotización (texto explicativo para el operador)
IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE name = 'COTIZACION_AVISO' AND object_id = OBJECT_ID('UD_EZI_STAGING_FACTURAS'))
BEGIN
    ALTER TABLE UD_EZI_STAGING_FACTURAS ADD COTIZACION_AVISO varchar(500) NULL
    PRINT 'OK: Columna COTIZACION_AVISO agregada.'
END
ELSE
    PRINT 'INFO: Columna COTIZACION_AVISO ya existe.'

-- Número de remito
IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE name = 'NRO_REMITO' AND object_id = OBJECT_ID('UD_EZI_STAGING_FACTURAS'))
BEGIN
    ALTER TABLE UD_EZI_STAGING_FACTURAS ADD NRO_REMITO varchar(50) NULL
    PRINT 'OK: Columna NRO_REMITO agregada.'
END
ELSE
    PRINT 'INFO: Columna NRO_REMITO ya existe.'

-- Confianza del parseo (score numérico 0-100)
IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE name = 'CONFIANZA_PARSEO' AND object_id = OBJECT_ID('UD_EZI_STAGING_FACTURAS'))
BEGIN
    ALTER TABLE UD_EZI_STAGING_FACTURAS ADD CONFIANZA_PARSEO int NULL DEFAULT 70
    PRINT 'OK: Columna CONFIANZA_PARSEO agregada.'
END
ELSE
    PRINT 'INFO: Columna CONFIANZA_PARSEO ya existe.'

-- Notas del parseo OpenAI
IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE name = 'NOTAS_PARSEO' AND object_id = OBJECT_ID('UD_EZI_STAGING_FACTURAS'))
BEGIN
    ALTER TABLE UD_EZI_STAGING_FACTURAS ADD NOTAS_PARSEO varchar(500) NULL
    PRINT 'OK: Columna NOTAS_PARSEO agregada.'
END
ELSE
    PRINT 'INFO: Columna NOTAS_PARSEO ya existe.'

GO

-- ===========================================================================
-- VERIFICACION FINAL
-- ===========================================================================
SELECT
    COLUMN_NAME,
    DATA_TYPE,
    CHARACTER_MAXIMUM_LENGTH
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'UD_EZI_STAGING_FACTURAS'
ORDER BY ORDINAL_POSITION

PRINT '========================================'
PRINT 'Script 12 completado.'
PRINT '========================================'
