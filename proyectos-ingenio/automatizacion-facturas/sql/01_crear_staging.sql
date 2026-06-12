/*
=============================================================================
  AUTOMATIZACION FACTURAS DE COMPRA Y SERVICIOS — Ingenio La Corona
  SCRIPT 01: Tabla de staging + Stored Procedures
  Base: CORONA | SQL Server 2008 R2
  Ejecutar en SSMS conectado a: serverico\CORONA
  Operador principal: pdietrich
=============================================================================
  ORDEN DE EJECUCIÓN:
    1. Sección A — Tabla UD_EZI_STAGING_FACTURAS
    2. Sección B — SP_STAGING_INSERTAR
    3. Sección C — SP_STAGING_PENDIENTES
    4. Sección D — SP_STAGING_MARCAR_PROCESADO
    5. Sección E — SP_STAGING_RECHAZAR
    6. Sección F — SP_STAGING_RESUMEN (dashboard)
=============================================================================
*/

USE CORONA
GO

-- ===========================================================================
-- SECCIÓN A — TABLA DE STAGING
-- ===========================================================================

IF NOT EXISTS (SELECT 1 FROM sys.tables WHERE name = 'UD_EZI_STAGING_FACTURAS')
BEGIN

    CREATE TABLE UD_EZI_STAGING_FACTURAS (

        -- CONTROL Y TRAZABILIDAD
        ID                  uniqueidentifier    NOT NULL
                            CONSTRAINT PK_UD_EZI_STAGING_FACTURAS PRIMARY KEY,
        TIPO_OPERACION      varchar(30)         NOT NULL,
            -- 'FACTURA_COMPRA' | 'FACTURA_SERVICIO' | 'GASTO_BANCARIO'
        ESTADO_PROCESO      varchar(20)         NOT NULL    DEFAULT 'PENDIENTE',
            -- PENDIENTE | EN_REVISION | APROBADO | PROCESADO | RECHAZADO | ERROR
        FECHA_CARGA         varchar(17)         NOT NULL,   -- YYYYMMDDHHMMSSMMM
        FECHA_APROBACION    varchar(17)         NULL,
        FECHA_PROCESO       varchar(17)         NULL,
        USUARIO_CARGA       varchar(30)         NOT NULL,
        APROBADO_POR        varchar(30)         NULL,
        PROCESADO_POR       varchar(30)         NULL,
        ORIGEN              varchar(50)         NULL,
            -- 'PDF_EMAIL' | 'MANUAL' | 'AFIP_WS'
        ERROR_DETALLE       varchar(500)        NULL,
        TR_GENERADO_ID      uniqueidentifier    NULL,       -- ID TR en Calipso post-carga
        LOG_PROCESO         varchar(2000)       NULL,

        -- COMPROBANTE
        TIPOTRANSACCION_ID  uniqueidentifier    NOT NULL,
        NUMERODOCUMENTO     varchar(20)         NOT NULL,   -- PtoVta(4)+NroComp(8) o PtoVta-NroComp
        LETRA               varchar(1)          NOT NULL,   -- A | B | C | M | E
        FECHA_EMISION       varchar(8)          NOT NULL,   -- YYYYMMDD
        FECHA_VENCIMIENTO   varchar(8)          NULL,       -- YYYYMMDD pago
        REFERENCIA          varchar(50)         NULL,       -- nro OC o nro ConstServ

        -- PROVEEDOR (resuelto por middleware)
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
        FECHA_VTO_CAE       varchar(8)          NULL,       -- YYYYMMDD

        -- IMPUTACION
        CENTROCOSTOS_ID     uniqueidentifier    NULL,
        CENTROCOSTOS_NOMBRE varchar(100)        NULL,       -- desnormalizado para vista rápida
        COMPANIA_ID         uniqueidentifier    NOT NULL
                            DEFAULT 'FC20C32D-3EFA-11D5-86AD-0080AD403F5F',
        UNIDADOPERATIVA_ID  uniqueidentifier    NULL,

        -- METADATOS PDF
        PDF_FILENAME        varchar(200)        NULL,
        PDF_HASH            varchar(64)         NULL,       -- SHA256 para dedup
        EMAIL_ORIGEN        varchar(200)        NULL,
        EMAIL_ASUNTO        varchar(300)        NULL

    )

    PRINT 'OK: Tabla UD_EZI_STAGING_FACTURAS creada.'

