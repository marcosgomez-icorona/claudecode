/*
=============================================================================
  SCRIPT 11: Crear tabla UD_EZI_STAGING_FACTURAS en SQL Server CORONA
  Base: CORONA | SQL Server 2008 R2
  Esta tabla recibe los datos cuando el operador APRUEBA una factura,
  sync desde MySQL staging_facturas
  Ejecutar en SSMS conectado a: serverico\CORONA
=============================================================================
*/

USE CORONA;
GO

IF NOT EXISTS (SELECT 1 FROM sys.tables WHERE name = 'UD_EZI_STAGING_FACTURAS')
BEGIN
    CREATE TABLE UD_EZI_STAGING_FACTURAS (
        -- CONTROL Y TRAZABILIDAD
        ID                  uniqueidentifier    NOT NULL
                            CONSTRAINT PK_UD_EZI_STAGING_FACTURAS PRIMARY KEY,
        TIPO_OPERACION      varchar(30)         NOT NULL,
        ESTADO_PROCESO      varchar(20)         NOT NULL    DEFAULT 'PENDIENTE',
        FECHA_CARGA         varchar(17)         NOT NULL,
        FECHA_APROBACION    varchar(17)         NULL,
        FECHA_PROCESO       varchar(17)         NULL,
        USUARIO_CARGA       varchar(30)         NOT NULL,
        APROBADO_POR        varchar(30)         NULL,
        PROCESADO_POR       varchar(30)         NULL,
        ORIGEN              varchar(50)         NULL,
        ERROR_DETALLE       varchar(500)        NULL,
        TR_GENERADO_ID      uniqueidentifier    NULL,
        LOG_PROCESO         varchar(2000)       NULL,

        -- COMPROBANTE
        TIPOTRANSACCION_ID  uniqueidentifier    NOT NULL,
        NUMERODOCUMENTO     varchar(20)         NOT NULL,
        LETRA               varchar(1)          NOT NULL,
        FECHA_EMISION       varchar(8)          NOT NULL,
        FECHA_VENCIMIENTO   varchar(8)          NULL,
        REFERENCIA          varchar(50)         NULL,

        -- PROVEEDOR
        PROVEEDOR_ID        uniqueidentifier    NOT NULL,
        PROVEEDOR_CODIGO    varchar(15)         NOT NULL,
        PROVEEDOR_CUIT      varchar(40)         NOT NULL,
        PROVEEDOR_NOMBRE    varchar(100)        NOT NULL,

        -- IMPORTES
        NETO                decimal(22,10)      NOT NULL    DEFAULT 0,
        IVA_21              decimal(22,10)      NULL        DEFAULT 0,
        IVA_105             decimal(22,10)      NULL        DEFAULT 0,
        PERCEPCIONES        decimal(22,10)      NULL        DEFAULT 0,
        OTROS_IMPUESTOS     decimal(22,10)      NULL        DEFAULT 0,
        TOTAL               decimal(22,10)      NOT NULL,
        MONEDA_ID           uniqueidentifier    NOT NULL,
        COTIZACION          decimal(22,10)      NOT NULL    DEFAULT 1,

        -- FISCAL AFIP
        CAE                 varchar(20)         NULL,
        FECHA_VTO_CAE       varchar(8)          NULL,

        -- IMPUTACION
        CENTROCOSTOS_ID     uniqueidentifier    NULL,
        CENTROCOSTOS_NOMBRE varchar(100)        NULL,
        COMPANIA_ID         uniqueidentifier    NOT NULL
                            DEFAULT 'FC20C32D-3EFA-11D5-86AD-0080AD403F5F',
        UNIDADOPERATIVA_ID  uniqueidentifier    NULL,

        -- METADATOS PDF
        PDF_FILENAME        varchar(200)        NULL,
        PDF_HASH            varchar(64)         NULL,
        EMAIL_ORIGEN        varchar(200)        NULL,
        EMAIL_ASUNTO        varchar(300)        NULL,

        -- CAMPOS ADICIONALES (sync desde n8n/MySQL)
        ITEMS_JSON          varchar(4000)       NULL,
        ES_DOLAR            int                 NULL    DEFAULT 0,
        COTIZACION_ORIGEN   varchar(20)         NULL,
        COTIZACION_AVISO    varchar(500)        NULL,
        NRO_REMITO          varchar(50)         NULL,
        CONFIANZA_PARSEO    int                 NULL    DEFAULT 70,
        NOTAS_PARSEO        varchar(500)        NULL
    );

    PRINT 'OK: Tabla UD_EZI_STAGING_FACTURAS creada.';
END
ELSE
    PRINT 'INFO: Tabla UD_EZI_STAGING_FACTURAS ya existe.';
GO

-- Tabla de items
IF NOT EXISTS (SELECT 1 FROM sys.tables WHERE name = 'UD_EZI_STAGING_ITEMS')
BEGIN
    CREATE TABLE UD_EZI_STAGING_ITEMS (
        ID                  int IDENTITY(1,1)   PRIMARY KEY,
        FACTURA_ID          uniqueidentifier    NOT NULL,
        LINEA               int                 NOT NULL,
        DESCRIPCION         varchar(500)        NULL,
        CANTIDAD            decimal(15,4)       NULL,
        UNIDAD              varchar(50)         NULL,
        PRECIO_UNITARIO     decimal(15,4)       NULL,
        ALICUOTA_IVA        int                 NULL,
        SUBTOTAL            decimal(15,2)       NULL,
        INDEX IX_FACTURA_ID (FACTURA_ID)
    );

    PRINT 'OK: Tabla UD_EZI_STAGING_ITEMS creada.';
END
ELSE
    PRINT 'INFO: Tabla UD_EZI_STAGING_ITEMS ya existe.';
GO

PRINT '========================================';
PRINT 'Script 11 completado.';
PRINT '========================================';
