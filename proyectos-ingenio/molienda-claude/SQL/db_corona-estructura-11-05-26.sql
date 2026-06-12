-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-05-2026 a las 21:10:26
-- Versión del servidor: 10.1.25-MariaDB
-- Versión de PHP: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_corona`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calidadazucar_xhora`
--

DROP TABLE IF EXISTS `calidadazucar_xhora`;
CREATE TABLE IF NOT EXISTS `calidadazucar_xhora` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigoproceso` varchar(50) NOT NULL,
  `fechaindustrial` date NOT NULL,
  `hora` time NOT NULL,
  `color` decimal(10,2) DEFAULT NULL,
  `turbidez` decimal(10,2) DEFAULT NULL,
  `humedad` decimal(10,2) DEFAULT NULL,
  `cenizas` decimal(10,2) DEFAULT NULL,
  `granulometria_20` decimal(10,2) DEFAULT NULL,
  `granulometria_30` decimal(10,2) DEFAULT NULL,
  `sediment_test` decimal(10,2) DEFAULT NULL,
  `so2` decimal(10,2) DEFAULT NULL,
  `destino` varchar(50) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_calidad` (`codigoproceso`,`fechaindustrial`,`hora`),
  KEY `idx_fecha` (`fecha`),
  KEY `idx_proceso` (`codigoproceso`)
) ENGINE=InnoDB AUTO_INCREMENT=6357712 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calidadbagazo_xhora`
--

DROP TABLE IF EXISTS `calidadbagazo_xhora`;
CREATE TABLE IF NOT EXISTS `calidadbagazo_xhora` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigoproceso` varchar(50) NOT NULL,
  `fechaindustrial` date NOT NULL,
  `fecharegistro` datetime DEFAULT NULL,
  `hora` time NOT NULL,
  `peso_final` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tara` decimal(10,2) NOT NULL DEFAULT '0.00',
  `polmanual` decimal(6,3) NOT NULL DEFAULT '0.000',
  `humedad` decimal(6,2) DEFAULT NULL,
  `pesoliquido` decimal(10,2) NOT NULL DEFAULT '0.00',
  `brixautomatico` decimal(6,3) NOT NULL DEFAULT '0.000',
  `polautomatico` decimal(6,3) NOT NULL DEFAULT '0.000',
  `fibra` decimal(6,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_bagazo` (`codigoproceso`,`fechaindustrial`,`hora`),
  KEY `idx_fecha` (`fechaindustrial`),
  KEY `idx_proceso` (`codigoproceso`)
) ENGINE=InnoDB AUTO_INCREMENT=28243 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calidadcachaza_xhora`
--

DROP TABLE IF EXISTS `calidadcachaza_xhora`;
CREATE TABLE IF NOT EXISTS `calidadcachaza_xhora` (
  `id_mssql` int(11) NOT NULL,
  `codigoproceso` varchar(50) NOT NULL,
  `fechaindustrial` date NOT NULL,
  `fecharegistro` datetime DEFAULT NULL,
  `hora` time NOT NULL,
  `polmanual1` decimal(6,2) DEFAULT NULL,
  `polmanual2` decimal(6,2) DEFAULT NULL,
  `polmanual3` decimal(6,2) DEFAULT NULL,
  `polmanual4` decimal(6,2) DEFAULT NULL,
  `humedad` decimal(6,2) DEFAULT NULL,
  `polautomatico` decimal(6,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mssql`),
  UNIQUE KEY `uk_cachaza_proc_fecha_hora` (`codigoproceso`,`fechaindustrial`,`hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calidadjugo_xhora`
--

DROP TABLE IF EXISTS `calidadjugo_xhora`;
CREATE TABLE IF NOT EXISTS `calidadjugo_xhora` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigoproceso` varchar(50) NOT NULL,
  `fechaindustrial` date NOT NULL,
  `fecharegistro` date DEFAULT NULL,
  `horaregistro` time DEFAULT NULL,
  `hora` varchar(5) NOT NULL,
  `kilos` decimal(10,2) DEFAULT NULL,
  `brix_manual` decimal(6,2) DEFAULT NULL,
  `temperatura_manual` decimal(6,2) DEFAULT NULL,
  `pol_manual` decimal(6,2) DEFAULT NULL,
  `ph_manual` decimal(5,2) DEFAULT NULL,
  `brix_automatico` decimal(6,2) DEFAULT NULL,
  `pol_automatico` decimal(6,2) DEFAULT NULL,
  `pureza` decimal(6,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_calidadJugo_ultimaHora` (`codigoproceso`,`fechaindustrial`,`hora`)
) ENGINE=InnoDB AUTO_INCREMENT=356543 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calidadmasacocida_xhora`
--

DROP TABLE IF EXISTS `calidadmasacocida_xhora`;
CREATE TABLE IF NOT EXISTS `calidadmasacocida_xhora` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_mssql` int(11) NOT NULL,
  `codigoproceso` varchar(50) NOT NULL,
  `fechaindustrial` date NOT NULL,
  `fecharegistro` datetime DEFAULT NULL,
  `horaregistro` time DEFAULT NULL,
  `hora` time NOT NULL,
  `kilos` decimal(10,2) DEFAULT '0.00',
  `brixmanual` decimal(10,3) DEFAULT '0.000',
  `temperaturamanual` decimal(10,2) DEFAULT '0.00',
  `polmanual` decimal(10,3) DEFAULT '0.000',
  `phmanual` decimal(10,3) DEFAULT '0.000',
  `brixautomatico` decimal(10,3) DEFAULT '0.000',
  `polautomatico` decimal(10,3) DEFAULT '0.000',
  `pureza` decimal(10,3) DEFAULT '0.000',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_masa_cocida` (`id_mssql`,`codigoproceso`)
) ENGINE=InnoDB AUTO_INCREMENT=3881944 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calidadmelaza_xhora`
--

