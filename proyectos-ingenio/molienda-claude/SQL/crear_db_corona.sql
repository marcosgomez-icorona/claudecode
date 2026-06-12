-- =============================================================
--  CREACION BASE DE DATOS db_corona
--  Ingenio La Corona — Molienda Web
--  Generado: 2026-05-03
--  Importar desde phpMyAdmin: seleccionar servidor (no DB),
--  ir a Importar y subir este archivo.
-- =============================================================

CREATE DATABASE IF NOT EXISTS `db_corona`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_0900_ai_ci;

USE `db_corona`;

-- -------------------------------------------------------------
-- 1. CALIDAD AZUCAR POR HORA
--    Fuente: pr_ezi_especiales (Cinta Larga, Cinta Corta, Embolsado, Crudo)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `calidadAzucar_xHora` (
  `id`              int           NOT NULL AUTO_INCREMENT,
  `codigoproceso`   varchar(50)   NOT NULL,
  `fechaindustrial` date          NOT NULL,
  `hora`            time          NOT NULL,
  `color`           decimal(10,2) DEFAULT NULL,
  `turbidez`        decimal(10,2) DEFAULT NULL,
  `humedad`         decimal(10,2) DEFAULT NULL,
  `cenizas`         decimal(10,2) DEFAULT NULL,
  `granulometria_20` decimal(10,2) DEFAULT NULL,
  `granulometria_30` decimal(10,2) DEFAULT NULL,
  `sediment_test`   decimal(10,2) DEFAULT NULL,
  `so2`             decimal(10,2) DEFAULT NULL,
  `destino`         varchar(50)   DEFAULT NULL,
  `fecha`           date          DEFAULT NULL,
  `created_at`      timestamp     NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_calidad` (`codigoproceso`,`fechaindustrial`,`hora`),
  KEY `idx_fecha` (`fecha`),
  KEY `idx_proceso` (`codigoproceso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- -------------------------------------------------------------
-- 2. CALIDAD BAGAZO POR HORA
--    Fuente: pr_ezi_laboratorio_gral2 WHERE codigoproceso='Bagazo'
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `calidadBagazo_xHora` (
  `id`              int           NOT NULL AUTO_INCREMENT,
  `codigoproceso`   varchar(50)   NOT NULL,
  `fechaindustrial` date          NOT NULL,
  `fecharegistro`   datetime      DEFAULT NULL,
  `hora`            time          NOT NULL,
  `peso_final`      decimal(10,2) NOT NULL DEFAULT '0.00',
  `tara`            decimal(10,2) NOT NULL DEFAULT '0.00',
  `polmanual`       decimal(6,3)  NOT NULL DEFAULT '0.000',
  `humedad`         decimal(6,2)  DEFAULT NULL,
  `pesoliquido`     decimal(10,2) NOT NULL DEFAULT '0.00',
  `brixautomatico`  decimal(6,3)  NOT NULL DEFAULT '0.000',
  `polautomatico`   decimal(6,3)  NOT NULL DEFAULT '0.000',
  `fibra`           decimal(6,2)  NOT NULL DEFAULT '0.00',
  `created_at`      timestamp     NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_bagazo` (`codigoproceso`,`fechaindustrial`,`hora`),
  KEY `idx_fecha` (`fechaindustrial`),
  KEY `idx_proceso` (`codigoproceso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- -------------------------------------------------------------
-- 3. CALIDAD CACHAZA POR HORA
--    Fuente: pr_ezi_laboratorio_gral2 WHERE codigoproceso='Cachaza'
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `calidadCachaza_xHora` (
  `id_mssql`        int           NOT NULL,
  `codigoproceso`   varchar(50)   NOT NULL,
  `fechaindustrial` date          NOT NULL,
  `fecharegistro`   datetime      DEFAULT NULL,
  `hora`            time          NOT NULL,
  `polmanual1`      decimal(6,2)  DEFAULT NULL,
  `polmanual2`      decimal(6,2)  DEFAULT NULL,
  `polmanual3`      decimal(6,2)  DEFAULT NULL,
  `polmanual4`      decimal(6,2)  DEFAULT NULL,
  `humedad`         decimal(6,2)  DEFAULT NULL,
  `polautomatico`   decimal(6,2)  DEFAULT NULL,
  `created_at`      timestamp     NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      timestamp     NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mssql`),
  UNIQUE KEY `uk_cachaza_proc_fecha_hora` (`codigoproceso`,`fechaindustrial`,`hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- -------------------------------------------------------------
-- 4. CALIDAD JUGO POR HORA
--    Fuente: pr_ezi_laboratorio_gral (Jugo Mixto, 1Era Presion, Clarificado, Melado, Jarabe…)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `calidadJugo_xHora` (
  `id`              int           NOT NULL AUTO_INCREMENT,
  `codigoproceso`   varchar(50)   NOT NULL,
  `fechaindustrial` date          NOT NULL,
  `fecharegistro`   date          DEFAULT NULL,
  `horaregistro`    time          DEFAULT NULL,
  `hora`            varchar(5)    NOT NULL,
  `kilos`           decimal(10,2) DEFAULT NULL,
  `brix_manual`     decimal(6,2)  DEFAULT NULL,
  `temperatura_manual` decimal(6,2) DEFAULT NULL,
  `pol_manual`      decimal(6,2)  DEFAULT NULL,
  `ph_manual`       decimal(5,2)  DEFAULT NULL,
  `brix_automatico` decimal(6,2)  DEFAULT NULL,
  `pol_automatico`  decimal(6,2)  DEFAULT NULL,
  `pureza`          decimal(6,2)  DEFAULT NULL,
  `created_at`      timestamp     NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_calidadJugo_ultimaHora` (`codigoproceso`,`fechaindustrial`,`hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- -------------------------------------------------------------
-- 5. CALIDAD MASA COCIDA POR HORA
--    Fuente: pr_ezi_laboratorio_gral (Masa Cocida 1era/2da/3era)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `calidadMasaCocida_xHora` (
  `id`              bigint        NOT NULL AUTO_INCREMENT,
  `id_mssql`        int           NOT NULL,
  `codigoproceso`   varchar(50)   NOT NULL,
  `fechaindustrial` date          NOT NULL,
  `fecharegistro`   datetime      DEFAULT NULL,
  `horaregistro`    time          DEFAULT NULL,
  `hora`            time          NOT NULL,
  `kilos`           decimal(10,2) DEFAULT '0.00',
  `brixmanual`      decimal(10,3) DEFAULT '0.000',
  `temperaturamanual` decimal(10,2) DEFAULT '0.00',
  `polmanual`       decimal(10,3) DEFAULT '0.000',
  `phmanual`        decimal(10,3) DEFAULT '0.000',
  `brixautomatico`  decimal(10,3) DEFAULT '0.000',
  `polautomatico`   decimal(10,3) DEFAULT '0.000',
  `pureza`          decimal(10,3) DEFAULT '0.000',
  `created_at`      timestamp     NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      timestamp     NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_masa_cocida` (`id_mssql`,`codigoproceso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- -------------------------------------------------------------
-- 6. CALIDAD MELAZA POR HORA
--    Fuente: pr_ezi_laboratorio_gral WHERE codigoproceso='Melaza'
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `calidadMelaza_xHora` (
  `id`              bigint        NOT NULL AUTO_INCREMENT,
  `id_mssql`        bigint        NOT NULL,
  `codigoproceso`   varchar(50)   NOT NULL,
  `fechaindustrial` date          NOT NULL,
  `fecharegistro`   datetime      DEFAULT NULL,
  `horaregistro`    time          DEFAULT NULL,
  `hora`            time          NOT NULL,
  `brixmanual`      decimal(10,2) DEFAULT NULL,
  `temperaturamanual` decimal(10,2) DEFAULT NULL,
  `polmanual`       decimal(10,2) DEFAULT NULL,
  `brixautomatico`  decimal(10,2) DEFAULT NULL,
  `polautomatico`   decimal(10,2) DEFAULT NULL,
  `pureza`          decimal(10,2) DEFAULT NULL,
  `created_at`      timestamp     NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_melaza` (`codigoproceso`,`fechaindustrial`,`hora`),
  KEY `idx_fecha` (`fechaindustrial`),
  KEY `idx_proceso` (`codigoproceso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- -------------------------------------------------------------
-- 7. CALIDAD MIELES POR HORA (incluye mieles destilería)
--    Fuente: pr_ezi_laboratorio_gral (Miel Rica/Pobre, Miel de Segunda…)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `calidadMieles_xHora` (
  `id`              int           NOT NULL AUTO_INCREMENT,
  `codigoproceso`   varchar(100)  NOT NULL,
  `fechaindustrial` date          NOT NULL,
  `fecharegistro`   datetime      DEFAULT NULL,
  `horaregistro`    time          DEFAULT NULL,
  `hora`            time          DEFAULT NULL,
  `brixmanual`      decimal(10,2) DEFAULT '0.00',
  `temperaturamanual` decimal(10,2) DEFAULT '0.00',
  `polmanual`       decimal(10,2) DEFAULT '0.00',
  `brixautomatico`  decimal(10,2) DEFAULT '0.00',
  `polautomatico`   decimal(10,2) DEFAULT '0.00',
  `pureza`          decimal(10,2) DEFAULT '0.00',
  `created_at`      timestamp     NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_laboratorio` (`codigoproceso`,`fechaindustrial`,`hora`),
  KEY `idx_miel_datetime` (`fechaindustrial`,`hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- -------------------------------------------------------------
-- 8. CALIDAD SULFITADO / ENCALADO POR HORA
--    Fuente: pr_ezi_laboratorio_gral2 WHERE codigoproceso IN ('Encalado','Sulfitado')
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `calidadSulfitado_xHora` (
  `id`              bigint        NOT NULL AUTO_INCREMENT,
  `id_mssql`        bigint        NOT NULL,
  `codigoproceso`   varchar(50)   NOT NULL,
  `fechaindustrial` date          NOT NULL,
  `fecharegistro`   date          DEFAULT NULL,
  `horaregistro`    time          DEFAULT NULL,
  `hora`            time          NOT NULL,
  `phmanual`        decimal(10,2) DEFAULT NULL,
  `PPMS02manual`    decimal(10,2) DEFAULT NULL,
  `created_at`      timestamp     NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sulfitado_mssql` (`id_mssql`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- -------------------------------------------------------------
-- 9. DATOS CANIA (pesadas de camiones)
--    Fuente: v_ezi_molienda_laboratorio (MSSQL)
--    Equivale a la tabla 'molienda' en db_molienda
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `datos_Cania` (
  `id`              bigint        NOT NULL AUTO_INCREMENT,
  `fechaindustrial` date          NOT NULL,
  `numero_pesada`   int           NOT NULL,
  `grupo`           varchar(250)  DEFAULT NULL,
  `caniero`         varchar(150)  DEFAULT NULL,
  `nro_muestra`     int           NOT NULL,
  `cania_bruta`     decimal(12,3) DEFAULT NULL,
  `trash`           decimal(6,2)  DEFAULT NULL,
  `trashReal`       decimal(8,5)  DEFAULT NULL,
  `polporciento`    decimal(10,7) DEFAULT NULL,
  `brixporciento`   decimal(10,7) DEFAULT NULL,
  `pureza`          decimal(10,8) DEFAULT NULL,
  `rendimiento`     decimal(10,8) DEFAULT NULL,
  `rendimientoReal` decimal(10,8) DEFAULT NULL,
  `tipo_cania`      varchar(50)   DEFAULT NULL,
  `fecha_pesada`    date          DEFAULT NULL,
  `hora_pesada`     time          DEFAULT NULL,
  `fecha_salida`    date          DEFAULT NULL,
  `hora_salida`     time          DEFAULT NULL,
  `nromuestra2`     varchar(50)   DEFAULT NULL,
  `prepesada`       char(1)       DEFAULT NULL,
  `usuario`         varchar(50)   DEFAULT NULL,
  `tipo_contrato`   varchar(100)  DEFAULT NULL,
  `transporte`      varchar(100)  DEFAULT NULL,
  `fletero`         varchar(150)  DEFAULT NULL,
  `cosechero`       varchar(150)  DEFAULT NULL,
  `finca`           varchar(150)  DEFAULT NULL,
  `nombre_finca`    varchar(150)  DEFAULT NULL,
  `patente`         varchar(150)  DEFAULT NULL,
  `observaciones`   text,
  `tara`            int           DEFAULT NULL,
  `fecha_insert`    timestamp     NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_datos_cania` (`fechaindustrial`,`numero_pesada`,`nro_muestra`),
  KEY `idx_fechaindustrial` (`fechaindustrial`),
  KEY `idx_fecha_pesada` (`fecha_pesada`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- -------------------------------------------------------------
-- 10. MOLIENDA POR HORA (produccion + gas + humedad + bolsas)
--     Fuente: pr_ezi_laboratorio_gral (Molienda, Gas, Humedad Bagazo, Bolsas)
--     Equivale a 'consumos_x_hora' en db_molienda
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `molienda_xHora` (
  `id`              bigint        NOT NULL AUTO_INCREMENT,
  `fechaindustrial` date          NOT NULL,
  `hora`            time          NOT NULL,
  `gas`             decimal(14,2) NOT NULL DEFAULT '0.00',
  `kilos`           decimal(14,2) NOT NULL DEFAULT '0.00',
  `humedad`         decimal(8,2)  NOT NULL DEFAULT '0.00',
  `bolsas`          int           NOT NULL DEFAULT '0',
  `created_at`      timestamp     NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_fecha_hora` (`fechaindustrial`,`hora`),
  KEY `idx_fecha` (`fechaindustrial`),
  KEY `idx_hora` (`hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- -------------------------------------------------------------
-- 11. PARADAS DESTILERIA
--     Fuente: v_pr_ezi_paradas_destileria (MSSQL)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `paradas_destileria` (
  `id`              int           NOT NULL AUTO_INCREMENT,
  `fechaindustrial` date          NOT NULL,
  `desde`           time          NOT NULL,
  `hasta`           time          DEFAULT NULL,
  `t_neto`          time          DEFAULT NULL,
  `origen`          varchar(100)  DEFAULT NULL,
  `maquina`         varchar(100)  DEFAULT NULL,
  `motivo`          varchar(255)  DEFAULT NULL,
  `codigoproceso`   varchar(100)  DEFAULT NULL,
  `created_at`      timestamp     NULL DEFAULT CURRENT_TIMESTAMP,
  `t_neto_minutos`  int           DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_parada` (`fechaindustrial`,`desde`,`codigoproceso`),
  KEY `idx_fecha` (`fechaindustrial`),
  KEY `idx_maquina` (`maquina`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- -------------------------------------------------------------
-- 12. PARADAS FABRICA
--     Fuente: v_pr_ezi_paradas (MSSQL)
--     Equivale a 'paradas' en db_molienda
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `paradas_fabrica` (
  `id`              int           NOT NULL AUTO_INCREMENT,
  `fechaindustrial` date          NOT NULL,
  `desde`           time          NOT NULL,
  `hasta`           time          DEFAULT NULL,
  `t_neto`          varchar(8)    DEFAULT NULL,
  `origen`          varchar(50)   DEFAULT NULL,
  `maquina`         varchar(100)  DEFAULT NULL,
  `motivo`          varchar(255)  DEFAULT NULL,
  `id_mssql`        int           DEFAULT NULL,
  `created_at`      timestamp     NULL DEFAULT CURRENT_TIMESTAMP,
  `t_neto_minutos`  int           DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_id_mssql` (`id_mssql`),
  KEY `idx_fecha` (`fechaindustrial`),
  KEY `idx_maquina` (`maquina`),
  KEY `idx_motivo` (`motivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- -------------------------------------------------------------
-- 13. USUARIOS DEL SISTEMA
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios_sistema` (
  `id`       bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`   varchar(120)    NOT NULL,
  `apellido` varchar(150)    NOT NULL,
  `celular`  varchar(20)     NOT NULL,
  `email`    varchar(150)    DEFAULT NULL,
  `cargo`    varchar(100)    NOT NULL,
  `area`     varchar(100)    NOT NULL,
  `turno`    varchar(50)     DEFAULT NULL,
  `activo`   tinyint(1)      NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `celular` (`celular`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_usuarios_celular` (`celular`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- =============================================================
-- FIN DEL SCRIPT
-- Una vez importado, verificar con:
--   SHOW TABLES;
--   SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'db_corona';
-- Debería mostrar 13 tablas.
-- =============================================================
