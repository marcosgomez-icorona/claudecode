-- =============================================================
--  CREATE TABLE datos_Cania
--  Base de datos: db_corona
--  Ingenio La Corona — Molienda Web
--  Generado: 2026-05-04
--  Tabla fuente: pesadas de caña ingresadas desde sistema cañeros
--  Accedida desde: controller/molienda_online.php
-- =============================================================

USE `db_corona`;

-- -------------------------------------------------------------
--  datos_Cania
--  Registro de cada pesada de caña: datos comerciales, calidad
--  y trazabilidad (cañero, finca, transporte, cosechero).
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `datos_Cania` (
  `id`               int unsigned  NOT NULL AUTO_INCREMENT,
  `numero_pesada`    int unsigned  NOT NULL COMMENT 'Número secuencial de pesada',
  `fechaindustrial`  date          NOT NULL COMMENT 'Fecha industrial / turno',
  `hora`             varchar(8)    NOT NULL DEFAULT '' COMMENT 'Hora de pesada HH:MM:SS',
  `grupo`            varchar(50)   NOT NULL DEFAULT '' COMMENT 'Grupo cañero',
  `caniero`          varchar(100)  NOT NULL DEFAULT '' COMMENT 'Nombre o código del cañero',
  `nro_muestra`      varchar(20)   DEFAULT NULL COMMENT 'Número de muestra de laboratorio',
  `cania_bruta`      decimal(10,2) DEFAULT NULL COMMENT 'Peso bruto de caña (tn)',
  `tara`             decimal(10,2) DEFAULT NULL COMMENT 'Tara del vehículo (tn)',
  `prepesada`        decimal(10,2) DEFAULT NULL COMMENT 'Pre-pesada (tn)',
  `trash`            decimal(8,4)  DEFAULT NULL COMMENT 'Trash registrado (%)',
  `trashReal`        decimal(8,4)  DEFAULT NULL COMMENT 'Trash real ajustado (%)',
  `polporciento`     decimal(8,4)  DEFAULT NULL COMMENT 'Pol % caña',
  `brixporciento`    decimal(8,4)  DEFAULT NULL COMMENT 'Brix % caña',
  `pureza`           decimal(8,4)  DEFAULT NULL COMMENT 'Pureza (%)',
  `rendimiento`      decimal(8,4)  DEFAULT NULL COMMENT 'Rendimiento teórico (%)',
  `rendimientoReal`  decimal(8,4)  DEFAULT NULL COMMENT 'Rendimiento real (%)',
  `tipo_cania`       varchar(30)   DEFAULT NULL COMMENT 'Tipo de caña (ej: Verde, Quemada)',
  `tipo_contrato`    varchar(50)   DEFAULT NULL COMMENT 'Tipo de contrato cañero',
  `fecha_pesada`     datetime      DEFAULT NULL COMMENT 'Fecha y hora de entrada a báscula',
  `hora_pesada`      varchar(8)    DEFAULT NULL COMMENT 'Hora de pesada (redundante, legacy)',
  `fecha_salida`     datetime      DEFAULT NULL COMMENT 'Fecha y hora de salida de báscula',
  `hora_salida`      varchar(8)    DEFAULT NULL COMMENT 'Hora de salida (redundante, legacy)',
  `nromuestra2`      varchar(20)   DEFAULT NULL COMMENT 'Número de segunda muestra',
  `usuario`          varchar(50)   DEFAULT NULL COMMENT 'Usuario que registró la pesada',
  `transporte`       varchar(100)  DEFAULT NULL COMMENT 'Empresa transportista',
  `fletero`          varchar(100)  DEFAULT NULL COMMENT 'Fletero / chofer',
  `cosechero`        varchar(100)  DEFAULT NULL COMMENT 'Cosechero / contratista de cosecha',
  `finca`            varchar(50)   DEFAULT NULL COMMENT 'Código o ID de finca',
  `nombre_finca`     varchar(100)  DEFAULT NULL COMMENT 'Nombre descriptivo de la finca',
  `patente`          varchar(15)   DEFAULT NULL COMMENT 'Patente del camión/acoplado',
  `observaciones`    varchar(500)  DEFAULT NULL COMMENT 'Observaciones libres',
  `created_at`       datetime      NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp de inserción',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_numero_pesada` (`numero_pesada`),
  KEY `idx_fechaindustrial`      (`fechaindustrial`),
  KEY `idx_fecha_pesada`         (`fecha_pesada`),
  KEY `idx_caniero`              (`caniero`),
  KEY `idx_grupo`                (`grupo`),
  KEY `idx_created_at`           (`created_at`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci
  COMMENT='Pesadas de caña: datos comerciales, calidad y trazabilidad';
