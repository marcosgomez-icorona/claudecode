-- ============================================================================
-- 03_crear_lookups.sql — Tablas de referencia para Despachos Pendientes
-- Base: corona_aux (MySQL)
-- Versión: 1.4.0
-- ============================================================================
-- Estas tablas normalizan catálogos usados por el dashboard y el agente.
-- Se pueblan con datos iniciales; el agente de despachos puede actualizarlas.
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. Estados de clasificación del agente
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `despachos_lookup_estados` (
  `id` TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Valor en clasificacionAgente',
  `etiqueta` VARCHAR(60) NOT NULL COMMENT 'Label visible en UI',
  `descripcion` VARCHAR(300) NULL COMMENT 'Qué significa este estado',
  `color_css` VARCHAR(7) NOT NULL DEFAULT '#6B7280' COMMENT 'Color de badge en UI',
  `color_bg` VARCHAR(7) NOT NULL DEFAULT '#F3F4F6' COMMENT 'Fondo del badge',
  `orden` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Orden en filtros',
  `activo` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Visible en UI',
  `creado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `despachos_lookup_estados` (`codigo`, `etiqueta`, `descripcion`, `color_css`, `color_bg`, `orden`) VALUES
('APTO_PARA_PROGRAMAR',       'Apto para programar',       'Remito validado, listo para vincular factura',           '#0F6E56', '#E1F5EE', 1),
('PENDIENTE_VALIDACION',      'Pendiente de validación',   'Falta verificar datos del remito contra OC o constancia', '#BA7517', '#FAEEDA', 2),
('REQUIERE_APROBACION_HUMANA','Requiere aprobación humana','Caso atípico — necesita revisión manual antes de facturar','#92400E', '#FEF3C7', 3),
('BLOQUEADO',                 'Bloqueado',                 'Remito con problemas — no facturable hasta resolver',     '#A32D2D', '#FCEBEB', 4);

-- ----------------------------------------------------------------------------
-- 2. Transportistas
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `despachos_lookup_transportistas` (
  `id` SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(120) NOT NULL UNIQUE COMMENT 'Razón social completa',
  `nombre_corto` VARCHAR(50) NULL COMMENT 'Nombre abreviado para tabla',
  `cuit` VARCHAR(13) NULL COMMENT 'CUIT del transportista (si aplica)',
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `creado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `despachos_lookup_transportistas` (`nombre`, `nombre_corto`, `cuit`) VALUES
('AGÜERO ABEL EDGARDO',           'AGÜERO A.E.',        NULL),
('BONAVIA SA',                    'BONAVIA SA',         '30500123456'),
('CORREA HNOS SRL',               'CORREA HNOS',        '30777666555'),
('EL RAPIDO SRL',                 'EL RAPIDO',          '30777888999'),
('LAUMANN SRL',                   'LAUMANN SRL',        '30711222333'),
('LOGISTICA D´CANARIO SRL',       'LOG. CANARIO',       '30788999000'),
('LUCI GERMAN',                   'LUCI G.',            '20283295878'),
('MORANO SRL',                    'MORANO SRL',         '30765432100'),
('TRANSPORTE DEL LITORAL SA',     'TTE. LITORAL',       '30555666777'),
('TRANSPORTES NORTE SRL',         'TTES. NORTE',        '30777888111'),
('VEL SARBO SRL',                 'VEL SARBO',          '30711875995');

-- ----------------------------------------------------------------------------
-- 3. Clientes
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `despachos_lookup_clientes` (
  `id` SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `razon_social` VARCHAR(150) NOT NULL UNIQUE,
  `nombre_corto` VARCHAR(50) NULL,
  `cuit` VARCHAR(13) NULL,
  `localidad` VARCHAR(100) NULL,
  `provincia` VARCHAR(60) NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `creado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `despachos_lookup_clientes` (`razon_social`, `nombre_corto`, `cuit`, `localidad`, `provincia`) VALUES
('ALIMENTOS DEL VALLE S.A.',         'ALIM. DEL VALLE',   '30685432109', 'Godoy Cruz',               'Mendoza'),
('COMERCIALIZADORA DEL PLATA SA',    'COM. DEL PLATA',    '30555666777', 'Rosario',                   'Santa Fe'),
('D&G SOCIEDAD DE HECHO',            'D&G S.H.',          '30711875995', 'Martinez',                  'Buenos Aires'),
('DISTRIBUIDORA NORTE SRL',          'DIST. NORTE',       '30711222333', 'San Miguel',                'Tucumán'),
('LEDESMA S.A.A.I.',                 'LEDESMA',           '30501250305', 'Gral. Libertador San Martin','Jujuy'),
('LUCI GERMAN',                      'LUCI G.',           '20283295878', 'Lincoln',                   'Buenos Aires'),
('MAYORISTA DEL CENTRO SRL',         'MAY. DEL CENTRO',   '30788999000', 'Río Cuarto',                'Córdoba'),
('STURTZ ESTEBAN RAUL',              'STURTZ E.R.',       '20123456789', 'Saladillo',                 'Buenos Aires'),
('TOMASI JAVIER ANGEL EFRAIN',       'TOMASI J.A.E.',     '20238188890', 'Saladillo',                 'Buenos Aires'),
('VERAMOR DE MARMOL S.R.L.',         'VERAMOR SRL',       '30707690034', 'Ferreyra',                  'Córdoba');

-- ----------------------------------------------------------------------------
-- 4. Productos (tipos de azúcar despachados)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `despachos_lookup_productos` (
  `id` TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL UNIQUE,
  `nombre_corto` VARCHAR(40) NULL,
  `color_css` VARCHAR(7) NOT NULL DEFAULT '#6B7280' COMMENT 'Color del badge en UI',
  `orden` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `creado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `despachos_lookup_productos` (`nombre`, `nombre_corto`, `color_css`, `orden`) VALUES
('AZUCAR COMUN TIPO A',          'Común A',  '#1D9E75', 1),
('AZUCAR CRUDO',                 'Crudo',    '#185FA5', 2),
('AZUCAR GRADO 4 EXPO CHILE',    'Grado 4',  '#BA7517', 3);

-- ----------------------------------------------------------------------------
-- 5. Vista consolidada para el dashboard (unifica lookups)
-- ----------------------------------------------------------------------------
-- Esta vista se puede usar desde Node-RED para devolver datos ya enriquecidos
-- al frontend, evitando múltiples JOINs en cada consulta.

CREATE OR REPLACE VIEW `vw_despachos_pendientes_enriched` AS
SELECT
  dp.remito,
  dp.fecha,
  dp.cliente,
  dp.cuit,
  dp.destino,
  dp.producto,
  dp.cantidad,
  dp.unidad,
  dp.cantidad2,
  dp.unidad2,
  dp.precio,
  dp.totalItem,
  dp.transportista,
  dp.chofer,
  dp.patente,
  dp.chasis,
  dp.guia,
  dp.orden,
  dp.observaciones,
  dp.clasificacionAgente,
  dp.sync_run_uuid,
  COALESCE(le.etiqueta, 'Sin clasificar')   AS clasificacion_etiqueta,
  COALESCE(le.color_css, '#6B7280')         AS clasificacion_color,
  COALESCE(le.color_bg, '#F3F4F6')          AS clasificacion_bg,
  COALESCE(lc.nombre_corto, dp.cliente)     AS cliente_corto,
  COALESCE(lt.nombre_corto, dp.transportista) AS transportista_corto,
  COALESCE(lp.nombre_corto, dp.producto)    AS producto_corto,
  COALESCE(lp.color_css, '#6B7280')         AS producto_color
FROM despachos_pendientes_cache dp
LEFT JOIN despachos_lookup_estados le        ON dp.clasificacionAgente = le.codigo
LEFT JOIN despachos_lookup_clientes lc       ON dp.cliente = lc.razon_social
LEFT JOIN despachos_lookup_transportistas lt ON dp.transportista = lt.nombre
LEFT JOIN despachos_lookup_productos lp      ON dp.producto = lp.nombre;
