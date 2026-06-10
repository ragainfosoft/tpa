-- =====================================================
-- TPA IMS — Schema v3 Migration
-- Adds: online_classes table for virtual session scheduling
-- =====================================================

USE `tpa_ims`;

CREATE TABLE IF NOT EXISTS `online_classes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `batch_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(200),
  `scheduled_at` DATETIME NOT NULL,
  `duration_min` SMALLINT UNSIGNED NOT NULL DEFAULT 60,
  `meeting_url` VARCHAR(500) NOT NULL,
  `meeting_platform` ENUM('Zoom','Google Meet','Teams','Other') NOT NULL DEFAULT 'Zoom',
  `host_id` INT UNSIGNED COMMENT 'User who scheduled it',
  `recording_url` VARCHAR(500),
  `notes` TEXT,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`batch_id`) REFERENCES `batches`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`host_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;