DROP TABLE IF EXISTS `calidadmelaza_xhora`;
CREATE TABLE IF NOT EXISTS `calidadmelaza_xhora` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_mssql` bigint(20) NOT NULL,
  `codigoproceso` varchar(50) NOT NULL,
  `fechaindustrial` date NOT NULL,
  `fecharegistro` datetime DEFAULT NULL,
  `horaregistro` time DEFAULT NULL,
  `hora` time NOT NULL,
  `brixmanual` decimal(10,2) DEFAULT NULL,
  `temperaturamanual` decimal(10,2) DEFAULT NULL,
  `polmanual` decimal(10,2) DEFAULT NULL,
  `brixautomatico` decimal(10,2) DEFAULT NULL,
  `polautomatico` decimal(10,2) DEFAULT NULL,
  `pureza` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_melaza` (`codigoproceso`,`fechaindustrial`,`hora`),
  KEY `idx_fecha` (`fechaindustrial`),
  KEY `idx_proceso` (`codigoproceso`)
) ENGINE=InnoDB AUTO_INCREMENT=56335 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calidadmieles_xhora`
--

DROP TABLE IF EXISTS `calidadmieles_xhora`;
CREATE TABLE IF NOT EXISTS `calidadmieles_xhora` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigoproceso` varchar(100) NOT NULL,
  `fechaindustrial` date NOT NULL,
  `fecharegistro` datetime DEFAULT NULL,
  `horaregistro` time DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `brixmanual` decimal(10,2) DEFAULT '0.00',
  `temperaturamanual` decimal(10,2) DEFAULT '0.00',
  `polmanual` decimal(10,2) DEFAULT '0.00',
  `brixautomatico` decimal(10,2) DEFAULT '0.00',
  `polautomatico` decimal(10,2) DEFAULT '0.00',
  `pureza` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_laboratorio` (`codigoproceso`,`fechaindustrial`,`hora`),
  KEY `idx_miel_datetime` (`fechaindustrial`,`hora`)
) ENGINE=InnoDB AUTO_INCREMENT=1869710 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calidadsulfitado_xhora`
--

DROP TABLE IF EXISTS `calidadsulfitado_xhora`;
CREATE TABLE IF NOT EXISTS `calidadsulfitado_xhora` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_mssql` bigint(20) NOT NULL,
  `codigoproceso` varchar(50) NOT NULL,
  `fechaindustrial` date NOT NULL,
  `fecharegistro` date DEFAULT NULL,
  `horaregistro` time DEFAULT NULL,
  `hora` time NOT NULL,
  `phmanual` decimal(10,2) DEFAULT NULL,
  `PPMS02manual` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sulfitado_mssql` (`id_mssql`)
) ENGINE=InnoDB AUTO_INCREMENT=219621 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consumos_x_hora`
--

DROP TABLE IF EXISTS `consumos_x_hora`;
CREATE TABLE IF NOT EXISTS `consumos_x_hora` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fechaindustrial` date NOT NULL,
  `hora` varchar(8) NOT NULL,
  `gas` decimal(12,2) DEFAULT NULL,
  `kilos` decimal(12,2) DEFAULT NULL,
  `humedad` decimal(6,3) DEFAULT NULL,
  `bolsas` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_fecha_hora` (`fechaindustrial`,`hora`)
) ENGINE=InnoDB AUTO_INCREMENT=83953 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_cania`
--

