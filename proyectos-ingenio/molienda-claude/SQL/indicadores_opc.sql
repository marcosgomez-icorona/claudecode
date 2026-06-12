-- ====================================================
-- TABLA: indicadores_opc
-- Almacena lecturas OPC de SRV-CONTROL, srv-cald1
-- Insertado por Node-RED cada 60 segundos
-- ====================================================
CREATE TABLE IF NOT EXISTS `indicadores_opc` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `timestamp` DATETIME NOT NULL,

  -- TRAPICHE (SRV-CONTROL)
  `velocidad_molino1`     FLOAT DEFAULT NULL,
  `velocidad_molino6`     FLOAT DEFAULT NULL,
  `balanza_cinta`         FLOAT DEFAULT NULL,
  `agua_imbibicion`       FLOAT DEFAULT NULL,
  `presion_molino6_este`  FLOAT DEFAULT NULL,
  `presion_molino6_oeste` FLOAT DEFAULT NULL,

  -- FABRICACION (SRV-CONTROL)
  `caudal_jugo_clarif`    FLOAT DEFAULT NULL,
  `nivel_melado_tratado`  FLOAT DEFAULT NULL,
  `nivel_melado`          FLOAT DEFAULT NULL,
  `nivel_decantador1`     FLOAT DEFAULT NULL,
  `nivel_decantador2`     FLOAT DEFAULT NULL,
  `nivel_decantador3`     FLOAT DEFAULT NULL,
  `descarga_tachos_1ra`   FLOAT DEFAULT NULL,
  `descarga_tachos_2da`   FLOAT DEFAULT NULL,
  `descarga_tachos_3ra`   FLOAT DEFAULT NULL,
  `nivel_jugo_pesado`     FLOAT DEFAULT NULL,
  `nivel_jugo_clarificado` FLOAT DEFAULT NULL,

  -- SALON (SRV-CONTROL)
  `contador_bolsas_dia`   FLOAT DEFAULT NULL,
  `silo_a`  FLOAT DEFAULT NULL,
  `silo_b`  FLOAT DEFAULT NULL,
  `silo_c`  FLOAT DEFAULT NULL,
  `silo_e`  FLOAT DEFAULT NULL,

  -- CALDERA (srv-cald1)
  `presion_vapor_directo` FLOAT DEFAULT NULL,
  `presion_vapor_escape`  FLOAT DEFAULT NULL,
  `presion_agua_alim`     FLOAT DEFAULT NULL,
  `temp_agua_alim`        FLOAT DEFAULT NULL,
  `presion_aire`          FLOAT DEFAULT NULL,
  `caudal_vapor_cald1`    FLOAT DEFAULT NULL,
  `caudal_vapor_cald2`    FLOAT DEFAULT NULL,
  `caudal_vapor_cald3`    FLOAT DEFAULT NULL,
  `caudal_vapor_cald6`    FLOAT DEFAULT NULL,
  `vapor_total`           FLOAT DEFAULT NULL,  -- calculado: sum cald1+2+3+6
  `caudal_gas_cald2`      FLOAT DEFAULT NULL,
  `caudal_gas_cald6`      FLOAT DEFAULT NULL,
  `temp_calentador`       FLOAT DEFAULT NULL,
  `presion_vg1`           FLOAT DEFAULT NULL,
  `nivel_agua_foza`       FLOAT DEFAULT NULL,

  -- USINA (srv-cald1)
  `potencia_activa_siemens`   FLOAT DEFAULT NULL,
  `potencia_reactiva_siemens` FLOAT DEFAULT NULL,
  `frecuencia_siemens`        FLOAT DEFAULT NULL,
  `intensidad_siemens`        FLOAT DEFAULT NULL,
  `potencia_activa_aeg`       FLOAT DEFAULT NULL,
  `potencia_reactiva_aeg`     FLOAT DEFAULT NULL,
  `frecuencia_aeg`            FLOAT DEFAULT NULL,
  `intensidad_aeg`            FLOAT DEFAULT NULL,
  `potencia_activa_edet`      FLOAT DEFAULT NULL,
  `intensidad_edet`           FLOAT DEFAULT NULL,
  `potencia_activa_tgm`       FLOAT DEFAULT NULL,
  `intensidad_tgm`            FLOAT DEFAULT NULL,
  `potencia_total`            FLOAT DEFAULT NULL,  -- calculado: siemens + aeg + edet (+ tgm cuando disponible)

  -- CONSUMOS VAPOR (srv-cald1)
  `cv_trapiche`           FLOAT DEFAULT NULL,
  `cv_usina_alta`         FLOAT DEFAULT NULL,
  `cv_destileria`         FLOAT DEFAULT NULL,
  `cv_aux_total`          FLOAT DEFAULT NULL,
  `cv_preparacion_cania`  FLOAT DEFAULT NULL,

  -- DESTILERIA (SRV-CONTROL)
  `caudal_vino`           FLOAT DEFAULT NULL,
  `caudal_alcohol`        FLOAT DEFAULT NULL,
  `caudal_jugo_dilutor`   FLOAT DEFAULT NULL,
  `caudal_melaza_dilutor` FLOAT DEFAULT NULL,
  `caudal_agua_dilutor`   FLOAT DEFAULT NULL,
  `caudal_vino_160`       FLOAT DEFAULT NULL,

  -- OTROS (SRV-CONTROL)
  `presion_k2`            FLOAT DEFAULT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_timestamp` (`timestamp`),
  INDEX `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Lecturas OPC cada 60s desde Node-RED';