END
ELSE
    PRINT 'INFO: Tabla UD_EZI_STAGING_FACTURAS ya existe, sin cambios.'

GO

-- Índices de consulta frecuente
IF NOT EXISTS (SELECT 1 FROM sys.indexes
               WHERE name = 'IX_STAGING_ESTADO' AND object_id = OBJECT_ID('UD_EZI_STAGING_FACTURAS'))
    CREATE INDEX IX_STAGING_ESTADO
        ON UD_EZI_STAGING_FACTURAS (ESTADO_PROCESO, FECHA_CARGA)

IF NOT EXISTS (SELECT 1 FROM sys.indexes
               WHERE name = 'IX_STAGING_PROVEEDOR' AND object_id = OBJECT_ID('UD_EZI_STAGING_FACTURAS'))
    CREATE INDEX IX_STAGING_PROVEEDOR
        ON UD_EZI_STAGING_FACTURAS (PROVEEDOR_CODIGO, NUMERODOCUMENTO, FECHA_EMISION)

IF NOT EXISTS (SELECT 1 FROM sys.indexes
               WHERE name = 'IX_STAGING_PDF_HASH' AND object_id = OBJECT_ID('UD_EZI_STAGING_FACTURAS'))
    CREATE INDEX IX_STAGING_PDF_HASH
        ON UD_EZI_STAGING_FACTURAS (PDF_HASH)

GO

-- ===========================================================================
-- SECCIÓN B — SP INSERTAR (llamado por el middleware n8n)
-- ===========================================================================

IF OBJECT_ID('UD_EZI_SP_STAGING_INSERTAR') IS NOT NULL
    DROP PROCEDURE UD_EZI_SP_STAGING_INSERTAR
GO

CREATE PROCEDURE UD_EZI_SP_STAGING_INSERTAR
    @UUID_OPERACION         uniqueidentifier,
    @TIPO_OPERACION         varchar(30),
    @ORIGEN                 varchar(50),
    @USUARIO_CARGA          varchar(30),

    @TIPOTRANSACCION_ID     uniqueidentifier,
    @NUMERODOCUMENTO        varchar(20),
    @LETRA                  varchar(1),
    @FECHA_EMISION          varchar(8),
    @FECHA_VENCIMIENTO      varchar(8),
    @REFERENCIA             varchar(50),

    @PROVEEDOR_CUIT         varchar(40),

    @NETO                   decimal(22,10),
    @IVA_21                 decimal(22,10),
    @IVA_105                decimal(22,10),
    @PERCEPCIONES           decimal(22,10),
    @OTROS_IMPUESTOS        decimal(22,10),
    @TOTAL                  decimal(22,10),
    @COTIZACION             decimal(22,10),

    @CAE                    varchar(20),
    @FECHA_VTO_CAE          varchar(8),

    @CENTROCOSTOS_CODIGO    varchar(20),

    @PDF_FILENAME           varchar(200),
    @PDF_HASH               varchar(64),
    @EMAIL_ORIGEN           varchar(200),
    @EMAIL_ASUNTO           varchar(300),

    @RESULTADO              int             OUTPUT,
    @MENSAJE                varchar(500)    OUTPUT