DROP TABLE IF EXISTS `datos_cania`;
CREATE TABLE IF NOT EXISTS `datos_cania` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fechaindustrial` date NOT NULL,
  `numero_pesada` int(11) NOT NULL,
  `grupo` varchar(250) DEFAULT NULL,
  `caniero` varchar(150) DEFAULT NULL,
  `nro_muestra` int(11) NOT NULL,
  `cania_bruta` decimal(12,3) DEFAULT NULL,
  `trash` decimal(6,2) DEFAULT NULL,
  `trashReal` decimal(8,5) DEFAULT NULL,
  `polporciento` decimal(10,7) DEFAULT NULL,
  `brixporciento` decimal(10,7) DEFAULT NULL,
  `pureza` decimal(10,8) DEFAULT NULL,
  `rendimiento` decimal(10,8) DEFAULT NULL,
  `rendimientoReal` decimal(10,8) DEFAULT NULL,
  `tipo_cania` varchar(50) DEFAULT NULL,
  `fecha_pesada` date DEFAULT NULL,
  `hora_pesada` time DEFAULT NULL,
  `fecha_salida` date DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `nromuestra2` varchar(50) DEFAULT NULL,
  `prepesada` char(1) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `tipo_contrato` varchar(100) DEFAULT NULL,
  `transporte` varchar(100) DEFAULT NULL,
  `fletero` varchar(150) DEFAULT NULL,
  `cosechero` varchar(150) DEFAULT NULL,
  `finca` varchar(150) DEFAULT NULL,
  `nombre_finca` varchar(150) DEFAULT NULL,
  `patente` varchar(150) DEFAULT NULL,
  `observaciones` text,
  `tara` int(11) DEFAULT NULL,
  `fecha_insert` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_datos_cania` (`fechaindustrial`,`numero_pesada`,`nro_muestra`),
  KEY `idx_fechaindustrial` (`fechaindustrial`),
  KEY `idx_fecha_pesada` (`fecha_pesada`)
) ENGINE=InnoDB AUTO_INCREMENT=134842 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_silos`
--

DROP TABLE IF EXISTS `estado_silos`;
CREATE TABLE IF NOT EXISTS `estado_silos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `hora` varchar(5) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `vacio` float DEFAULT NULL,
  `calidad` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_silo_hora` (`fecha`,`hora`,`nombre`),
  KEY `idx_fecha_hora` (`fecha`,`hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Estado por silo cada hora — alimentado desde OPC via Node-RED';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `indicadores_opc`
--

