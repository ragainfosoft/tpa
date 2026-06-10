-- =====================================================
-- TPA Institute Management System — Database Schema
-- =====================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS `tpa_ims` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tpa_ims`;

-- =====================================================
-- USERS & AUTHENTICATION
-- =====================================================

CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(200) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','staff','parent') NOT NULL DEFAULT 'staff',
  `phone` VARCHAR(20),
  `whatsapp` VARCHAR(20),
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login` DATETIME,
  `reset_token` VARCHAR(100),
  `reset_expires` DATETIME,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- LEADS & CRM
-- =====================================================

CREATE TABLE `leads` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(200),
  `phone` VARCHAR(20),
  `whatsapp` VARCHAR(20),
  `child_name` VARCHAR(120),
  `child_year` VARCHAR(20),
  `course_interest` VARCHAR(100),
  `centre` ENUM('Romford','Chelmsford','Online','No preference') DEFAULT 'No preference',
  `source` ENUM('Google Search','Word of Mouth','Social Media','Flyer','Website','Other') DEFAULT 'Other',
  `status` ENUM('new','contacted','follow_up','assessment_booked','enrolled','lost') NOT NULL DEFAULT 'new',
  `assigned_to` INT UNSIGNED,
  `notes` TEXT,
  `next_followup_date` DATE,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE `lead_followups` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `lead_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED,
  `type` ENUM('call','whatsapp','email','visit','assessment','other') NOT NULL DEFAULT 'call',
  `notes` TEXT,
  `outcome` VARCHAR(255),
  `next_followup_date` DATE,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`lead_id`) REFERENCES `leads`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================
-- STUDENTS & PARENTS
-- =====================================================

CREATE TABLE `students` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `student_ref` VARCHAR(20) NOT NULL UNIQUE COMMENT 'e.g. TPA-2024-001',
  `first_name` VARCHAR(80) NOT NULL,
  `last_name` VARCHAR(80) NOT NULL,
  `dob` DATE,
  `year_group` VARCHAR(20),
  `school` VARCHAR(150),
  `gender` ENUM('Male','Female','Other','Prefer not to say'),
  `photo` VARCHAR(255),
  `join_date` DATE,
  `lead_id` INT UNSIGNED COMMENT 'Original lead if converted',
  `centre` ENUM('Romford','Chelmsford','Online','Both'),
  `status` ENUM('active','inactive','suspended','left') NOT NULL DEFAULT 'active',
  `notes` TEXT,
  `medical_notes` TEXT,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`lead_id`) REFERENCES `leads`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE `student_parents` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT UNSIGNED NOT NULL,
  `parent_name` VARCHAR(120) NOT NULL,
  `relationship` ENUM('Mother','Father','Guardian','Other') NOT NULL DEFAULT 'Guardian',
  `email` VARCHAR(200),
  `phone` VARCHAR(20),
  `whatsapp` VARCHAR(20),
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `user_id` INT UNSIGNED COMMENT 'Linked parent portal user',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================
-- TEACHERS / STAFF
-- =====================================================

CREATE TABLE `teachers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `subjects` VARCHAR(255) COMMENT 'comma-separated',
  `qualification` VARCHAR(200),
  `dbs_number` VARCHAR(50),
  `dbs_expiry` DATE,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- BATCHES / GROUPS
-- =====================================================

CREATE TABLE `batches` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `course_type` ENUM('11plus','sats','ks1','ks2','ks3','gcse','easter_camp','summer_camp','other') NOT NULL,
  `year_group` VARCHAR(50) COMMENT 'e.g. Year 5, Year 5 & 6',
  `teacher_id` INT UNSIGNED,
  `centre` ENUM('Romford','Chelmsford','Online','Both'),
  `day_of_week` VARCHAR(50) COMMENT 'e.g. Saturday, Saturday & Sunday',
  `start_time` TIME,
  `end_time` TIME,
  `max_capacity` TINYINT UNSIGNED DEFAULT 10,
  `term` VARCHAR(50) COMMENT 'e.g. Spring 2026',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE `batch_students` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `batch_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `joined_date` DATE NOT NULL,
  `left_date` DATE,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY `unique_batch_student` (`batch_id`, `student_id`),
  FOREIGN KEY (`batch_id`) REFERENCES `batches`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- ATTENDANCE
-- =====================================================

CREATE TABLE `attendance` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `batch_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `date` DATE NOT NULL,
  `status` ENUM('present','absent','late','excused') NOT NULL DEFAULT 'present',
  `notes` VARCHAR(255),
  `marked_by` INT UNSIGNED,
  `parent_notified` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_attendance` (`batch_id`, `student_id`, `date`),
  FOREIGN KEY (`batch_id`) REFERENCES `batches`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`marked_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================
