-- ============================================================
-- config_zafra — Tabla de parametros de zafra en MySQL (db_corona)
-- Ejecutar en la misma BD que usa el flujo Node-RED MMSQL a MySQL
-- ============================================================

CREATE TABLE IF NOT EXISTS config_zafra (
    id               INT         NOT NULL DEFAULT 1,
    anio             INT         NOT NULL COMMENT 'Año de la zafra (ej: 2026)',
    fecha_inicio     DATE        NOT NULL COMMENT 'Fecha real de inicio (ej: 2026-05-18)',
    nombre_zafra     VARCHAR(50) NOT NULL COMMENT 'Ej: Molienda 2026',
    notas            TEXT,
    fecha_update     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar/actualizar config zafra 2026
INSERT INTO config_zafra (id, anio, fecha_inicio, nombre_zafra, notas)
VALUES (1, 2026, '2026-05-18', 'Molienda 2026', 'Zafra 2026 — inicio real 18/05/2026 ~21hs')
ON DUPLICATE KEY UPDATE
    anio         = VALUES(anio),
    fecha_inicio = VALUES(fecha_inicio),
    nombre_zafra = VALUES(nombre_zafra),
    notas        = VALUES(notas);

-- ============================================================
-- PARA CAMBIO DE ZAFRA (próximos años):
-- UPDATE config_zafra SET anio=2027, fecha_inicio='2027-05-XX', nombre_zafra='Molienda 2027' WHERE id=1;
-- ============================================================
