-- =====================================================
-- TPA IMS — Database Schema v2 Migration
-- Run AFTER importing tpa_schema.sql (v1)
-- =====================================================

USE `tpa_ims`;

-- ── 1. Extend users table for all 5 roles ──────────────────────────
ALTER TABLE `users`
  MODIFY COLUMN `role` ENUM('admin','branch_manager','teacher','student','parent','staff') NOT NULL DEFAULT 'staff',
  ADD COLUMN `branch_id`  INT UNSIGNED AFTER `role`,
  ADD COLUMN `student_id` INT UNSIGNED COMMENT 'FK to students, for student/parent portal logins' AFTER `branch_id`;

-- ── 2. Extend students table ────────────────────────────────────────
ALTER TABLE `students`
  ADD COLUMN `user_id`   INT UNSIGNED COMMENT 'Portal login user account' AFTER `id`,
  ADD COLUMN `branch_id` INT UNSIGNED AFTER `user_id`;

-- ── 3. Branches ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `branches` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(100) NOT NULL,
  `address`    TEXT,
  `phone`      VARCHAR(20),
  `email`      VARCHAR(200),
  `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO `branches` (`name`,`address`,`phone`) VALUES
  ('Romford',     'Romford, Essex, RM1', '01708 000000'),
  ('Chelmsford',  'Chelmsford, Essex, CM1', '01245 000000'),
  ('Online',      'Online (Virtual)', '');

-- ── 4. Subjects ──────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `subjects` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(100) NOT NULL,
  `icon`       VARCHAR(40) DEFAULT 'book',
  `color`      VARCHAR(20) DEFAULT '#0A1628',
  `sort_order` INT UNSIGNED DEFAULT 0
) ENGINE=InnoDB;

INSERT IGNORE INTO `subjects` (`name`,`icon`,`color`,`sort_order`) VALUES
  ('Mathematics',  'calculator',     '#1565c0', 1),
  ('English',      'alphabet-latin', '#2e7d32', 2),
  ('Science',      'flask',          '#6a1b9a', 3),
  ('11 Plus',      'star-fill',      '#e65100', 4),
  ('Verbal Reasoning',  'chat-text', '#1976d2', 5),
  ('Non-Verbal Reasoning','grid-3x3','#00695c', 6),
  ('GCSE Maths',   'calculator-fill','#c62828', 7),
  ('GCSE English', 'pen',            '#4a148c', 8);

-- ── 5. Extend batches table ──────────────────────────────────────────
ALTER TABLE `batches`
  ADD COLUMN `branch_id`  INT UNSIGNED AFTER `id`,
  ADD COLUMN `subject_id` INT UNSIGNED AFTER `branch_id`;

-- ── 6. Online Classes ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `online_classes` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `batch_id`    INT UNSIGNED NOT NULL,
  `title`       VARCHAR(200),
  `meeting_url` TEXT,
  `platform`    ENUM('Zoom','Google Meet','Microsoft Teams','Custom') DEFAULT 'Zoom',
  `scheduled_at` DATETIME NOT NULL,
  `duration_min` INT UNSIGNED DEFAULT 60,
  `reminder_sent` TINYINT(1) DEFAULT 0,
  `is_active`   TINYINT(1) DEFAULT 1,
  `notes`       TEXT,
  `created_by`  INT UNSIGNED,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`batch_id`) REFERENCES `batches`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── 7. Homework ──────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `homework` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `batch_id`    INT UNSIGNED NOT NULL,
  `subject_id`  INT UNSIGNED,
  `title`       VARCHAR(200) NOT NULL,
  `description` TEXT,
  `file_path`   VARCHAR(500),
  `due_date`    DATE,
  `set_by`      INT UNSIGNED COMMENT 'users.id of teacher',
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`batch_id`) REFERENCES `batches`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `homework_submissions` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `homework_id`  INT UNSIGNED NOT NULL,
  `student_id`   INT UNSIGNED NOT NULL,
  `text_answer`  TEXT,
  `file_path`    VARCHAR(500),
  `status`       ENUM('submitted','late','graded','missing') NOT NULL DEFAULT 'submitted',
  `grade`        VARCHAR(10),
  `feedback`     TEXT,
  `submitted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `graded_at`    DATETIME,
  `graded_by`    INT UNSIGNED,
  UNIQUE KEY `unique_submission` (`homework_id`,`student_id`),
  FOREIGN KEY (`homework_id`) REFERENCES `homework`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── 8. MCQ Quiz System ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `quiz_sets` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title`          VARCHAR(200) NOT NULL,
  `subject_id`     INT UNSIGNED,
  `year_group`     VARCHAR(20),
  `lesson`         VARCHAR(200) COMMENT 'e.g. Algebra, Fractions',
  `description`    TEXT,
  `time_limit_min` INT UNSIGNED DEFAULT 0  COMMENT '0 = unlimited',
  `attempt_limit`  INT UNSIGNED DEFAULT 1  COMMENT '0 = unlimited',
  `pass_mark_pct`  DECIMAL(5,2) DEFAULT 60.00,
  `shuffle_questions` TINYINT(1) DEFAULT 0,
  `shuffle_options`   TINYINT(1) DEFAULT 0,
  `negative_marking`  DECIMAL(4,2) DEFAULT 0.00 COMMENT 'marks deducted per wrong answer',
  `result_mode`       ENUM('instant','delayed') DEFAULT 'instant',
  `marks_per_question` DECIMAL(4,2) DEFAULT 1.00,
  `is_active`      TINYINT(1) DEFAULT 1,
  `created_by`     INT UNSIGNED,
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `quiz_questions` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `quiz_id`      INT UNSIGNED NOT NULL,
  `question`     TEXT NOT NULL,
  `option_a`     TEXT NOT NULL,
  `option_b`     TEXT NOT NULL,
  `option_c`     TEXT NOT NULL,
  `option_d`     TEXT NOT NULL,
  `correct`      ENUM('a','b','c','d') NOT NULL,
  `explanation`  TEXT,
  `marks`        DECIMAL(4,2) DEFAULT 1.00,
  `sort_order`   INT UNSIGNED DEFAULT 0,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`quiz_id`) REFERENCES `quiz_sets`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `quiz_assignments` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `quiz_id`    INT UNSIGNED NOT NULL,
  `batch_id`   INT UNSIGNED,
  `student_id` INT UNSIGNED  COMMENT 'NULL if batch-wide',
  `assigned_by` INT UNSIGNED,
  `due_date`   DATE,
  `assigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`quiz_id`) REFERENCES `quiz_sets`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `quiz_attempts` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `quiz_id`      INT UNSIGNED NOT NULL,
  `student_id`   INT UNSIGNED NOT NULL,
  `assignment_id` INT UNSIGNED,
  `started_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `submitted_at` DATETIME,
  `score`        DECIMAL(6,2) DEFAULT 0,
  `max_score`    DECIMAL(6,2) DEFAULT 0,
  `percentage`   DECIMAL(5,2) DEFAULT 0,
  `passed`       TINYINT(1) DEFAULT 0,
  `time_taken_sec` INT UNSIGNED DEFAULT 0,
  `status`       ENUM('in_progress','submitted','timed_out') DEFAULT 'in_progress',
  FOREIGN KEY (`quiz_id`) REFERENCES `quiz_sets`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `quiz_answers` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `attempt_id`   INT UNSIGNED NOT NULL,
  `question_id`  INT UNSIGNED NOT NULL,
  `chosen`       ENUM('a','b','c','d'),
  `is_correct`   TINYINT(1) DEFAULT 0,
  `marks_earned` DECIMAL(4,2) DEFAULT 0,
  UNIQUE KEY `unique_answer` (`attempt_id`,`question_id`),
  FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── 9. Add WhatsApp templates for new events to settings ─────────────
INSERT IGNORE INTO `settings` (`key`, `value`, `description`) VALUES
  ('wa_template_homework_set',  'Hi {parent_name}, {child_name} has new homework: "{title}" due {due_date}. Please ensure they complete it. — TPA', 'WA: Homework set'),
  ('wa_template_class_reminder','Hi {parent_name}, reminder: {child_name}\'s class starts in 30 mins. Link: {meeting_url} — TPA', 'WA: Class reminder'),
  ('wa_template_quiz_assigned', 'Hi {parent_name}, {child_name} has been assigned a new quiz: "{quiz_title}". Login to the student portal to attempt it. — TPA', 'WA: Quiz assigned');

-- ── 10. Seed default admin user for portal portals (example) ─────────
-- Branch Manager, Teacher, Student, Parent users can be added via admin/settings/users.php
