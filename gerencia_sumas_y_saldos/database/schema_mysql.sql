-- Seguimiento Inteligente de Sumas y Saldos
-- Schema MySQL MVP - base intermedia fuera de Calipso
-- No contiene escrituras a Calipso. Solo persiste snapshots extraidos por MCP readonly.

CREATE DATABASE IF NOT EXISTS gerencia_sumas_y_saldos
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gerencia_sumas_y_saldos;

CREATE TABLE IF NOT EXISTS sy_snapshots (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  uuid CHAR(36) NOT NULL,
  source_type VARCHAR(30) NOT NULL DEFAULT 'MCP_CALIPSO',
  source_database VARCHAR(100) NULL,
  source_hash CHAR(64) NULL,
  query_version VARCHAR(40) NOT NULL DEFAULT 'snapshot_period_v1',
  period_from DATE NOT NULL,
  period_to DATE NOT NULL,
  fiscal_year INT NULL,
  fiscal_period INT NULL,
  mode VARCHAR(30) NOT NULL DEFAULT 'mensual',
  requested_by VARCHAR(100) NOT NULL DEFAULT 'system',
  row_count INT UNSIGNED NOT NULL DEFAULT 0,
  total_debit DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  total_credit DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  total_balance DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  status VARCHAR(30) NOT NULL DEFAULT 'PENDING',
  error_message TEXT NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  processed_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY ux_sy_snapshots_uuid (uuid),
  KEY ix_sy_snapshots_period (period_from, period_to),
  KEY ix_sy_snapshots_status (status),
  KEY ix_sy_snapshots_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sy_snapshot_rows (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  snapshot_id BIGINT UNSIGNED NOT NULL,
  row_number INT UNSIGNED NOT NULL,
  account_code_full VARCHAR(255) NOT NULL,
  account_code VARCHAR(80) NOT NULL,
  account_name VARCHAR(255) NULL,
  rubro_code VARCHAR(80) NULL,
  rubro_name VARCHAR(255) NULL,
  subrubro1_code VARCHAR(80) NULL,
  subrubro1_name VARCHAR(255) NULL,
  subrubro2_code VARCHAR(80) NULL,
  subrubro2_name VARCHAR(255) NULL,
  subrubro3_code VARCHAR(80) NULL,
  subrubro3_name VARCHAR(255) NULL,
  debit_period DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  credit_period DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  balance_period DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  opening_balance DECIMAL(18,2) NULL,
  closing_balance DECIMAL(18,2) NULL,
  raw_payload TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY ux_sy_snapshot_rows_snapshot_account (snapshot_id, account_code_full),
  KEY ix_sy_snapshot_rows_account_code (account_code),
  KEY ix_sy_snapshot_rows_rubro (rubro_name),
  KEY ix_sy_snapshot_rows_balance (balance_period),
  CONSTRAINT fk_sy_snapshot_rows_snapshot
    FOREIGN KEY (snapshot_id) REFERENCES sy_snapshots(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sy_snapshot_comparisons (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  uuid CHAR(36) NOT NULL,
  current_snapshot_id BIGINT UNSIGNED NOT NULL,
  previous_snapshot_id BIGINT UNSIGNED NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'PENDING',
  row_count INT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  processed_at DATETIME NULL,
  notes TEXT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY ux_sy_snapshot_comparisons_uuid (uuid),
  UNIQUE KEY ux_sy_snapshot_comparisons_pair (current_snapshot_id, previous_snapshot_id),
  KEY ix_sy_snapshot_comparisons_status (status),
  CONSTRAINT fk_sy_snapshot_comparisons_current
    FOREIGN KEY (current_snapshot_id) REFERENCES sy_snapshots(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_sy_snapshot_comparisons_previous
    FOREIGN KEY (previous_snapshot_id) REFERENCES sy_snapshots(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sy_comparison_rows (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  comparison_id BIGINT UNSIGNED NOT NULL,
  account_code_full VARCHAR(255) NOT NULL,
  account_code VARCHAR(80) NOT NULL,
  account_name VARCHAR(255) NULL,
  current_debit DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  current_credit DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  current_balance DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  previous_debit DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  previous_credit DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  previous_balance DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  debit_delta DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  credit_delta DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  balance_delta DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  balance_delta_percent DECIMAL(18,6) NULL,
  change_type VARCHAR(30) NOT NULL DEFAULT 'UNCHANGED',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY ux_sy_comparison_rows_account (comparison_id, account_code_full),
  KEY ix_sy_comparison_rows_account_code (account_code),
  KEY ix_sy_comparison_rows_change_type (change_type),
  KEY ix_sy_comparison_rows_balance_delta (balance_delta),
  CONSTRAINT fk_sy_comparison_rows_comparison
    FOREIGN KEY (comparison_id) REFERENCES sy_snapshot_comparisons(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sy_alerts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  snapshot_id BIGINT UNSIGNED NOT NULL,
  comparison_id BIGINT UNSIGNED NULL,
  rule_code VARCHAR(80) NOT NULL,
  severity VARCHAR(20) NOT NULL,
  account_code_full VARCHAR(255) NULL,
  account_code VARCHAR(80) NULL,
  account_name VARCHAR(255) NULL,
  current_balance DECIMAL(18,2) NULL,
  previous_balance DECIMAL(18,2) NULL,
  absolute_delta DECIMAL(18,2) NULL,
  percent_delta DECIMAL(18,6) NULL,
  message TEXT NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'OPEN',
  reviewed_by VARCHAR(100) NULL,
  reviewed_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_sy_alerts_snapshot (snapshot_id),
  KEY ix_sy_alerts_comparison (comparison_id),
  KEY ix_sy_alerts_severity_status (severity, status),
  KEY ix_sy_alerts_account_code (account_code),
  CONSTRAINT fk_sy_alerts_snapshot
    FOREIGN KEY (snapshot_id) REFERENCES sy_snapshots(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_sy_alerts_comparison
    FOREIGN KEY (comparison_id) REFERENCES sy_snapshot_comparisons(id)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sy_processing_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  trace_uuid CHAR(36) NOT NULL,
  snapshot_id BIGINT UNSIGNED NULL,
  comparison_id BIGINT UNSIGNED NULL,
  level VARCHAR(20) NOT NULL,
  step VARCHAR(80) NOT NULL,
  message TEXT NOT NULL,
  context_payload TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_sy_processing_logs_trace (trace_uuid),
  KEY ix_sy_processing_logs_snapshot (snapshot_id),
  KEY ix_sy_processing_logs_comparison (comparison_id),
  KEY ix_sy_processing_logs_level (level),
  CONSTRAINT fk_sy_processing_logs_snapshot
    FOREIGN KEY (snapshot_id) REFERENCES sy_snapshots(id)
    ON DELETE SET NULL,
  CONSTRAINT fk_sy_processing_logs_comparison
    FOREIGN KEY (comparison_id) REFERENCES sy_snapshot_comparisons(id)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