AS
BEGIN
    SET NOCOUNT ON

    DECLARE @TS         varchar(17)
    DECLARE @PROV_ID    uniqueidentifier
    DECLARE @PROV_COD   varchar(15)
    DECLARE @PROV_NOM   varchar(100)
    DECLARE @PROV_CUIT  varchar(40)
    DECLARE @MON_ID     uniqueidentifier
    DECLARE @CC_ID      uniqueidentifier
    DECLARE @CC_NOM     varchar(100)
    DECLARE @UO_ID      uniqueidentifier
    DECLARE @TOTAL_CAL  decimal(22,10)

    SET @TS = CONVERT(varchar(8), GETDATE(), 112) +
              REPLACE(REPLACE(REPLACE(CONVERT(varchar(12), GETDATE(), 114),':',''),'.',''),' ','')

    -- VAL-01: Proveedor activo por CUIT
    SELECT
        @PROV_ID   = ID,
        @PROV_COD  = CODIGO,
        @PROV_NOM  = DENOMINACION,
        @PROV_CUIT = CUIT,
        @MON_ID    = MONEDA_ID,
        @UO_ID     = UNIDADOPERATIVA_ID
    FROM PROVEEDOR
    WHERE CUIT = @PROVEEDOR_CUIT
      AND ACTIVESTATUS = 0

    IF @PROV_ID IS NULL
    BEGIN
        SET @RESULTADO = 1
        SET @MENSAJE = 'ERROR VAL-01: Proveedor con CUIT ' + @PROVEEDOR_CUIT
                       + ' no encontrado o inactivo en Calipso'
        RETURN
    END

    -- VAL-02: Factura no duplicada en Calipso
    IF EXISTS (
        SELECT 1 FROM TRFACTURACOMPRA
        WHERE NUMERODOCUMENTO    = @NUMERODOCUMENTO
          AND CODIGODESTINATARIO = @PROV_COD
          AND FECHAREGISTRO      = @FECHA_EMISION
          AND TIPOTRANSACCION_ID = @TIPOTRANSACCION_ID
    )
    BEGIN
        SET @RESULTADO = 2
        SET @MENSAJE = 'ERROR VAL-02: Factura ' + @NUMERODOCUMENTO
                       + ' del proveedor ' + @PROV_COD
                       + ' con fecha ' + @FECHA_EMISION
                       + ' ya existe en Calipso'
        RETURN
    END

    -- VAL-03: No duplicada en staging activo
    IF EXISTS (
        SELECT 1 FROM UD_EZI_STAGING_FACTURAS
        WHERE NUMERODOCUMENTO  = @NUMERODOCUMENTO
          AND PROVEEDOR_CODIGO = @PROV_COD
          AND FECHA_EMISION    = @FECHA_EMISION
          AND ESTADO_PROCESO NOT IN ('ERROR', 'RECHAZADO')
    )
    BEGIN
        SET @RESULTADO = 3
        SET @MENSAJE = 'ERROR VAL-03: Factura ' + @NUMERODOCUMENTO
                       + ' ya está en staging con estado activo'
        RETURN
    END

    -- VAL-04: Dedup por hash PDF (mismo archivo ya procesado)
    IF @PDF_HASH IS NOT NULL AND @PDF_HASH <> ''
    BEGIN
        IF EXISTS (
            SELECT 1 FROM UD_EZI_STAGING_FACTURAS
            WHERE PDF_HASH = @PDF_HASH
              AND ESTADO_PROCESO NOT IN ('ERROR', 'RECHAZADO')
        )
        BEGIN
            SET @RESULTADO = 4
            SET @MENSAJE = 'ERROR VAL-04: PDF duplicado. Hash ' + @PDF_HASH
                           + ' ya fue procesado'
            RETURN
        END
    END

    -- VAL-05: Si es SERVICIO, ConstServ debe existir, confirmada y sin factura
    IF @TIPO_OPERACION = 'FACTURA_SERVICIO' AND @REFERENCIA IS NOT NULL AND @REFERENCIA <> ''
    BEGIN
        IF NOT EXISTS (
            SELECT 1 FROM TRORDENCOMPRA
            WHERE NUMERODOCUMENTO = @REFERENCIA
              AND TIPOTRANSACCION_ID IN (
                  'E5887DA3-618D-11D5-931E-00E07D9040B9',
                  '08D2A275-E50E-47B5-90A6-5E06088DA3CA'
              )
              AND ESTADO = 'C'
        )
        BEGIN
            SET @RESULTADO = 5
            SET @MENSAJE = 'ERROR VAL-05: ConstServ ' + @REFERENCIA
                           + ' no existe o no está confirmada en Calipso'
            RETURN
        END

        IF EXISTS (
            SELECT 1 FROM TRFACTURACOMPRA
            WHERE DETALLE = @REFERENCIA
        )
        BEGIN
            SET @RESULTADO = 6
            SET @MENSAJE = 'ERROR VAL-06: ConstServ ' + @REFERENCIA
                           + ' ya tiene una factura registrada en Calipso'
            RETURN
        END
    END

    -- VAL-06: CAE debe tener 14 dígitos si se informa
    IF @CAE IS NOT NULL AND @CAE <> '' AND LEN(@CAE) <> 14
    BEGIN
        SET @RESULTADO = 7
        SET @MENSAJE = 'ERROR VAL-07: CAE debe tener 14 dígitos. Recibido: ' + @CAE
        RETURN
    END

    -- VAL-07: Cuadre de totales (tolerancia $1 por redondeos)
    SET @TOTAL_CAL = ISNULL(@NETO, 0) + ISNULL(@IVA_21, 0)
                   + ISNULL(@IVA_105, 0) + ISNULL(@PERCEPCIONES, 0)
                   + ISNULL(@OTROS_IMPUESTOS, 0)

    IF ABS(@TOTAL_CAL - @TOTAL) > 1.0
    BEGIN
        SET @RESULTADO = 8
        SET @MENSAJE = 'ERROR VAL-08: Total ' + CAST(@TOTAL AS varchar(30))
                       + ' no cuadra. Suma componentes: '
                       + CAST(@TOTAL_CAL AS varchar(30))
                       + ' | Diferencia: ' + CAST(ABS(@TOTAL_CAL - @TOTAL) AS varchar(30))
        RETURN
    END

    -- Resolver centro de costos
    IF @CENTROCOSTOS_CODIGO IS NOT NULL AND @CENTROCOSTOS_CODIGO <> ''
    BEGIN
        SELECT @CC_ID = ID, @CC_NOM = NOMBRE
        FROM CENTROCOSTOS
        WHERE CODIGO = @CENTROCOSTOS_CODIGO
          AND ACTIVESTATUS = 0

        IF @CC_ID IS NULL
        BEGIN
            SET @RESULTADO = 9
            SET @MENSAJE = 'ERROR VAL-09: Centro de costo código '
                           + @CENTROCOSTOS_CODIGO + ' no encontrado'
            RETURN
        END
    END

    -- Usar Pesos si la moneda del proveedor no se resuelve
    IF @MON_ID IS NULL
        SET @MON_ID = '76C69765-3DAE-11D5-B059-004854841C8A'  -- Pesos

    -- TODAS LAS VALIDACIONES PASARON
    INSERT INTO UD_EZI_STAGING_FACTURAS (
        ID, TIPO_OPERACION, ESTADO_PROCESO, FECHA_CARGA,
        USUARIO_CARGA, ORIGEN,
        TIPOTRANSACCION_ID, NUMERODOCUMENTO, LETRA,
        FECHA_EMISION, FECHA_VENCIMIENTO, REFERENCIA,
        PROVEEDOR_ID, PROVEEDOR_CODIGO, PROVEEDOR_CUIT, PROVEEDOR_NOMBRE,
        NETO, IVA_21, IVA_105, PERCEPCIONES, OTROS_IMPUESTOS, TOTAL,
        MONEDA_ID, COTIZACION,
        CAE, FECHA_VTO_CAE,
        CENTROCOSTOS_ID, CENTROCOSTOS_NOMBRE,
        COMPANIA_ID, UNIDADOPERATIVA_ID,
        PDF_FILENAME, PDF_HASH, EMAIL_ORIGEN, EMAIL_ASUNTO
    )
    VALUES (
        @UUID_OPERACION, @TIPO_OPERACION, 'PENDIENTE', @TS,
        @USUARIO_CARGA, @ORIGEN,
        @TIPOTRANSACCION_ID, @NUMERODOCUMENTO, @LETRA,
        @FECHA_EMISION, @FECHA_VENCIMIENTO, @REFERENCIA,
        @PROV_ID, @PROV_COD, @PROV_CUIT, @PROV_NOM,
        ISNULL(@NETO, 0), ISNULL(@IVA_21, 0), ISNULL(@IVA_105, 0),
        ISNULL(@PERCEPCIONES, 0), ISNULL(@OTROS_IMPUESTOS, 0), @TOTAL,
        @MON_ID, ISNULL(@COTIZACION, 1),
        @CAE, @FECHA_VTO_CAE,
        @CC_ID, @CC_NOM,
        'FC20C32D-3EFA-11D5-86AD-0080AD403F5F', @UO_ID,
        @PDF_FILENAME, @PDF_HASH, @EMAIL_ORIGEN, @EMAIL_ASUNTO
    )

    SET @RESULTADO = 0
    SET @MENSAJE = 'OK: ' + @TIPO_OPERACION + ' ' + @LETRA + ' '
                   + @NUMERODOCUMENTO + ' | Proveedor: ' + @PROV_COD
                   + ' | Total: $' + CAST(@TOTAL AS varchar(30))
                   + ' | UUID: ' + CAST(@UUID_OPERACION AS varchar(50))
