-- ============================================================
-- SIMULACIÓN: 20 registros en indicadores_opc (fecha de hoy)
-- ============================================================
-- Registros 1-19: valores aleatorios dentro de rangos normales
-- Registro 20 (23:59hs): valores diseñados para ver los 3 estados
--   VERDE:    presion_vapor_directo, caudal_vapor_cald2,
--             potencia_activa_aeg, caudal_vino
--   AMARILLO: presion_molino6_oeste, caudal_vapor_cald6,
--             caudal_jugo_clarif
--   ROJO:     cv_usina_alta, caudal_jugo_dilutor, potencia_total
--   GRIS:     todos los indicadores sin objetivo definido
-- ============================================================
-- INSERT IGNORE: omite duplicados si ya existe data en ese timestamp
-- Para limpiar la simulación: DELETE FROM indicadores_opc WHERE DATE(timestamp)=CURDATE();
-- ============================================================

INSERT IGNORE INTO indicadores_opc (
  timestamp,
  -- TRAPICHE
  velocidad_molino1, velocidad_molino6, balanza_cinta, agua_imbibicion,
  presion_molino6_este, presion_molino6_oeste,
  -- FABRICACION
  caudal_jugo_clarif, nivel_melado_tratado, nivel_melado,
  nivel_decantador1, nivel_decantador2, nivel_decantador3,
  descarga_tachos_1ra, descarga_tachos_2da, descarga_tachos_3ra,
  -- SALON
  contador_bolsas_dia, silo_a, silo_b, silo_c, silo_e,
  -- CALDERA
  presion_vapor_directo, presion_agua_alim, presion_aire,
  caudal_vapor_cald1, caudal_vapor_cald2, caudal_vapor_cald3, caudal_vapor_cald6,
  vapor_total, caudal_gas_cald2, caudal_gas_cald6,
  -- USINA
  potencia_activa_siemens, potencia_reactiva_siemens, frecuencia_siemens, intensidad_siemens,
  potencia_activa_aeg, potencia_reactiva_aeg, frecuencia_aeg, intensidad_aeg, potencia_total,
  -- CONSUMOS VAPOR
  cv_trapiche, cv_usina_alta, cv_destileria, cv_aux_total, cv_preparacion_cania,
  -- DESTILERIA
  caudal_vino, caudal_alcohol, caudal_jugo_dilutor, caudal_melaza_dilutor
)
VALUES
-- ── Registro 1 (06:00) ──────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 360 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 2 (06:30) ──────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 390 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 3 (07:00) ──────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 420 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 4 (07:30) ──────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 450 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 5 (08:00) ──────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 480 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 6 (08:30) ──────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 510 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 7 (09:00) ──────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 540 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 8 (09:30) ──────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 570 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 9 (10:00) ──────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 600 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 10 (10:30) ─────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 630 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 11 (11:00) ─────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 660 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 12 (11:30) ─────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 690 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 13 (12:00) ─────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 720 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 14 (12:30) ─────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 750 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 15 (13:00) ─────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 780 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 16 (13:30) ─────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 810 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 17 (14:00) ─────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 840 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 18 (14:30) ─────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 870 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),
-- ── Registro 19 (15:00) ─────────────────────────────────────
(DATE_ADD(CURDATE(), INTERVAL 900 MINUTE),
 ROUND(3.0+RAND()*4.0,2),   ROUND(3.0+RAND()*4.0,2),   ROUND(200+RAND()*200,1), ROUND(40+RAND()*40,1),
 ROUND(2.5+RAND()*1.5,2),   ROUND(2.5+RAND()*1.5,2),
 ROUND(220+RAND()*60,1),    ROUND(50+RAND()*35,1),      ROUND(50+RAND()*35,1),
 ROUND(40+RAND()*40,1),     ROUND(40+RAND()*40,1),      ROUND(40+RAND()*40,1),
 ROUND(60+RAND()*30,1),     ROUND(60+RAND()*30,1),      ROUND(60+RAND()*30,1),
 ROUND(RAND()*8000,0),      ROUND(20+RAND()*75,1),      ROUND(20+RAND()*75,1),   ROUND(20+RAND()*75,1), ROUND(20+RAND()*75,1),
 ROUND(17+RAND()*6,2),      ROUND(22+RAND()*4,2),       ROUND(5+RAND()*3,2),
 ROUND(RAND()*25,1),        ROUND(30+RAND()*20,1),      ROUND(RAND()*30,1),       ROUND(80+RAND()*40,1),
 ROUND(150+RAND()*70,1),    ROUND(RAND()*500,0),         ROUND(RAND()*1000,0),
 ROUND(2000+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),
 ROUND(3500+RAND()*3000,0), ROUND(500+RAND()*1500,0),   ROUND(49.8+RAND()*0.4,2),ROUND(200+RAND()*400,0),ROUND(5500+RAND()*5500,0),
 ROUND(15+RAND()*35,1),     ROUND(100+RAND()*80,1),     ROUND(8+RAND()*17,1),    ROUND(5+RAND()*15,1),  ROUND(3+RAND()*12,1),
 ROUND(4000+RAND()*2000,0), ROUND(600+RAND()*900,0),    ROUND(4000+RAND()*2000,0),ROUND(1000+RAND()*2000,0)),

