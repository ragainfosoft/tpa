-- Migration 002: missing invoices / payments columns
-- Run via phpMyAdmin on the live server (kkagucom_tpa_ims database).
--
-- MySQL 5.7 compatible: `ADD COLUMN IF NOT EXISTS` is MariaDB-only syntax and
-- is a hard syntax error here, so each column is guarded by an
-- information_schema lookup and applied through PREPARE/EXECUTE instead.
-- Re-running this file is safe — existing columns are reported and skipped.
-- Paste the whole file into the SQL tab and run it in one go.

-- invoices.reminder_sent_at — reminder tracking
SET @sql := (SELECT IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'reminder_sent_at') > 0,
  'SELECT ''invoices.reminder_sent_at already exists'' AS note',
  'ALTER TABLE invoices ADD COLUMN reminder_sent_at DATETIME NULL DEFAULT NULL'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- invoices.reminder_count — reminder tracking
SET @sql := (SELECT IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'reminder_count') > 0,
  'SELECT ''invoices.reminder_count already exists'' AS note',
  'ALTER TABLE invoices ADD COLUMN reminder_count INT NOT NULL DEFAULT 0'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- invoices.payment_token — online pay links
SET @sql := (SELECT IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'payment_token') > 0,
  'SELECT ''invoices.payment_token already exists'' AS note',
  'ALTER TABLE invoices ADD COLUMN payment_token VARCHAR(64) NULL DEFAULT NULL'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- invoices.fee_plan_name — label used on the invoice PDF
SET @sql := (SELECT IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'fee_plan_name') > 0,
  'SELECT ''invoices.fee_plan_name already exists'' AS note',
  'ALTER TABLE invoices ADD COLUMN fee_plan_name VARCHAR(150) NULL DEFAULT NULL'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- payments.gateway — stripe, gocardless, manual
SET @sql := (SELECT IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payments' AND COLUMN_NAME = 'gateway') > 0,
  'SELECT ''payments.gateway already exists'' AS note',
  'ALTER TABLE payments ADD COLUMN gateway VARCHAR(50) NULL DEFAULT NULL'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- payments.gateway_payment_id — gateway transaction reference
SET @sql := (SELECT IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payments' AND COLUMN_NAME = 'gateway_payment_id') > 0,
  'SELECT ''payments.gateway_payment_id already exists'' AS note',
  'ALTER TABLE payments ADD COLUMN gateway_payment_id VARCHAR(150) NULL DEFAULT NULL'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