END
GO

PRINT 'OK: SP UD_EZI_SP_STAGING_INSERTAR creado.'
GO

-- ===========================================================================
-- SECCIÓN C — SP PENDIENTES (cola de trabajo para pdietrich)
-- ===========================================================================

IF OBJECT_ID('UD_EZI_SP_STAGING_PENDIENTES') IS NOT NULL
    DROP PROCEDURE UD_EZI_SP_STAGING_PENDIENTES
GO

CREATE PROCEDURE UD_EZI_SP_STAGING_PENDIENTES
    @ESTADO     varchar(20) = 'APROBADO'  -- default: solo los listos para cargar
AS
BEGIN
    SET NOCOUNT ON

    SELECT
        s.ID,
        s.TIPO_OPERACION,
        s.ESTADO_PROCESO,
        s.FECHA_CARGA,
        s.USUARIO_CARGA,
        s.APROBADO_POR,
        s.ORIGEN,

        s.LETRA + ' ' + s.NUMERODOCUMENTO   AS comprobante,
        s.FECHA_EMISION                     AS fecha_doc,
        s.FECHA_VENCIMIENTO                 AS vencimiento,

        s.PROVEEDOR_CODIGO                  AS cod_prov,
        s.PROVEEDOR_NOMBRE                  AS proveedor,
        s.PROVEEDOR_CUIT                    AS cuit,

        s.REFERENCIA                        AS ref_oc_constserv,
        s.CENTROCOSTOS_NOMBRE               AS centro_costo,

        s.NETO,
        s.IVA_21,
        s.IVA_105,
        s.PERCEPCIONES,
        s.TOTAL,
        m.NOMBRE                            AS moneda,
        s.COTIZACION,

        s.CAE,
        s.FECHA_VTO_CAE,

        s.PDF_FILENAME,
        s.EMAIL_ORIGEN,

        -- Semáforo vencimiento CAE
        CASE
            WHEN s.CAE IS NULL OR s.CAE = ''       THEN 'SIN_CAE'
            WHEN s.FECHA_VTO_CAE < CONVERT(varchar(8), GETDATE(), 112)
                                                    THEN 'CAE_VENCIDO'
            ELSE                                         'CAE_OK'
        END AS estado_cae,

        -- Días desde carga
        DATEDIFF(DAY,
            CONVERT(datetime,
                SUBSTRING(s.FECHA_CARGA, 1, 8), 112),
            GETDATE()
        )                                           AS dias_en_cola

    FROM UD_EZI_STAGING_FACTURAS s
    LEFT JOIN MONEDA m ON m.ID = s.MONEDA_ID
    WHERE s.ESTADO_PROCESO = @ESTADO
    ORDER BY s.FECHA_CARGA ASC
