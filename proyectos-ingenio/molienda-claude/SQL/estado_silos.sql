-- ====================================================
-- TABLA: estado_silos
-- Almacena el estado por silo (% vacío, calidad) por fecha/hora
-- Poblado por Node-RED desde columnas silo_a/b/c/e de indicadores_opc
-- ====================================================
CREATE TABLE IF NOT EXISTS `estado_silos` (
  `id`     INT NOT NULL AUTO_INCREMENT,
  `fecha`  DATE        NOT NULL,
  `hora`   VARCHAR(5)  NOT NULL,   -- formato HH:MM
  `nombre` VARCHAR(20) NOT NULL,   -- 'Silo A', 'Silo B', 'Silo C', 'Silo E'
  `vacio`  FLOAT       DEFAULT NULL,  -- % vacío (0-100)
  `calidad` FLOAT      DEFAULT NULL,  -- indicador numérico de calidad
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_silo_hora` (`fecha`, `hora`, `nombre`),
  INDEX `idx_fecha_hora` (`fecha`, `hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Estado por silo cada hora — alimentado desde OPC via Node-RED';


-- ====================================================
-- FLUJO SUGERIDO (Node-RED): poblar estado_silos desde indicadores_opc
-- Inject cada 60s → query SELECT silo_a, silo_b, silo_c, silo_e FROM indicadores_opc
--   ORDER BY timestamp DESC LIMIT 1
-- → function que arma 4 filas (una por silo)
-- → MySQL INSERT INTO estado_silos ... ON DUPLICATE KEY UPDATE vacio=VALUES(vacio)
-- ====================================================

-- Datos de prueba para verificar la query del controller:
-- INSERT INTO estado_silos (fecha, hora, nombre, vacio, calidad) VALUES
--   (CURDATE(), DATE_FORMAT(NOW(),'%H:%i'), 'Silo A', 45.2, 98.5),
--   (CURDATE(), DATE_FORMAT(NOW(),'%H:%i'), 'Silo B', 78.1, 97.2),
--   (CURDATE(), DATE_FORMAT(NOW(),'%H:%i'), 'Silo C', 12.0, 99.1),
--   (CURDATE(), DATE_FORMAT(NOW(),'%H:%i'), 'Silo E', 60.5, 98.8);
