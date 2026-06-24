-- ============================================================================
-- 04_crear_agente.sql — Agente de Despachos: auditoría + reglas
-- Base: corona_aux (MySQL)
-- Versión: 1.5.0
-- ============================================================================
-- El agente lee remitos de despachos_pendientes_cache, aplica reglas de
-- clasificación, y escribe el resultado en clasificacionAgente.
-- Cada decisión queda registrada en despachos_agente_log para trazabilidad.
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. Log de decisiones del agente (auditoría completa)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `despachos_agente_log` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `run_uuid` VARCHAR(36) NOT NULL COMMENT 'UUID de la ejecución del agente',
  `remito` VARCHAR(50) NOT NULL COMMENT 'Número de remito clasificado',
  `estado_anterior` VARCHAR(50) NULL COMMENT 'Clasificación previa (si había)',
  `estado_nuevo` VARCHAR(50) NOT NULL COMMENT 'Clasificación asignada por el agente',
  `motivo` VARCHAR(300) NOT NULL COMMENT 'Regla que disparó la decisión',
  `puntaje` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Score de confianza (0-100)',
  `regla_id` SMALLINT UNSIGNED NULL COMMENT 'FK a despachos_reglas_clasificacion',
  `datos_snapshot` JSON NULL COMMENT 'Snapshot del remito al momento de clasificar',
  `usuario` VARCHAR(50) NOT NULL DEFAULT 'AGENTE_DESPACHOS',
  `creado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_run_uuid` (`run_uuid`),
  INDEX `idx_remito` (`remito`),
  INDEX `idx_estado_nuevo` (`estado_nuevo`),
  INDEX `idx_creado` (`creado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. Reglas de clasificación configurables
-- ----------------------------------------------------------------------------
-- Cada regla evalúa una condición y asigna un estado.
-- Las reglas se ejecutan en orden (campo `orden`).
-- La primera regla que dispara gana (short-circuit).
-- Si ninguna regla dispara → APTO_PARA_PROGRAMAR (default).
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `despachos_reglas_clasificacion` (
  `id` SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(80) NOT NULL UNIQUE COMMENT 'Nombre descriptivo de la regla',
  `descripcion` VARCHAR(300) NULL COMMENT 'Qué evalúa esta regla',
  `estado_resultante` VARCHAR(50) NOT NULL COMMENT 'Estado asignado si la regla dispara',
  `condicion_tipo` VARCHAR(30) NOT NULL DEFAULT 'COMPUESTA' COMMENT 'Tipo: FALTANTE, RANGO, EXIGE_VALOR, COMPUESTA',
  `campo_evaluar` VARCHAR(60) NULL COMMENT 'Campo del remito a evaluar',
  `operador` VARCHAR(10) NULL COMMENT 'Operador: ES_NULO, ES_VACIO, MENOR_QUE, MAYOR_QUE, CONTIENE, NO_CONTIENE',
  `valor_referencia` VARCHAR(100) NULL COMMENT 'Valor de referencia para la comparación',
  `motivo` VARCHAR(200) NOT NULL COMMENT 'Texto que se graba en el log cuando dispara',
  `puntaje` TINYINT UNSIGNED NOT NULL DEFAULT 90 COMMENT 'Score de confianza (0-100)',
  `orden` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Orden de evaluación (menor primero)',
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `creado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. Reglas por defecto (poblado inicial)
-- ----------------------------------------------------------------------------
-- Orden: primero BLOQUEADOS (críticos), luego REQUIERE_APROBACION,
-- luego PENDIENTE_VALIDACION. Lo que sobrevive → APTO_PARA_PROGRAMAR.

INSERT INTO `despachos_reglas_clasificacion` (`nombre`, `descripcion`, `estado_resultante`, `condicion_tipo`, `campo_evaluar`, `operador`, `valor_referencia`, `motivo`, `puntaje`, `orden`) VALUES
-- BLOQUEADOS (datos críticos faltantes o inválidos)
('sin_cliente',       'Remito sin cliente',                  'BLOQUEADO',  'FALTANTE', 'cliente',    'ES_NULO',  NULL, 'Cliente no informado — dato crítico', 100, 10),
('sin_cuit',          'Remito sin CUIT',                     'BLOQUEADO',  'FALTANTE', 'cuit',       'ES_NULO',  NULL, 'CUIT no informado — dato crítico', 100, 11),
('sin_producto',      'Remito sin producto',                 'BLOQUEADO',  'FALTANTE', 'producto',   'ES_NULO',  NULL, 'Producto no informado — dato crítico', 100, 12),
('sin_cantidad',      'Remito sin cantidad',                 'BLOQUEADO',  'FALTANTE', 'cantidad',   'ES_NULO',  NULL, 'Cantidad no informada — dato crítico', 100, 13),
('cantidad_cero',     'Cantidad = 0',                        'BLOQUEADO',  'RANGO',    'cantidad',   'MENOR_QUE', '1', 'Cantidad = 0 — inconsistente', 100, 14),
('precio_nulo',       'Precio no informado',                 'BLOQUEADO',  'FALTANTE', 'precio',     'ES_NULO',  NULL, 'Precio no informado — dato crítico', 100, 15),
('precio_cero',       'Precio = 0',                          'BLOQUEADO',  'RANGO',    'precio',     'MENOR_QUE', '1', 'Precio = 0 — inconsistente', 100, 16),
('precio_excesivo',   'Precio > $2000 (posible error)',      'BLOQUEADO',  'RANGO',    'precio',     'MAYOR_QUE', '2000', 'Precio > $2000 — posible error de carga', 85, 17),

-- REQUIEREN APROBACIÓN HUMANA (casos especiales)
('exportacion',       'Producto de exportación',             'REQUIERE_APROBACION_HUMANA', 'CONTIENE', 'producto', 'CONTIENE', 'EXPO', 'Producto de exportación — revisión obligatoria', 95, 30),
('monto_alto',        'Importe > $400.000',                  'REQUIERE_APROBACION_HUMANA', 'RANGO', 'totalItem', 'MAYOR_QUE', '400000', 'Importe elevado (>$400K) — requiere autorización', 80, 31),
('sin_transportista', 'Falta transportista en exportación',   'REQUIERE_APROBACION_HUMANA', 'FALTANTE', 'transportista', 'ES_NULO', NULL, 'Falta transportista — revisar logística', 70, 32),

-- PENDIENTES DE VALIDACIÓN (datos secundarios faltantes)
('sin_guia',          'Falta número de guía',                 'PENDIENTE_VALIDACION', 'FALTANTE', 'guia',  'ES_NULO', NULL, 'Falta número de guía', 85, 50),
('sin_observaciones', 'Falta observaciones',                  'PENDIENTE_VALIDACION', 'FALTANTE', 'observaciones', 'ES_VACIO', NULL, 'Falta observaciones del remito', 70, 51),
('sin_destino',       'Falta destino de entrega',             'PENDIENTE_VALIDACION', 'FALTANTE', 'destino', 'ES_NULO', NULL, 'Falta destino de entrega', 80, 52),
('sin_chofer',        'Falta nombre del chofer',              'PENDIENTE_VALIDACION', 'FALTANTE', 'chofer', 'ES_NULO', NULL, 'Falta nombre del chofer', 65, 53),
('sin_patente',       'Falta patente del vehículo',           'PENDIENTE_VALIDACION', 'FALTANTE', 'patente', 'ES_NULO', NULL, 'Falta patente del vehículo', 65, 54);

-- ----------------------------------------------------------------------------
-- 4. Vista de estadísticas del agente
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW `vw_agente_estadisticas` AS
SELECT
  DATE(creado) AS fecha,
  COUNT(*) AS total_clasificados,
  SUM(CASE WHEN estado_nuevo = 'APTO_PARA_PROGRAMAR' THEN 1 ELSE 0 END) AS aptos,
  SUM(CASE WHEN estado_nuevo = 'PENDIENTE_VALIDACION' THEN 1 ELSE 0 END) AS pendientes,
  SUM(CASE WHEN estado_nuevo = 'REQUIERE_APROBACION_HUMANA' THEN 1 ELSE 0 END) AS requieren_aprob,
  SUM(CASE WHEN estado_nuevo = 'BLOQUEADO' THEN 1 ELSE 0 END) AS bloqueados,
  SUM(CASE WHEN estado_anterior IS NULL THEN 1 ELSE 0 END) AS nuevos,
  SUM(CASE WHEN estado_anterior IS NOT NULL AND estado_anterior != estado_nuevo THEN 1 ELSE 0 END) AS cambiaron,
  SUM(CASE WHEN estado_anterior = estado_nuevo THEN 1 ELSE 0 END) AS sin_cambio
FROM despachos_agente_log
GROUP BY DATE(creado)
ORDER BY fecha DESC;

-- ----------------------------------------------------------------------------
-- 5. Procedimiento para ejecutar el agente (opcional — puede correr en Node-RED)
-- ----------------------------------------------------------------------------
-- Este SP recorre los remitos pendientes, aplica reglas y actualiza la cache.
-- Útil si se quiere ejecutar el agente desde el scheduler de MySQL o desde
-- Node-RED con un solo CALL.

DELIMITER //

CREATE PROCEDURE `sp_agente_clasificar`(
  IN p_run_uuid VARCHAR(36),
  IN p_days_back INT
)
BEGIN
  DECLARE v_remito VARCHAR(50);
  DECLARE v_estado_anterior VARCHAR(50);
  DECLARE v_estado_nuevo VARCHAR(50);
  DECLARE v_motivo VARCHAR(300);
  DECLARE v_regla_id SMALLINT;
  DECLARE v_puntaje TINYINT;
  DECLARE v_done INT DEFAULT 0;

  -- Cursor: remitos sin clasificar o con clasificación vencida
  DECLARE cur CURSOR FOR
    SELECT remito, clasificacionAgente
    FROM despachos_pendientes_cache
    WHERE 1=1
      AND (clasificacionAgente IS NULL OR clasificacionAgente = '')
    ORDER BY fecha DESC;

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;

  OPEN cur;

  read_loop: LOOP
    FETCH cur INTO v_remito, v_estado_anterior;
    IF v_done THEN LEAVE read_loop; END IF;

    -- Buscar primera regla que dispare (ordenado por `orden`)
    SET v_regla_id = NULL;
    SET v_estado_nuevo = 'APTO_PARA_PROGRAMAR';
    SET v_motivo = 'Regla default: todos los datos verificados';
    SET v_puntaje = 75;

    SELECT r.id, r.estado_resultante, r.motivo, r.puntaje
    INTO v_regla_id, v_estado_nuevo, v_motivo, v_puntaje
    FROM despachos_reglas_clasificacion r
    WHERE r.activo = 1
      AND (
        (r.condicion_tipo = 'FALTANTE' AND r.operador = 'ES_NULO' AND
          (SELECT CASE WHEN r.campo_evaluar = 'cliente' THEN (SELECT cliente FROM despachos_pendientes_cache WHERE remito = v_remito)
                       WHEN r.campo_evaluar = 'cuit' THEN (SELECT cuit FROM despachos_pendientes_cache WHERE remito = v_remito)
                       WHEN r.campo_evaluar = 'producto' THEN (SELECT producto FROM despachos_pendientes_cache WHERE remito = v_remito)
                       WHEN r.campo_evaluar = 'cantidad' THEN (SELECT CAST(cantidad AS CHAR) FROM despachos_pendientes_cache WHERE remito = v_remito)
                       WHEN r.campo_evaluar = 'precio' THEN (SELECT CAST(precio AS CHAR) FROM despachos_pendientes_cache WHERE remito = v_remito)
                       WHEN r.campo_evaluar = 'transportista' THEN (SELECT transportista FROM despachos_pendientes_cache WHERE remito = v_remito)
                       WHEN r.campo_evaluar = 'guia' THEN (SELECT guia FROM despachos_pendientes_cache WHERE remito = v_remito)
                       WHEN r.campo_evaluar = 'destino' THEN (SELECT destino FROM despachos_pendientes_cache WHERE remito = v_remito)
                       WHEN r.campo_evaluar = 'chofer' THEN (SELECT chofer FROM despachos_pendientes_cache WHERE remito = v_remito)
                       WHEN r.campo_evaluar = 'patente' THEN (SELECT patente FROM despachos_pendientes_cache WHERE remito = v_remito)
                  END) IS NULL)
        )
        OR
        (r.condicion_tipo = 'FALTANTE' AND r.operador = 'ES_VACIO' AND
          (SELECT CASE WHEN r.campo_evaluar = 'observaciones' THEN (SELECT observaciones FROM despachos_pendientes_cache WHERE remito = v_remito)
                  END) = '')
        )
        OR
        (r.condicion_tipo = 'RANGO' AND r.operador = 'MENOR_QUE' AND
          CAST((SELECT CASE WHEN r.campo_evaluar = 'cantidad' THEN (SELECT CAST(cantidad AS CHAR) FROM despachos_pendientes_cache WHERE remito = v_remito)
                            WHEN r.campo_evaluar = 'precio' THEN (SELECT CAST(precio AS CHAR) FROM despachos_pendientes_cache WHERE remito = v_remito)
                            WHEN r.campo_evaluar = 'totalItem' THEN (SELECT CAST(totalItem AS CHAR) FROM despachos_pendientes_cache WHERE remito = v_remito)
                       END) AS DECIMAL(18,2)) < CAST(r.valor_referencia AS DECIMAL(18,2))
        )
        OR
        (r.condicion_tipo = 'RANGO' AND r.operador = 'MAYOR_QUE' AND
          CAST((SELECT CASE WHEN r.campo_evaluar = 'precio' THEN (SELECT CAST(precio AS CHAR) FROM despachos_pendientes_cache WHERE remito = v_remito)
                            WHEN r.campo_evaluar = 'totalItem' THEN (SELECT CAST(totalItem AS CHAR) FROM despachos_pendientes_cache WHERE remito = v_remito)
                       END) AS DECIMAL(18,2)) > CAST(r.valor_referencia AS DECIMAL(18,2))
        )
        OR
        (r.condicion_tipo = 'CONTIENE' AND r.operador = 'CONTIENE' AND
          (SELECT producto FROM despachos_pendientes_cache WHERE remito = v_remito) LIKE CONCAT('%', r.valor_referencia, '%')
        )
      )
    ORDER BY r.orden ASC
    LIMIT 1;

    -- Actualizar cache
    UPDATE despachos_pendientes_cache
    SET clasificacionAgente = v_estado_nuevo
    WHERE remito = v_remito;

    -- Registrar auditoría
    INSERT INTO despachos_agente_log
      (run_uuid, remito, estado_anterior, estado_nuevo, motivo, puntaje, regla_id)
    VALUES
      (p_run_uuid, v_remito, v_estado_anterior, v_estado_nuevo, v_motivo, v_puntaje, v_regla_id);

  END LOOP;

  CLOSE cur;

  -- Devolver resumen
  SELECT
    p_run_uuid AS run_uuid,
    COUNT(*) AS total_procesados,
    SUM(CASE WHEN v_estado_nuevo = 'APTO_PARA_PROGRAMAR' THEN 1 ELSE 0 END) AS aptos,
    SUM(CASE WHEN v_estado_nuevo = 'PENDIENTE_VALIDACION' THEN 1 ELSE 0 END) AS pendientes,
    SUM(CASE WHEN v_estado_nuevo = 'REQUIERE_APROBACION_HUMANA' THEN 1 ELSE 0 END) AS requieren_aprob,
    SUM(CASE WHEN v_estado_nuevo = 'BLOQUEADO' THEN 1 ELSE 0 END) AS bloqueados
  FROM despachos_agente_log
  WHERE run_uuid = p_run_uuid;

END//

DELIMITER ;