END
GO

PRINT 'OK: SP UD_EZI_SP_STAGING_PENDIENTES creado.'
GO

-- ===========================================================================
-- SECCIÓN D — SP MARCAR PROCESADO (pdietrich llama esto después de cargar)
-- ===========================================================================

IF OBJECT_ID('UD_EZI_SP_STAGING_MARCAR_PROCESADO') IS NOT NULL
    DROP PROCEDURE UD_EZI_SP_STAGING_MARCAR_PROCESADO
GO

CREATE PROCEDURE UD_EZI_SP_STAGING_MARCAR_PROCESADO
    @ID                 uniqueidentifier,
    @TR_GENERADO_ID     uniqueidentifier,
    @OPERADOR           varchar(30)
AS
BEGIN
    SET NOCOUNT ON

    DECLARE @TS         varchar(17)
    DECLARE @ESTADO_ACT varchar(20)
    DECLARE @NRO        varchar(20)
    DECLARE @PROV       varchar(100)

    SET @TS = CONVERT(varchar(8), GETDATE(), 112) +
              REPLACE(REPLACE(REPLACE(CONVERT(varchar(12), GETDATE(), 114),':',''),'.',''),' ','')

    -- Verificar que existe y está en estado procesable
    SELECT @ESTADO_ACT = ESTADO_PROCESO,
           @NRO = NUMERODOCUMENTO,
           @PROV = PROVEEDOR_NOMBRE
    FROM UD_EZI_STAGING_FACTURAS
    WHERE ID = @ID

    IF @ESTADO_ACT IS NULL
    BEGIN
        SELECT 'ERROR' AS resultado,
               'Registro no encontrado en staging. ID: '
               + CAST(@ID AS varchar(50)) AS detalle
        RETURN
    END

    IF @ESTADO_ACT <> 'APROBADO'
    BEGIN
        SELECT 'ERROR' AS resultado,
               'El registro no está en estado APROBADO. Estado actual: '
               + @ESTADO_ACT AS detalle
        RETURN
    END

    -- Verificar que el TR existe en Calipso y está confirmado
    IF NOT EXISTS (
        SELECT 1 FROM TRFACTURACOMPRA
        WHERE ID = @TR_GENERADO_ID AND ESTADO = 'C'
    )
    BEGIN
        SELECT 'ALERTA' AS resultado,
               'TR ' + CAST(@TR_GENERADO_ID AS varchar(50))
               + ' no encontrado en Calipso o no está confirmado (ESTADO=C).'
               + ' Verificar manualmente antes de marcar procesado.' AS detalle
        RETURN
    END

    -- Actualizar staging
    UPDATE UD_EZI_STAGING_FACTURAS
    SET
        ESTADO_PROCESO  = 'PROCESADO',
        FECHA_PROCESO   = @TS,
        PROCESADO_POR   = @OPERADOR,
        TR_GENERADO_ID  = @TR_GENERADO_ID
    WHERE ID = @ID

    SELECT
        'OK'                            AS resultado,
        'Factura ' + @NRO + ' de ' + @PROV
        + ' marcada como PROCESADA. TR Calipso: '
        + CAST(@TR_GENERADO_ID AS varchar(50)) AS detalle

