-- ============================================================================
--  SCRIPT 04: Tabla de ítems de facturas
--  Ejecutar en MySQL sobre db_automatizaciones
-- ============================================================================

USE db_automatizaciones;

CREATE TABLE IF NOT EXISTS staging_facturas_items (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    factura_id       VARCHAR(36)     NOT NULL  COMMENT 'FK a staging_facturas.id',
    linea            INT             NOT NULL  DEFAULT 1,
    descripcion      VARCHAR(500)    DEFAULT NULL,
    cantidad         DECIMAL(15,4)   DEFAULT NULL,
    unidad           VARCHAR(50)     DEFAULT NULL,
    precio_unitario  DECIMAL(15,4)   DEFAULT NULL,
    alicuota_iva     INT             DEFAULT NULL  COMMENT 'Porcentaje: 0, 10, 21, 27',
    subtotal         DECIMAL(15,2)   DEFAULT NULL,
    moneda           VARCHAR(10)     DEFAULT 'ARS',
    fecha_carga      DATETIME        DEFAULT NOW(),
    INDEX idx_factura_id (factura_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='Ítems de facturas de proveedores — detalle por línea de comprobante';

-- Permisos para el usuario de automatización
GRANT SELECT, INSERT, UPDATE, DELETE ON db_automatizaciones.staging_facturas_items TO 'usr_automatizacion'@'%';
FLUSH PRIVILEGES;

-- Verificación
SELECT 'Tabla staging_facturas_items creada OK' AS resultado;
SHOW CREATE TABLE staging_facturas_items\G
