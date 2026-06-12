-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql
-- Tiempo de generación: 11-05-2026 a las 20:19:56
-- Versión del servidor: 8.4.9
-- Versión de PHP: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
-- Estructura de tabla para la tabla `calidadAzucar_xHora`
--

CREATE TABLE `calidadAzucar_xHora` (
  `id` int NOT NULL,
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calidadBagazo_xHora`
--

CREATE TABLE `calidadBagazo_xHora` (
  `id` int NOT NULL,
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calidadCachaza_xHora`
--

CREATE TABLE `calidadCachaza_xHora` (
  `id_mssql` int NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calidadJugo_xHora`
--

CREATE TABLE `calidadJugo_xHora` (
  `id` int NOT NULL,
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calidadMasaCocida_xHora`
--

CREATE TABLE `calidadMasaCocida_xHora` (
  `id` bigint NOT NULL,
  `id_mssql` int NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calidadMelaza_xHora`
--

CREATE TABLE `calidadMelaza_xHora` (
  `id` bigint NOT NULL,
  `id_mssql` bigint NOT NULL,
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calidadMieles_xHora`
--

CREATE TABLE `calidadMieles_xHora` (
  `id` int NOT NULL,
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calidadSulfitado_xHora`
--

CREATE TABLE `calidadSulfitado_xHora` (
  `id` bigint NOT NULL,
  `id_mssql` bigint NOT NULL,
  `codigoproceso` varchar(50) NOT NULL,
  `fechaindustrial` date NOT NULL,
  `fecharegistro` date DEFAULT NULL,
  `horaregistro` time DEFAULT NULL,
  `hora` time NOT NULL,
  `phmanual` decimal(10,2) DEFAULT NULL,
  `PPMS02manual` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consumos_x_hora`
--

CREATE TABLE `consumos_x_hora` (
  `id` int UNSIGNED NOT NULL,
  `fechaindustrial` date NOT NULL,
  `hora` varchar(8) NOT NULL,
  `gas` decimal(12,2) DEFAULT NULL,
  `kilos` decimal(12,2) DEFAULT NULL,
  `humedad` decimal(6,3) DEFAULT NULL,
  `bolsas` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_Cania`
--

CREATE TABLE `datos_Cania` (
  `id` bigint NOT NULL,
  `fechaindustrial` date NOT NULL,
  `numero_pesada` int NOT NULL,
  `grupo` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `caniero` varchar(150) DEFAULT NULL,
  `nro_muestra` int NOT NULL,
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
  `patente` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `observaciones` text,
  `tara` int DEFAULT NULL,
  `fecha_insert` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_silos`
--

CREATE TABLE `estado_silos` (
  `id` int NOT NULL,
  `fecha` date NOT NULL,
  `hora` varchar(5) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `vacio` float DEFAULT NULL,
  `calidad` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Estado por silo cada hora — alimentado desde OPC via Node-RED';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `indicadores_opc`
--

CREATE TABLE `indicadores_opc` (
  `id` int NOT NULL,
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
  `nivel_jugo_clarificado` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `molienda_xHora`
--

CREATE TABLE `molienda_xHora` (
  `id` bigint NOT NULL,
  `fechaindustrial` date NOT NULL,
  `hora` time NOT NULL,
  `gas` decimal(14,2) NOT NULL DEFAULT '0.00',
  `kilos` decimal(14,2) NOT NULL DEFAULT '0.00',
  `humedad` decimal(8,2) NOT NULL DEFAULT '0.00',
  `bolsas` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paradas_destileria`
--

CREATE TABLE `paradas_destileria` (
  `id` int NOT NULL,
  `fechaindustrial` date NOT NULL,
  `desde` time NOT NULL,
  `hasta` time DEFAULT NULL,
  `t_neto` time DEFAULT NULL,
  `origen` varchar(100) DEFAULT NULL,
  `maquina` varchar(100) DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `codigoproceso` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `t_neto_minutos` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paradas_fabrica`
--

CREATE TABLE `paradas_fabrica` (
  `id` int NOT NULL,
  `fechaindustrial` date NOT NULL,
  `desde` time NOT NULL,
  `hasta` time DEFAULT NULL,
  `t_neto` varchar(8) DEFAULT NULL,
  `origen` varchar(50) DEFAULT NULL,
  `maquina` varchar(100) DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `id_mssql` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `t_neto_minutos` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_sistema`
--

CREATE TABLE `usuarios_sistema` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `apellido` varchar(150) NOT NULL,
  `celular` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `cargo` varchar(100) NOT NULL,
  `area` varchar(100) NOT NULL,
  `turno` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calidadAzucar_xHora`
--
ALTER TABLE `calidadAzucar_xHora`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_calidad` (`codigoproceso`,`fechaindustrial`,`hora`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_proceso` (`codigoproceso`);

--
-- Indices de la tabla `calidadBagazo_xHora`
--
ALTER TABLE `calidadBagazo_xHora`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_bagazo` (`codigoproceso`,`fechaindustrial`,`hora`),
  ADD KEY `idx_fecha` (`fechaindustrial`),
  ADD KEY `idx_proceso` (`codigoproceso`);

--
-- Indices de la tabla `calidadCachaza_xHora`
--
ALTER TABLE `calidadCachaza_xHora`
  ADD PRIMARY KEY (`id_mssql`),
  ADD UNIQUE KEY `uk_cachaza_proc_fecha_hora` (`codigoproceso`,`fechaindustrial`,`hora`);

--
-- Indices de la tabla `calidadJugo_xHora`
--
ALTER TABLE `calidadJugo_xHora`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_calidadJugo_ultimaHora` (`codigoproceso`,`fechaindustrial`,`hora`);

--
-- Indices de la tabla `calidadMasaCocida_xHora`
--
ALTER TABLE `calidadMasaCocida_xHora`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_masa_cocida` (`id_mssql`,`codigoproceso`);

--
-- Indices de la tabla `calidadMelaza_xHora`
--
ALTER TABLE `calidadMelaza_xHora`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_melaza` (`codigoproceso`,`fechaindustrial`,`hora`),
  ADD KEY `idx_fecha` (`fechaindustrial`),
  ADD KEY `idx_proceso` (`codigoproceso`);

--
-- Indices de la tabla `calidadMieles_xHora`
--
ALTER TABLE `calidadMieles_xHora`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_laboratorio` (`codigoproceso`,`fechaindustrial`,`hora`),
  ADD KEY `idx_miel_datetime` (`fechaindustrial`,`hora`);

--
-- Indices de la tabla `calidadSulfitado_xHora`
--
ALTER TABLE `calidadSulfitado_xHora`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_sulfitado_mssql` (`id_mssql`);

--
-- Indices de la tabla `consumos_x_hora`
--
ALTER TABLE `consumos_x_hora`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fecha_hora` (`fechaindustrial`,`hora`);

--
-- Indices de la tabla `datos_Cania`
--
ALTER TABLE `datos_Cania`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_datos_cania` (`fechaindustrial`,`numero_pesada`,`nro_muestra`);

--
-- Indices de la tabla `estado_silos`
--
ALTER TABLE `estado_silos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_silo_hora` (`fecha`,`hora`,`nombre`),
  ADD KEY `idx_fecha_hora` (`fecha`,`hora`);

--
-- Indices de la tabla `indicadores_opc`
--
ALTER TABLE `indicadores_opc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_timestamp` (`timestamp`),
  ADD KEY `idx_ts` (`timestamp`);

--
-- Indices de la tabla `molienda_xHora`
--
ALTER TABLE `molienda_xHora`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fecha_hora` (`fechaindustrial`,`hora`),
  ADD KEY `idx_fecha` (`fechaindustrial`),
  ADD KEY `idx_hora` (`hora`);

--
-- Indices de la tabla `paradas_destileria`
--
ALTER TABLE `paradas_destileria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_parada` (`fechaindustrial`,`desde`,`codigoproceso`),
  ADD KEY `idx_fecha` (`fechaindustrial`),
  ADD KEY `idx_maquina` (`maquina`);

--
-- Indices de la tabla `paradas_fabrica`
--
ALTER TABLE `paradas_fabrica`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_id_mssql` (`id_mssql`),
  ADD KEY `idx_fecha` (`fechaindustrial`),
  ADD KEY `idx_maquina` (`maquina`),
  ADD KEY `idx_motivo` (`motivo`(191));

--
-- Indices de la tabla `usuarios_sistema`
--
ALTER TABLE `usuarios_sistema`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `celular` (`celular`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_usuarios_celular` (`celular`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calidadAzucar_xHora`
--
ALTER TABLE `calidadAzucar_xHora`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `calidadBagazo_xHora`
--
ALTER TABLE `calidadBagazo_xHora`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `calidadJugo_xHora`
--
ALTER TABLE `calidadJugo_xHora`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `calidadMasaCocida_xHora`
--
ALTER TABLE `calidadMasaCocida_xHora`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `calidadMelaza_xHora`
--
ALTER TABLE `calidadMelaza_xHora`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `calidadMieles_xHora`
--
ALTER TABLE `calidadMieles_xHora`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `calidadSulfitado_xHora`
--
ALTER TABLE `calidadSulfitado_xHora`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `consumos_x_hora`
--
ALTER TABLE `consumos_x_hora`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `datos_Cania`
--
ALTER TABLE `datos_Cania`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estado_silos`
--
ALTER TABLE `estado_silos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `indicadores_opc`
--
ALTER TABLE `indicadores_opc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `molienda_xHora`
--
ALTER TABLE `molienda_xHora`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `paradas_destileria`
--
ALTER TABLE `paradas_destileria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `paradas_fabrica`
--
ALTER TABLE `paradas_fabrica`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios_sistema`
--
ALTER TABLE `usuarios_sistema`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