END
GO

PRINT 'OK: SP UD_EZI_SP_STAGING_MARCAR_PROCESADO creado.'
GO

-- ===========================================================================
-- SECCIÓN E — SP RECHAZAR
-- ===========================================================================

IF OBJECT_ID('UD_EZI_SP_STAGING_RECHAZAR') IS NOT NULL
    DROP PROCEDURE UD_EZI_SP_STAGING_RECHAZAR
GO

CREATE PROCEDURE UD_EZI_SP_STAGING_RECHAZAR
    @ID         uniqueidentifier,
    @MOTIVO     varchar(500),
    @OPERADOR   varchar(30)
AS
BEGIN
    SET NOCOUNT ON

    DECLARE @TS varchar(17)
    SET @TS = CONVERT(varchar(8), GETDATE(), 112) +
              REPLACE(REPLACE(REPLACE(CONVERT(varchar(12), GETDATE(), 114),':',''),'.',''),' ','')

    UPDATE UD_EZI_STAGING_FACTURAS
    SET
        ESTADO_PROCESO  = 'RECHAZADO',
        FECHA_APROBACION = @TS,
        APROBADO_POR    = @OPERADOR,
        ERROR_DETALLE   = @MOTIVO
    WHERE ID = @ID
      AND ESTADO_PROCESO IN ('PENDIENTE', 'EN_REVISION')

    IF @@ROWCOUNT = 0
        SELECT 'ERROR' AS resultado,
               'Registro no encontrado o no está en estado modificable' AS detalle
    ELSE
        SELECT 'OK' AS resultado,
               'Factura rechazada. Motivo: ' + @MOTIVO AS detalle
