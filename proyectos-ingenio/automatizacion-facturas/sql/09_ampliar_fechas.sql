-- 09_ampliar_fechas.sql
-- Ampliar columnas de fecha de VARCHAR(8) a DATE en staging_facturas
-- Base: db_automatizaciones
--
-- Contexto: el esquema original usaba VARCHAR(8) con formato YYYYMMDD.
-- El workflow v10 inserta con STR_TO_DATE(), necesita tipo DATE.
-- Filas existentes con fechas truncadas ('2026-04-') quedan NULL.

ALTER TABLE staging_facturas
  MODIFY COLUMN fecha_emision     DATE NULL,
  MODIFY COLUMN fecha_vencimiento DATE NULL,
  MODIFY COLUMN fecha_vto_cae     DATE NULL;
