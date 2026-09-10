-- Migration 004: parent / emergency contact numbers on leads
-- Run via phpMyAdmin on the live server (kkagucom_tpa_ims database).
--
-- MySQL 5.7 compatible: `ADD COLUMN IF NOT EXISTS` is MariaDB-only syntax, so
-- each column is guarded by an information_schema lookup and applied through
-- PREPARE/EXECUTE. Re-running this file is safe.
--
-- The assessment form keeps its existing required Phone Number (leads.phone).
-- These three sit alongside it: mother's and father's are optional, the
-- caretaker/emergency number is required on the public form.

-- leads.mother_phone — optional
SET @sql := (SELECT IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leads' AND COLUMN_NAME = 'mother_phone') > 0,
  'SELECT ''leads.mother_phone already exists'' AS note',
  'ALTER TABLE leads ADD COLUMN mother_phone VARCHAR(20) NULL DEFAULT NULL COMMENT ''Optional - mother''''s contact number'' AFTER whatsapp'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- leads.father_phone — optional
SET @sql := (SELECT IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leads' AND COLUMN_NAME = 'father_phone') > 0,
  'SELECT ''leads.father_phone already exists'' AS note',
  'ALTER TABLE leads ADD COLUMN father_phone VARCHAR(20) NULL DEFAULT NULL COMMENT ''Optional - father''''s contact number'' AFTER mother_phone'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- leads.emergency_phone — required on the public assessment form
SET @sql := (SELECT IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leads' AND COLUMN_NAME = 'emergency_phone') > 0,
  'SELECT ''leads.emergency_phone already exists'' AS note',
  'ALTER TABLE leads ADD COLUMN emergency_phone VARCHAR(20) NULL DEFAULT NULL COMMENT ''Caretaker / emergency contact - required on the public form'' AFTER father_phone'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
