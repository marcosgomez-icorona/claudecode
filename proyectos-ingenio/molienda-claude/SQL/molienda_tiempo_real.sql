-- ============================================================
-- Molienda Tiempo Real — DDL
-- Fuente: pr_ezi_movimientos (Calipso SQL Server)
-- Actualizado cada 2 min por Node-RED
-- ============================================================

-- Kilos y pesadas de caña por hora del día actual
-- hora_inicio: hora real de la pesada (ej: '10:00' para pesadas 10:xx-10:59)
CREATE TABLE IF NOT EXISTS `molienda_tiempo_real` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `fecha`          DATE        NOT NULL,
    `hora_inicio`    VARCHAR(5)  NOT NULL,
    `neto_cana_kg`   INT         NOT NULL DEFAULT 0,
    `pesadas_count`  INT         NOT NULL DEFAULT 0,
    `actualizado`    DATETIME    NOT NULL,
    UNIQUE KEY `uk_fecha_hora` (`fecha`, `hora_inicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Estado en tiempo real (fila única id=1)
-- pre_ingreso:  camiones co/mon primera pesada hecha, esperando tara de salida
-- ultima_pesada: HH:MM:SS de la última pesada completada del día
CREATE TABLE IF NOT EXISTS `molienda_estado_actual` (
    `id`            INT         NOT NULL DEFAULT 1,
    `pre_ingreso`   INT         NOT NULL DEFAULT 0,
    `ultima_pesada` VARCHAR(8)  DEFAULT NULL,
    `actualizado`   DATETIME    NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `molienda_estado_actual` (`id`, `pre_ingreso`, `ultima_pesada`, `actualizado`)
VALUES (1, 0, NULL, NOW());
