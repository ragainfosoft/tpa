-- Migration 001: payments.reconciled
-- Run via phpMyAdmin on the live server (kkagucom_tpa_ims database).
--
-- MySQL 5.7 compatible: `ADD COLUMN IF NOT EXISTS` is MariaDB-only syntax and
-- is a hard syntax error here, so each column is guarded by an
-- information_schema lookup and applied through PREPARE/EXECUTE instead.
-- Re-running this file is safe — existing columns are reported and skipped.
-- Paste the whole file into the SQL tab and run it in one go.

-- payments.reconciled — BACS verification flag
SET @sql := (SELECT IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payments' AND COLUMN_NAME = 'reconciled') > 0,
  'SELECT ''payments.reconciled already exists'' AS note',
  'ALTER TABLE payments ADD COLUMN reconciled TINYINT(1) NOT NULL DEFAULT 1 COMMENT ''0=unverified BACS claim, 1=verified/reconciled'''));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Existing payments were entered manually or came from a gateway, so they are
-- already verified. Only rows left NULL by an older build need setting.
UPDATE payments SET reconciled = 1 WHERE reconciled IS NULL;