-- FEES & PAYMENTS
-- =====================================================

CREATE TABLE `fee_structures` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `course_type` VARCHAR(50),
  `amount` DECIMAL(8,2) NOT NULL,
  `frequency` ENUM('per_session','weekly','fortnightly','monthly','half_termly','termly','annual','custom') NOT NULL DEFAULT 'monthly',
  `frequency_detail` VARCHAR(100) COMMENT 'e.g. "Every 4 weeks", "10 instalments"',
  `description` TEXT,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `student_payment_schedules` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT UNSIGNED NOT NULL,
  `fee_structure_id` INT UNSIGNED NOT NULL,
  `batch_id` INT UNSIGNED,
  `start_date` DATE NOT NULL,
  `end_date` DATE,
  `next_invoice_date` DATE NOT NULL,
  `auto_generate` TINYINT(1) NOT NULL DEFAULT 1,
  `payment_method` ENUM('cash','bacs','card','gocardless','stripe') DEFAULT 'bacs',
  `gocardless_mandate_id` VARCHAR(100),
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`fee_structure_id`) REFERENCES `fee_structures`(`id`),
  FOREIGN KEY (`batch_id`) REFERENCES `batches`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE `invoices` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `invoice_number` VARCHAR(30) NOT NULL UNIQUE COMMENT 'e.g. TPA-2026-0001',
  `student_id` INT UNSIGNED NOT NULL,
  `schedule_id` INT UNSIGNED,
  `amount` DECIMAL(8,2) NOT NULL,
  `discount` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  `amount_due` DECIMAL(8,2) NOT NULL,
  `period_label` VARCHAR(100) COMMENT 'e.g. April 2026, Spring Term 2026',
  `due_date` DATE NOT NULL,
  `status` ENUM('draft','unpaid','partial','paid','overdue','cancelled','refunded') NOT NULL DEFAULT 'unpaid',
  `payment_link_stripe` VARCHAR(500),
  `payment_link_gocardless` VARCHAR(500),
  `notes` TEXT,
  `created_by` INT UNSIGNED,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`schedule_id`) REFERENCES `student_payment_schedules`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE `payments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `invoice_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(8,2) NOT NULL,
  `payment_date` DATE NOT NULL,
  `method` ENUM('cash','bacs','card','gocardless','stripe') NOT NULL,
  `reference` VARCHAR(100) COMMENT 'BACS ref, Stripe charge ID, GoCardless payment ID',
  `gateway_status` VARCHAR(50) COMMENT 'succeeded, pending, failed',
  `notes` VARCHAR(255),
  `recorded_by` INT UNSIGNED,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recorded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================
-- ASSESSMENTS / RESULTS
-- =====================================================

CREATE TABLE `assessments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `batch_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `type` ENUM('mock_exam','classwork','homework','termly_test','entrance_test') NOT NULL DEFAULT 'mock_exam',
  `subject` VARCHAR(80),
  `date` DATE NOT NULL,
  `max_marks` DECIMAL(6,2) NOT NULL DEFAULT 100,
  `notes` TEXT,
  `created_by` INT UNSIGNED,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`batch_id`) REFERENCES `batches`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE `assessment_results` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `assessment_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `marks` DECIMAL(6,2),
  `percentage` DECIMAL(5,2),
  `grade` CHAR(2) COMMENT 'A*, A, B, C, D, E, F',
  `feedback` TEXT,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_result` (`assessment_id`, `student_id`),
  FOREIGN KEY (`assessment_id`) REFERENCES `assessments`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- COMMUNICATION & NOTIFICATIONS
-- =====================================================