-- ====================================================
-- ALTER TABLE: agregar columnas faltantes en instalaciones existentes
-- Ejecutar solo si la tabla ya existe (idempotente con IF NOT EXISTS no disponible en ALTER,
-- ignorar el error "Duplicate column name" si alguna ya fue agregada)
-- ====================================================

-- Fabricacion (grupo 2)
ALTER TABLE `indicadores_opc`
  ADD COLUMN IF NOT EXISTS `nivel_jugo_pesado`      FLOAT DEFAULT NULL AFTER `descarga_tachos_3ra`,
  ADD COLUMN IF NOT EXISTS `nivel_jugo_clarificado` FLOAT DEFAULT NULL AFTER `nivel_jugo_pesado`;

-- Caldera / Vapor (grupo 2 y 3)
ALTER TABLE `indicadores_opc`
  ADD COLUMN IF NOT EXISTS `presion_vapor_escape` FLOAT DEFAULT NULL AFTER `presion_vapor_directo`,
  ADD COLUMN IF NOT EXISTS `temp_agua_alim`       FLOAT DEFAULT NULL AFTER `presion_agua_alim`,
  ADD COLUMN IF NOT EXISTS `temp_calentador`      FLOAT DEFAULT NULL AFTER `caudal_gas_cald6`,
  ADD COLUMN IF NOT EXISTS `presion_vg1`          FLOAT DEFAULT NULL AFTER `temp_calentador`,
  ADD COLUMN IF NOT EXISTS `nivel_agua_foza`      FLOAT DEFAULT NULL AFTER `presion_vg1`;

-- Usina (grupo 3)
ALTER TABLE `indicadores_opc`
  ADD COLUMN IF NOT EXISTS `potencia_activa_edet` FLOAT DEFAULT NULL AFTER `intensidad_aeg`,
  ADD COLUMN IF NOT EXISTS `intensidad_edet`      FLOAT DEFAULT NULL AFTER `potencia_activa_edet`;

-- TGM (pendiente cablear tag OPC)
ALTER TABLE `indicadores_opc`
  ADD COLUMN IF NOT EXISTS `potencia_activa_tgm` FLOAT DEFAULT NULL AFTER `intensidad_edet`,
  ADD COLUMN IF NOT EXISTS `intensidad_tgm`      FLOAT DEFAULT NULL AFTER `potencia_activa_tgm`;

-- Destileria (grupo 3)
ALTER TABLE `indicadores_opc`
  ADD COLUMN IF NOT EXISTS `caudal_agua_dilutor` FLOAT DEFAULT NULL AFTER `caudal_melaza_dilutor`,
  ADD COLUMN IF NOT EXISTS `caudal_vino_160`     FLOAT DEFAULT NULL AFTER `caudal_agua_dilutor`;

-- Otros
ALTER TABLE `indicadores_opc`
  ADD COLUMN IF NOT EXISTS `presion_k2` FLOAT DEFAULT NULL AFTER `caudal_vino_160`;