-- ── Registro 20 (23:59) — DISEÑADO PARA VER TODOS LOS ESTADOS ──
-- Este es el que muestra la pantalla (ORDER BY timestamp DESC LIMIT 1)
--
--  VERDE    presion_vapor_directo=20.10  (rango 19-21  ✓)
--  VERDE    caudal_vapor_cald2=38.50     (obj:40  -3.8% ✓)
--  VERDE    potencia_activa_aeg=4820     (obj:5000 -3.6% ✓)
--  VERDE    caudal_vino=5150             (obj:5000 +3.0% ✓)
--  AMARILLO presion_molino6_oeste=2.72   (obj:3   -9.3% ⚠)
--  AMARILLO caudal_vapor_cald6=108.50    (obj:100  +8.5% ⚠)
--  AMARILLO caudal_jugo_clarif=270.00    (obj:250  +8.0% ⚠)
--  ROJO     cv_usina_alta=162.00         (obj:140 +15.7% ✗)
--  ROJO     caudal_jugo_dilutor=4300     (obj:5000 -14.0% ✗)
--  ROJO     potencia_total=8670          (obj:5000 +73.4% ✗)
--  GRIS     resto de indicadores (sin objetivo definido)
(CONCAT(CURDATE(), ' 23:59:00'),
 -- TRAPICHE
 5.20,    4.80,    312.50,  58.30,
 3.10,    2.72,
 -- FABRICACION
 270.00,  67.40,   71.20,
 58.30,   61.70,   55.90,
 74.20,   71.80,   68.50,
 -- SALON
 3850,    45.20,   72.80,   38.10,   61.40,
 -- CALDERA
 20.10,   24.30,   6.50,
 18.20,   38.50,   22.70,   108.50,
 187.90,  0,       0,
 -- USINA
 3850,    1240,    50.02,   380,
 4820,    1580,    50.01,   420,     8670,
 -- CONSUMOS VAPOR
 32.40,   162.00,  14.80,   9.30,    6.70,
 -- DESTILERIA
 5150,    980,     4300,    1820);

-- ── Verificación ────────────────────────────────────────────
SELECT
  timestamp,
  presion_vapor_directo, caudal_vapor_cald2, caudal_vapor_cald6,
  cv_usina_alta, potencia_activa_aeg, potencia_total,
  caudal_vino, caudal_jugo_dilutor, caudal_jugo_clarif,
  presion_molino6_oeste
FROM indicadores_opc
WHERE DATE(timestamp) = CURDATE()
ORDER BY timestamp DESC
LIMIT 5;