CREATE TABLE `communications` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `type` ENUM('whatsapp','email','sms') NOT NULL,
  `recipient_type` ENUM('parent','lead','staff','all') NOT NULL,
  `recipient_id` INT UNSIGNED COMMENT 'student_parent id or lead id',
  `to_number_or_email` VARCHAR(200),
  `template_name` VARCHAR(100),
  `message` TEXT NOT NULL,
  `status` ENUM('pending','sent','delivered','failed') NOT NULL DEFAULT 'pending',
  `error_message` TEXT,
  `meta_message_id` VARCHAR(200) COMMENT 'WhatsApp message ID from Meta API',
  `sent_by` INT UNSIGNED,
  `sent_at` DATETIME,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`sent_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE `reminders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `type` ENUM('fee_due','fee_overdue','attendance_absent','followup','assessment_result','general') NOT NULL,
  `related_type` VARCHAR(50) COMMENT 'invoice, lead, student, etc.',
  `related_id` INT UNSIGNED,
  `recipient_id` INT UNSIGNED COMMENT 'student_parent id',
  `message` TEXT,
  `send_via` SET('whatsapp','email') NOT NULL DEFAULT 'whatsapp,email',
  `scheduled_at` DATETIME NOT NULL,
  `sent_at` DATETIME,
  `status` ENUM('pending','sent','failed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- SETTINGS & AUDIT
-- =====================================================

CREATE TABLE `settings` (
  `key` VARCHAR(100) PRIMARY KEY,
  `value` TEXT,
  `description` VARCHAR(255),
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `activity_log` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED,
  `action` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `ip_address` VARCHAR(45),
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================
-- DEFAULT SEED DATA
-- =====================================================

-- Admin user (password: Admin@TPA2026)
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `phone`, `is_active`)
VALUES (
  'Admin TPA',
  'admin@talentpoolacademy.com',
  '$2y$12$JbP03AJE/bpHifQ5geh81OAxZciO5vfNDqPvwHunzsi0MT1oxNFJ2',
  'admin',
  '07772922943',
  1
);

-- Default settings
INSERT INTO `settings` (`key`, `value`, `description`) VALUES
('site_name', 'Talent Pool Academy', 'Academy name'),
('site_phone', '07772 922943', 'Main phone'),
('site_email', 'enquiry@talentpoolacademy.com', 'Main email'),
('site_address_romford', '60 High Road, Chadwell Heath, Romford RM6 6PP', 'Romford address'),
('site_address_chelmsford', '4 Corporation Road, Chelmsford CM1 2AR', 'Chelmsford address'),
('bank_name', 'Talent Pool Academy', 'Bank account name'),
('bank_account', '69995444', 'Bank account number'),
('bank_sort_code', '08-92-99', 'Sort code'),
('bacs_reference_prefix', 'TPA', 'Payment ref prefix'),
('smtp_host', '', 'SMTP server'),
('smtp_port', '587', 'SMTP port'),
('smtp_user', '', 'SMTP username'),
('smtp_pass', '', 'SMTP password'),
('smtp_from_name', 'Talent Pool Academy', 'From name for emails'),
('smtp_from_email', 'enquiry@talentpoolacademy.com', 'From email'),
('whatsapp_api_url', 'https://graph.facebook.com/v18.0', 'Meta WhatsApp Cloud API base URL'),
('whatsapp_phone_number_id', '', 'WhatsApp phone number ID from Meta'),
('whatsapp_token', '', 'Meta permanent access token'),
('gocardless_access_token', '', 'GoCardless API key'),
('gocardless_environment', 'sandbox', 'sandbox or live'),
('stripe_secret_key', '', 'Stripe secret key'),
('stripe_public_key', '', 'Stripe publishable key'),
('invoice_prefix', 'TPA', 'Invoice number prefix'),
('invoice_next_number', '1', 'Next invoice sequence number'),
('student_ref_prefix', 'TPA', 'Student reference prefix'),
('student_ref_next', '1', 'Next student ref number');

-- Sample fee structures
INSERT INTO `fee_structures` (`name`, `course_type`, `amount`, `frequency`, `description`) VALUES
('11 Plus Monthly', '11plus', 120.00, 'monthly', 'Monthly tuition fee for 11 Plus programme'),
('11 Plus Termly', '11plus', 330.00, 'termly', 'Termly fee — 3 terms per year'),
('SATs Monthly', 'sats', 110.00, 'monthly', 'Monthly tuition fee for SATs preparation'),
('KS1 Monthly', 'ks1', 90.00, 'monthly', 'Monthly tuition fee for KS1'),
('KS2 Monthly', 'ks2', 100.00, 'monthly', 'Monthly tuition fee for KS2'),
('KS3 Monthly', 'ks3', 110.00, 'monthly', 'Monthly tuition fee for KS3'),
('GCSE Monthly', 'gcse', 130.00, 'monthly', 'Monthly tuition fee for GCSE revision'),
('Easter Camp', 'easter_camp', 150.00, 'per_session', 'Easter holiday intensive course'),
('GCSE Easter Camp', 'easter_camp', 200.00, 'per_session', 'GCSE Easter holiday intensive');
