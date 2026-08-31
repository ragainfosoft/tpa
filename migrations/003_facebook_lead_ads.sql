-- Migration 003: Facebook / Instagram Lead Ads integration
-- Run via phpMyAdmin on the live server (kkagucom_tpa_ims database).
--
-- MySQL 5.7 compatible: no `IF NOT EXISTS` on columns (MariaDB-only), and the
-- lead_sources inserts are guarded in case schema v5 was never applied.
-- Additive only, and safe to run more than once.

-- ── 1. leads.source must hold custom labels like 'Facebook Ad' ───────
-- Schema v5 already did this; MODIFY is idempotent, so repeating it is safe
-- and it also covers the case where v5 was never run.
ALTER TABLE `leads` MODIFY COLUMN `source` VARCHAR(100) NOT NULL DEFAULT 'Other';

-- ── 2. Webhook audit trail + idempotency guard ───────────────────────
-- leadgen_id is UNIQUE so Meta's retries can never create a second lead.
CREATE TABLE IF NOT EXISTS `fb_lead_log` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `leadgen_id`    VARCHAR(64) NOT NULL,
  `form_id`       VARCHAR(64)  NULL,
  `form_name`     VARCHAR(191) NULL,
  `page_id`       VARCHAR(64)  NULL,
  `campaign_name` VARCHAR(191) NULL,
  `adset_name`    VARCHAR(191) NULL,
  `ad_name`       VARCHAR(191) NULL,
  `platform`      VARCHAR(20)  NULL,
  `lead_id`       INT UNSIGNED NULL,
  `status`        ENUM('received','created','duplicate','skipped','error') NOT NULL DEFAULT 'received',
  `error_message` TEXT NULL,
  `raw_payload`   LONGTEXT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_leadgen` (`leadgen_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `fb_lead_log_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 3. Settings rows ─────────────────────────────────────────────────
-- These must exist before the Settings screen can write them on builds
-- where saving used a plain UPDATE.
INSERT INTO `settings` (`key`, `value`, `description`) VALUES
  ('fb_leads_enabled',      '0',             'Facebook Lead Ads: master on/off switch'),
  ('fb_verify_token',       '',              'Facebook Lead Ads: webhook verify token (you invent this string)'),
  ('fb_app_secret',         '',              'Facebook Lead Ads: Meta app secret, used to verify webhook signatures'),
  ('fb_page_id',            '',              'Facebook Lead Ads: Facebook Page ID'),
  ('fb_page_token',         '',              'Facebook Lead Ads: long-lived Page access token'),
  ('fb_lead_source_label',  'Facebook Ad',   'Facebook Lead Ads: value written to leads.source'),
  ('fb_lead_default_centre','No preference', 'Facebook Lead Ads: centre used when the form does not ask'),
  ('fb_lead_assign_to',     '',              'Facebook Lead Ads: user ID to auto-assign new leads to (blank = unassigned)'),
  ('fb_lead_send_whatsapp', '0',             'Facebook Lead Ads: send the new-lead WhatsApp welcome automatically'),
  ('fb_lead_notify_email',  '',              'Facebook Lead Ads: email address to notify on each new lead (blank = off)')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);

-- ── 4. Register the new sources in the Add/Edit Lead dropdowns ───────
-- lead_sources arrives with schema v5 and has no unique key on name, so this
-- checks both that the table exists and that the row does not.
SET @sql := (SELECT IF(
  (SELECT COUNT(*) FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lead_sources') = 0,
  'SELECT ''lead_sources table not present — run schema v5 first'' AS note',
  'INSERT INTO lead_sources (name, sort_order)
     SELECT ''Facebook Ad'', 8 FROM DUAL
     WHERE NOT EXISTS (SELECT 1 FROM lead_sources WHERE name = ''Facebook Ad'')'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (SELECT IF(
  (SELECT COUNT(*) FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lead_sources') = 0,
  'SELECT ''lead_sources table not present — run schema v5 first'' AS note',
  'INSERT INTO lead_sources (name, sort_order)
     SELECT ''Instagram Ad'', 9 FROM DUAL
     WHERE NOT EXISTS (SELECT 1 FROM lead_sources WHERE name = ''Instagram Ad'')'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
