-- ============================================================================
--  SCRIPT 00: Crear base de datos MySQL + usuario + tabla staging
--  Ejecutar como root en el servidor MySQL (192.168.0.23)
--  Base: db_automatizaciones
-- ============================================================================

-- 1. Crear la base de datos
CREATE DATABASE IF NOT EXISTS db_automatizaciones
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- 2. Crear usuario (acceso local y remoto)
CREATE USER IF NOT EXISTS 'usr_automatizacion'@'localhost' IDENTIFIED BY 'Corona1234$';
CREATE USER IF NOT EXISTS 'usr_automatizacion'@'%'          IDENTIFIED BY 'Corona1234$';

-- 3. Permisos completos sobre la base
GRANT ALL PRIVILEGES ON db_automatizaciones.* TO 'usr_automatizacion'@'localhost';
GRANT ALL PRIVILEGES ON db_automatizaciones.* TO 'usr_automatizacion'@'%';

FLUSH PRIVILEGES;

-- ============================================================================
--  TABLA PRINCIPAL DE STAGING
-- ============================================================================

USE db_automatizaciones;

CREATE TABLE IF NOT EXISTS staging_facturas (

    -- CONTROL Y TRAZABILIDAD
    id                  CHAR(36)            NOT NULL,
    tipo_operacion      VARCHAR(30)         NOT NULL,
    estado_proceso      VARCHAR(20)         NOT NULL DEFAULT 'PENDIENTE',
        -- PENDIENTE | EN_REVISION | APROBADO | PROCESADO | RECHAZADO | ERROR
    fecha_carga         DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_aprobacion    DATETIME            NULL,
    fecha_proceso       DATETIME            NULL,
    usuario_carga       VARCHAR(30)         NOT NULL,
    aprobado_por        VARCHAR(30)         NULL,
    procesado_por       VARCHAR(30)         NULL,
    origen              VARCHAR(50)         NULL,
    error_detalle       VARCHAR(500)        NULL,
    tr_generado_id      CHAR(36)            NULL,
    log_proceso         TEXT                NULL,

    -- COMPROBANTE
    tipotransaccion_id  CHAR(36)            NOT NULL,
    numerodocumento     VARCHAR(20)         NOT NULL,
    letra               CHAR(1)             NOT NULL,
    fecha_emision       CHAR(8)             NOT NULL,
    fecha_vencimiento   CHAR(8)             NULL,
    referencia          VARCHAR(50)         NULL,

    -- PROVEEDOR
    proveedor_id        CHAR(36)            NOT NULL,
    proveedor_codigo    VARCHAR(15)         NOT NULL,
    proveedor_cuit      VARCHAR(40)         NOT NULL,
    proveedor_nombre    VARCHAR(100)        NOT NULL,

    -- IMPORTES
    neto                DECIMAL(22,10)      NOT NULL DEFAULT 0,
    iva_21              DECIMAL(22,10)      NULL     DEFAULT 0,
    iva_105             DECIMAL(22,10)      NULL     DEFAULT 0,
    percepciones        DECIMAL(22,10)      NULL     DEFAULT 0,
    otros_impuestos     DECIMAL(22,10)      NULL     DEFAULT 0,
    total               DECIMAL(22,10)      NOT NULL,
    moneda_id           CHAR(36)            NOT NULL,
    cotizacion          DECIMAL(22,10)      NOT NULL DEFAULT 1,

    -- FISCAL AFIP
    cae                 VARCHAR(20)         NULL,
    fecha_vto_cae       CHAR(8)             NULL,

    -- IMPUTACIÓN
    centrocostos_id     CHAR(36)            NULL,
    centrocostos_nombre VARCHAR(100)        NULL,
    compania_id         CHAR(36)            NOT NULL DEFAULT 'FC20C32D-3EFA-11D5-86AD-0080AD403F5F',
    unidadoperativa_id  CHAR(36)            NULL,

    -- METADATOS PDF
    pdf_filename        VARCHAR(200)        NULL,
    pdf_hash            VARCHAR(64)         NULL,
    email_origen        VARCHAR(200)        NULL,
    email_asunto        VARCHAR(300)        NULL,

    -- ÍTEMS DEL PDF (agregado script 02)
    items_json          MEDIUMTEXT          NULL,

    PRIMARY KEY (id),
    INDEX idx_estado    (estado_proceso, fecha_carga),
    INDEX idx_proveedor (proveedor_codigo, numerodocumento, fecha_emision),
    INDEX idx_hash      (pdf_hash)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
--  VERIFICACIÓN FINAL
-- ============================================================================

SELECT
    'db_automatizaciones'   AS base,
    'staging_facturas'      AS tabla,
    COUNT(*)                AS columnas
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'db_automatizaciones'
  AND TABLE_NAME   = 'staging_facturas';

-- Debe devolver: columnas = 38

SELECT user, host FROM mysql.user WHERE user = 'usr_automatizacion';
-- Debe devolver 2 filas: localhost y %
