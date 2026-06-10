-- Migration 001: Add reconciled column to payments table
-- Run once via phpMyAdmin on live server
-- Safe to run multiple times (uses IF NOT EXISTS check)

ALTER TABLE payments
  ADD COLUMN IF NOT EXISTS reconciled TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0=unverified BACS claim, 1=verified/reconciled';

-- Set existing payments as reconciled (they were entered manually or via gateway, so already verified)
UPDATE payments SET reconciled = 1 WHERE reconciled IS NULL OR reconciled != 0;
