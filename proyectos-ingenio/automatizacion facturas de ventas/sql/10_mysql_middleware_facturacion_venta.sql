/*
Base MySQL para middleware de facturacion de venta.

Objetivo:
  Staging, validacion, auditoria y outbox de integracion para facturas de
  venta de azucar y alcohol.

Regla:
  No reemplaza Calipso. No escribe directo en ERP. Guarda estados y payloads
  para middleware/Node-RED con aprobacion humana.
*/

CREATE DATABASE IF NOT EXISTS facturacion_venta_mw
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE facturacion_venta_mw;

CREATE TABLE IF NOT EXISTS usuarios_autorizados (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario VARCHAR(80) NOT NULL,
  nombre VARCHAR(160) NOT NULL,
  email VARCHAR(180) NULL,
  rol ENUM('LECTOR','OPERADOR','APROBADOR','SUPERVISOR','ADMIN') NOT NULL DEFAULT 'LECTOR',
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_usuarios_autorizados_usuario (usuario)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS despachos_pendientes (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  producto_tipo ENUM('AZUCAR','ALCOHOL') NOT NULL,
  origen_tabla VARCHAR(80) NOT NULL,
  origen_id VARCHAR(80) NOT NULL,
  remito VARCHAR(40) NOT NULL,
  numero_remito VARCHAR(40) NULL,
  fecha_remito DATETIME NULL,
  cliente_nombre VARCHAR(220) NULL,
  cliente_cuit VARCHAR(20) NULL,
  cliente_id VARCHAR(80) NULL,
  factura_origen VARCHAR(80) NULL,
  estado_origen VARCHAR(80) NULL,
  importado TINYINT NULL,
  confirmado TINYINT NULL,
  cumplido VARCHAR(20) NULL,
  orden VARCHAR(80) NULL,
  producto_id VARCHAR(80) NULL,
  descripcion VARCHAR(255) NULL,
  cantidad DECIMAL(18,4) NULL,
  unidad VARCHAR(40) NULL,
  cantidad_secundaria DECIMAL(18,4) NULL,
  unidad_secundaria VARCHAR(40) NULL,
  precio DECIMAL(18,4) NULL,
  importe_estimado DECIMAL(18,4) NULL,
  patente VARCHAR(40) NULL,
  chasis VARCHAR(40) NULL,
  chofer VARCHAR(160) NULL,
  dni_chofer VARCHAR(40) NULL,
  alcohol_gl VARCHAR(40) NULL,
  alcohol_litros VARCHAR(40) NULL,
  alcohol_nro_analisis VARCHAR(80) NULL,
  alcohol_neto VARCHAR(40) NULL,
  alcohol_bruto VARCHAR(40) NULL,
  hash_fuente CHAR(64) NOT NULL,
  estado_mw ENUM('PENDIENTE','SELECCIONADO','EN_PREFACTURA','FACTURADO','IGNORADO','ERROR') NOT NULL DEFAULT 'PENDIENTE',
  ultima_sync DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_despachos_producto_remito_origen (producto_tipo, remito, origen_tabla, origen_id),
  KEY ix_despachos_estado (estado_mw),
  KEY ix_despachos_fecha (fecha_remito),
  KEY ix_despachos_cliente (cliente_cuit),
  KEY ix_despachos_orden (orden)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS prefacturas (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  uuid CHAR(36) NOT NULL,
  producto_tipo ENUM('AZUCAR','ALCOHOL','MIXTO') NOT NULL,
  cliente_nombre VARCHAR(220) NULL,
  cliente_cuit VARCHAR(20) NULL,
  criterio_agrupacion ENUM('POR_REMITO','POR_ORDEN','POR_CLIENTE','MANUAL') NOT NULL DEFAULT 'MANUAL',
  estado ENUM('BORRADOR','EN_VALIDACION','CON_ALERTAS','APROBADO','ENVIADO_CALIPSO','FACTURADO','ERROR','ANULADO') NOT NULL DEFAULT 'BORRADOR',
  total_estimado DECIMAL(18,4) NOT NULL DEFAULT 0,
  moneda VARCHAR(20) NULL,
  observacion_operador TEXT NULL,
  observacion_aprobador TEXT NULL,
  creado_por VARCHAR(80) NOT NULL,
  aprobado_por VARCHAR(80) NULL,
  aprobado_en DATETIME NULL,
  enviado_calipso_en DATETIME NULL,
  factura_numero VARCHAR(80) NULL,
  factura_calipso_id VARCHAR(80) NULL,
  error_mensaje TEXT NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_prefacturas_uuid (uuid),
  KEY ix_prefacturas_estado (estado),
  KEY ix_prefacturas_cliente (cliente_cuit)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS prefactura_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  prefactura_id BIGINT UNSIGNED NOT NULL,
  despacho_id BIGINT UNSIGNED NOT NULL,
  remito VARCHAR(40) NOT NULL,
  orden VARCHAR(80) NULL,
  descripcion VARCHAR(255) NULL,
  cantidad DECIMAL(18,4) NULL,
  unidad VARCHAR(40) NULL,
  precio DECIMAL(18,4) NULL,
  importe_estimado DECIMAL(18,4) NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_prefactura_despacho (prefactura_id, despacho_id),
  KEY ix_prefactura_items_despacho (despacho_id),
  CONSTRAINT fk_prefactura_items_prefactura
    FOREIGN KEY (prefactura_id) REFERENCES prefacturas(id),
  CONSTRAINT fk_prefactura_items_despacho
    FOREIGN KEY (despacho_id) REFERENCES despachos_pendientes(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS prefactura_validaciones (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  prefactura_id BIGINT UNSIGNED NOT NULL,
  despacho_id BIGINT UNSIGNED NULL,
  severidad ENUM('INFO','ADVERTENCIA','BLOQUEANTE') NOT NULL,
  codigo VARCHAR(80) NOT NULL,
  mensaje VARCHAR(500) NOT NULL,
  resuelta TINYINT(1) NOT NULL DEFAULT 0,
  justificacion TEXT NULL,
  resuelta_por VARCHAR(80) NULL,
  resuelta_en DATETIME NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_validaciones_prefactura (prefactura_id),
  KEY ix_validaciones_severidad (severidad),
  CONSTRAINT fk_validaciones_prefactura
    FOREIGN KEY (prefactura_id) REFERENCES prefacturas(id),
  CONSTRAINT fk_validaciones_despacho
    FOREIGN KEY (despacho_id) REFERENCES despachos_pendientes(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS integracion_outbox (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  uuid CHAR(36) NOT NULL,
  prefactura_id BIGINT UNSIGNED NOT NULL,
  tipo_evento ENUM('PREFACTURA_APROBADA','ENVIAR_CALIPSO','REINTENTAR_CALIPSO','NOTIFICAR_RESULTADO') NOT NULL,
  estado ENUM('PENDIENTE','EN_PROCESO','PROCESADO','ERROR','CANCELADO') NOT NULL DEFAULT 'PENDIENTE',
  payload_json JSON NOT NULL,
  intentos INT NOT NULL DEFAULT 0,
  ultimo_error TEXT NULL,
  procesar_desde DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  procesado_en DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_outbox_uuid_evento (uuid, tipo_evento),
  KEY ix_outbox_estado (estado, procesar_desde),
  CONSTRAINT fk_outbox_prefactura
    FOREIGN KEY (prefactura_id) REFERENCES prefacturas(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS auditoria_eventos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  uuid CHAR(36) NOT NULL,
  entidad VARCHAR(80) NOT NULL,
  entidad_id VARCHAR(80) NULL,
  evento VARCHAR(120) NOT NULL,
  usuario VARCHAR(80) NULL,
  detalle_json JSON NULL,
  ip_origen VARCHAR(80) NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_auditoria_uuid (uuid),
  KEY ix_auditoria_evento (evento),
  KEY ix_auditoria_fecha (creado_en)
) ENGINE=InnoDB;

CREATE OR REPLACE VIEW vw_prefacturas_estado AS
SELECT
  p.uuid,
  p.producto_tipo,
  p.cliente_nombre,
  p.cliente_cuit,
  p.criterio_agrupacion,
  p.estado,
  p.total_estimado,
  p.creado_por,
  p.aprobado_por,
  p.factura_numero,
  p.error_mensaje,
  p.creado_en,
  COUNT(pi.id) AS cantidad_items,
  SUM(CASE WHEN v.severidad = 'BLOQUEANTE' AND v.resuelta = 0 THEN 1 ELSE 0 END) AS bloqueantes_pendientes,
  SUM(CASE WHEN v.severidad = 'ADVERTENCIA' AND v.resuelta = 0 THEN 1 ELSE 0 END) AS advertencias_pendientes
FROM prefacturas p
LEFT JOIN prefactura_items pi
  ON pi.prefactura_id = p.id
LEFT JOIN prefactura_validaciones v
  ON v.prefactura_id = p.id
GROUP BY
  p.uuid, p.producto_tipo, p.cliente_nombre, p.cliente_cuit,
  p.criterio_agrupacion, p.estado, p.total_estimado,
  p.creado_por, p.aprobado_por, p.factura_numero,
  p.error_mensaje, p.creado_en;