DROP TABLE IF EXISTS `indicadores_opc`;
CREATE TABLE IF NOT EXISTS `indicadores_opc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `velocidad_molino1` float DEFAULT NULL,
  `velocidad_molino6` float DEFAULT NULL,
  `balanza_cinta` float DEFAULT NULL,
  `agua_imbibicion` float DEFAULT NULL,
  `presion_molino6_este` float DEFAULT NULL,
  `presion_molino6_oeste` float DEFAULT NULL,
  `caudal_jugo_clarif` float DEFAULT NULL,
  `nivel_melado_tratado` float DEFAULT NULL,
  `nivel_melado` float DEFAULT NULL,
  `nivel_decantador1` float DEFAULT NULL,
  `nivel_decantador2` float DEFAULT NULL,
  `nivel_decantador3` float DEFAULT NULL,
  `descarga_tachos_1ra` float DEFAULT NULL,
  `descarga_tachos_2da` float DEFAULT NULL,
  `descarga_tachos_3ra` float DEFAULT NULL,
  `contador_bolsas_dia` float DEFAULT NULL,
  `silo_a` float DEFAULT NULL,
  `silo_b` float DEFAULT NULL,
  `silo_c` float DEFAULT NULL,
  `silo_e` float DEFAULT NULL,
  `presion_vapor_directo` float DEFAULT NULL,
  `presion_agua_alim` float DEFAULT NULL,
  `presion_aire` float DEFAULT NULL,
  `caudal_vapor_cald1` float DEFAULT NULL,
  `caudal_vapor_cald2` float DEFAULT NULL,
  `caudal_vapor_cald3` float DEFAULT NULL,
  `caudal_vapor_cald6` float DEFAULT NULL,
  `vapor_total` float DEFAULT NULL,
  `caudal_gas_cald2` float DEFAULT NULL,
  `caudal_gas_cald6` float DEFAULT NULL,
  `temp_calentador` float DEFAULT NULL,
  `presion_vg1` float DEFAULT NULL,
  `nivel_agua_foza` float DEFAULT NULL,
  `potencia_activa_siemens` float DEFAULT NULL,
  `potencia_reactiva_siemens` float DEFAULT NULL,
  `frecuencia_siemens` float DEFAULT NULL,
  `intensidad_siemens` float DEFAULT NULL,
  `potencia_activa_aeg` float DEFAULT NULL,
  `potencia_reactiva_aeg` float DEFAULT NULL,
  `frecuencia_aeg` float DEFAULT NULL,
  `intensidad_aeg` float DEFAULT NULL,
  `potencia_activa_edet` float DEFAULT NULL,
  `intensidad_edet` float DEFAULT NULL,
  `potencia_total` float DEFAULT NULL,
  `cv_trapiche` float DEFAULT NULL,
  `cv_usina_alta` float DEFAULT NULL,
  `cv_destileria` float DEFAULT NULL,
  `cv_aux_total` float DEFAULT NULL,
  `cv_preparacion_cania` float DEFAULT NULL,
  `caudal_vino` float DEFAULT NULL,
  `caudal_alcohol` float DEFAULT NULL,
  `caudal_jugo_dilutor` float DEFAULT NULL,
  `caudal_melaza_dilutor` float DEFAULT NULL,
  `caudal_agua_dilutor` float DEFAULT NULL,
  `caudal_vino_160` float DEFAULT NULL,
  `presion_k2` float DEFAULT NULL,
  `nivel_jugo_pesado` float DEFAULT NULL,
  `presion_vapor_escape` float DEFAULT NULL,
  `temp_agua_alim` float DEFAULT NULL,
  `nivel_jugo_clarificado` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_timestamp` (`timestamp`),
  KEY `idx_ts` (`timestamp`)
) ENGINE=InnoDB AUTO_INCREMENT=7731 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `molienda_xhora`
--

DROP TABLE IF EXISTS `molienda_xhora`;
CREATE TABLE IF NOT EXISTS `molienda_xhora` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fechaindustrial` date NOT NULL,
  `hora` time NOT NULL,
  `gas` decimal(14,2) NOT NULL DEFAULT '0.00',
  `kilos` decimal(14,2) NOT NULL DEFAULT '0.00',
  `humedad` decimal(8,2) NOT NULL DEFAULT '0.00',
  `bolsas` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_fecha_hora` (`fechaindustrial`,`hora`),
  KEY `idx_fecha` (`fechaindustrial`),
  KEY `idx_hora` (`hora`)
) ENGINE=InnoDB AUTO_INCREMENT=6730976 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paradas_destileria`
--

DROP TABLE IF EXISTS `paradas_destileria`;
CREATE TABLE IF NOT EXISTS `paradas_destileria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fechaindustrial` date NOT NULL,
  `desde` time NOT NULL,
  `hasta` time DEFAULT NULL,
  `t_neto` time DEFAULT NULL,
  `origen` varchar(100) DEFAULT NULL,
  `maquina` varchar(100) DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `codigoproceso` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `t_neto_minutos` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_parada` (`fechaindustrial`,`desde`,`codigoproceso`),
  KEY `idx_fecha` (`fechaindustrial`),
  KEY `idx_maquina` (`maquina`)
) ENGINE=InnoDB AUTO_INCREMENT=5619 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paradas_fabrica`
--

DROP TABLE IF EXISTS `paradas_fabrica`;
CREATE TABLE IF NOT EXISTS `paradas_fabrica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fechaindustrial` date NOT NULL,
  `desde` time NOT NULL,
  `hasta` time DEFAULT NULL,
  `t_neto` varchar(8) DEFAULT NULL,
  `origen` varchar(50) DEFAULT NULL,
  `maquina` varchar(100) DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `id_mssql` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `t_neto_minutos` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_id_mssql` (`id_mssql`),
  KEY `idx_fecha` (`fechaindustrial`),
  KEY `idx_maquina` (`maquina`),
  KEY `idx_motivo` (`motivo`(191))
) ENGINE=InnoDB AUTO_INCREMENT=292830 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_sistema`
--

DROP TABLE IF EXISTS `usuarios_sistema`;
CREATE TABLE IF NOT EXISTS `usuarios_sistema` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `apellido` varchar(150) NOT NULL,
  `celular` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `cargo` varchar(100) NOT NULL,
  `area` varchar(100) NOT NULL,
  `turno` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `celular` (`celular`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_usuarios_celular` (`celular`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