END
GO

PRINT 'OK: SP UD_EZI_SP_STAGING_RECHAZAR creado.'
GO

-- ===========================================================================
-- SECCIÓN F — SP RESUMEN (dashboard rápido)
-- ===========================================================================

IF OBJECT_ID('UD_EZI_SP_STAGING_RESUMEN') IS NOT NULL
    DROP PROCEDURE UD_EZI_SP_STAGING_RESUMEN
GO

CREATE PROCEDURE UD_EZI_SP_STAGING_RESUMEN
AS
BEGIN
    SET NOCOUNT ON

    SELECT
        ESTADO_PROCESO,
        COUNT(*)                        AS cantidad,
        SUM(TOTAL)                      AS total_acumulado,
        MIN(FECHA_CARGA)                AS mas_antigua,
        MAX(FECHA_CARGA)                AS mas_reciente
    FROM UD_EZI_STAGING_FACTURAS
    GROUP BY ESTADO_PROCESO
    ORDER BY
        CASE ESTADO_PROCESO
            WHEN 'APROBADO'     THEN 1
            WHEN 'PENDIENTE'    THEN 2
            WHEN 'EN_REVISION'  THEN 3
            WHEN 'PROCESADO'    THEN 4
            WHEN 'RECHAZADO'    THEN 5
            WHEN 'ERROR'        THEN 6
            ELSE                     7
        END
END
GO

PRINT 'OK: SP UD_EZI_SP_STAGING_RESUMEN creado.'
GO

-- ===========================================================================
-- VERIFICACION FINAL
-- ===========================================================================

SELECT
    'UD_EZI_STAGING_FACTURAS'          AS objeto,
    'TABLE'                             AS tipo,
    CASE WHEN OBJECT_ID('UD_EZI_STAGING_FACTURAS') IS NOT NULL
         THEN 'OK' ELSE 'FALTA' END     AS estado
UNION ALL SELECT 'UD_EZI_SP_STAGING_INSERTAR',          'SP',
    CASE WHEN OBJECT_ID('UD_EZI_SP_STAGING_INSERTAR')          IS NOT NULL THEN 'OK' ELSE 'FALTA' END
UNION ALL SELECT 'UD_EZI_SP_STAGING_PENDIENTES',         'SP',
    CASE WHEN OBJECT_ID('UD_EZI_SP_STAGING_PENDIENTES')        IS NOT NULL THEN 'OK' ELSE 'FALTA' END
UNION ALL SELECT 'UD_EZI_SP_STAGING_MARCAR_PROCESADO',  'SP',
    CASE WHEN OBJECT_ID('UD_EZI_SP_STAGING_MARCAR_PROCESADO')  IS NOT NULL THEN 'OK' ELSE 'FALTA' END
UNION ALL SELECT 'UD_EZI_SP_STAGING_RECHAZAR',          'SP',
    CASE WHEN OBJECT_ID('UD_EZI_SP_STAGING_RECHAZAR')          IS NOT NULL THEN 'OK' ELSE 'FALTA' END
UNION ALL SELECT 'UD_EZI_SP_STAGING_RESUMEN',           'SP',
    CASE WHEN OBJECT_ID('UD_EZI_SP_STAGING_RESUMEN')           IS NOT NULL THEN 'OK' ELSE 'FALTA' END
