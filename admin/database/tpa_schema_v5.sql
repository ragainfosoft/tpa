-- =====================================================
-- TPA IMS — Schema v5 Migration
-- SaaS-ready: flexible centre names, programmes table,
-- missing settings keys, security & consistency fixes
-- Run AFTER v1–v4 have been applied
-- =====================================================

USE `tpa_ims`;

-- ── 1. Fix centre columns: ENUM → VARCHAR across all tables ─────────
-- Allows any institute to use their own branch/centre names

ALTER TABLE `leads`
  MODIFY COLUMN `centre` VARCHAR(100) NOT NULL DEFAULT 'No preference';

ALTER TABLE `students`
  MODIFY COLUMN `centre` VARCHAR(100) NULL;

ALTER TABLE `fee_structures`
  MODIFY COLUMN `centre` VARCHAR(100) NULL;

-- batches.centre was VARCHAR already — ensure consistent collation
ALTER TABLE `batches`
  MODIFY COLUMN `centre` VARCHAR(100) NULL;

-- ── 2. Standardise centre name: Romford → Chadwell Heath ────────────
-- (Romford was the old internal name; Chadwell Heath is the real address)

UPDATE `leads`     SET `centre` = 'Chadwell Heath' WHERE `centre` IN ('Romford','romford');
UPDATE `students`  SET `centre` = 'Chadwell Heath' WHERE `centre` IN ('Romford','romford');
UPDATE `batches`   SET `centre` = 'Chadwell Heath' WHERE `centre` IN ('Romford','romford','Both');
UPDATE `fee_structures` SET `centre` = 'Chadwell Heath' WHERE `centre` IN ('Romford','romford');

-- ── 3. Update branches table ─────────────────────────────────────────
UPDATE `branches`
  SET `name` = 'Chadwell Heath',
      `address` = '60 High Road, Chadwell Heath, Romford RM6 6PP',
      `phone` = '07772 922943'
  WHERE `name` IN ('Romford','romford');

UPDATE `branches`
  SET `address` = '4B Corporation Road, Chelmsford CM1 2AR'
  WHERE `name` = 'Chelmsford';

-- ── 4. Add programmes table (replaces hardcoded course_interest lists) ─
CREATE TABLE IF NOT EXISTS `programmes` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(150) NOT NULL,
  `short_code`  VARCHAR(30)  NOT NULL DEFAULT '',
  `year_range`  VARCHAR(50)  NULL COMMENT 'e.g. Year 3–6',
  `sort_order`  INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `programmes` (`name`,`short_code`,`year_range`,`sort_order`) VALUES
  ('11 Plus Preparation', '11plus',       'Year 3–6',   1),
  ('SATs (Year 2)',        'sats_y2',      'Year 2',     2),
  ('SATs (Year 6)',        'sats_y6',      'Year 6',     3),
  ('Key Stage 1 (Year 1–2)', 'ks1',       'Year 1–2',   4),
  ('Key Stage 2 (Year 3–6)', 'ks2',       'Year 3–6',   5),
  ('Key Stage 3 (Year 7–9)', 'ks3',       'Year 7–9',   6),
  ('GCSE (Year 10–11)',    'gcse',         'Year 10–11', 7),
  ('A-Level (Year 12–13)', 'alevel',       'Year 12–13', 8),
  ('Adult Learning',       'adult',        'Adult',      9),
  ('Summer Camp 2026',     'summer_camp',  'Ages 5–14',  10),
  ('Easter Camp',          'easter_camp',  'Ages 5–14',  11),
  ('Not Sure',             'not_sure',     NULL,         12);

-- ── 5. Add lead_sources table (replaces hardcoded array) ─────────────
CREATE TABLE IF NOT EXISTS `lead_sources` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(100) NOT NULL,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active`  TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `lead_sources` (`name`,`sort_order`) VALUES
  ('Google Search', 1), ('Word of Mouth', 2), ('Social Media', 3),
  ('Flyer / Leaflet', 4), ('Website', 5), ('Referral', 6), ('Other', 7);

-- ── 6. Add missing settings keys ─────────────────────────────────────
INSERT IGNORE INTO `settings` (`key`, `value`, `description`) VALUES
  ('default_password_prefix',  'Acad@',       'Prefix for auto-generated user passwords'),
  ('staff_default_password',   'Staff@2026',  'Default password for new staff accounts'),
  ('invoice_due_days',         '7',           'Days after invoice creation until due date'),
  ('wa_api_version',           'v18.0',       'Meta WhatsApp API version'),
  ('site_url_public',          '',            'Public-facing website URL (leave blank to auto-detect)'),
  ('student_ref_format',       'STU{year}{num4}', 'Student reference format'),
  ('batch_default_capacity',   '8',           'Default max students per batch'),
  ('attendance_notify_channel','both',        'Channel for absence notifications: whatsapp|email|both'),
  ('fee_vat_rate',             '0',           'VAT rate % (0 for VAT-exempt tuition)'),
  ('portal_parent_enabled',    '1',           'Enable parent portal access'),
  ('portal_student_enabled',   '1',           'Enable student portal access'),
  ('portal_teacher_enabled',   '1',           'Enable teacher portal access');

-- ── 7. Add is_active + notes to batches if not present ───────────────
ALTER TABLE `batches`
  ADD COLUMN IF NOT EXISTS `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  ADD COLUMN IF NOT EXISTS `notes`     TEXT NULL AFTER `term`;

-- ── 8. Add unique constraint on lead phone/email (soft dedup aid) ────
-- Not enforced at DB level (to allow re-enquiries) but used in code
-- ALTER TABLE `leads` ADD UNIQUE KEY ... -- intentionally left to code

-- ── 9. Ensure programmes FK can be stored on leads ───────────────────
ALTER TABLE `leads`
  ADD COLUMN IF NOT EXISTS `programme_id` INT UNSIGNED NULL AFTER `course_interest`;

-- ── 10. Widen source column on leads to accept custom sources ─────────
ALTER TABLE `leads`
  MODIFY COLUMN `source` VARCHAR(100) NOT NULL DEFAULT 'Other';
