-- Migration 002: Add missing columns to invoices and payments tables
-- Run via phpMyAdmin on live server (tpa_ims database)
-- Safe: uses plain ALTER TABLE — run each line separately if any fail

-- invoices: reminder tracking
ALTER TABLE invoices ADD COLUMN reminder_sent_at DATETIME NULL DEFAULT NULL;
ALTER TABLE invoices ADD COLUMN reminder_count INT NOT NULL DEFAULT 0;

-- invoices: payment token for online pay links
ALTER TABLE invoices ADD COLUMN payment_token VARCHAR(64) NULL DEFAULT NULL;

-- invoices: fee plan name label (used on invoice PDF)
ALTER TABLE invoices ADD COLUMN fee_plan_name VARCHAR(150) NULL DEFAULT NULL;

-- payments: gateway identifier (stripe, gocardless, manual)
ALTER TABLE payments ADD COLUMN gateway VARCHAR(50) NULL DEFAULT NULL;

-- payments: gateway transaction reference
ALTER TABLE payments ADD COLUMN gateway_payment_id VARCHAR(150) NULL DEFAULT NULL;
