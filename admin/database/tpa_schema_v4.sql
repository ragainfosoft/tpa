-- =====================================================
-- TPA IMS — Schema v4 Migration
-- Reminders tracking + Payment gateway columns + new settings
-- =====================================================

USE `tpa_ims`;

-- ── track reminder state per invoice ──────────────────
ALTER TABLE `invoices`
  ADD COLUMN IF NOT EXISTS `reminder_sent_at` DATETIME NULL COMMENT 'Last reminder sent timestamp',
  ADD COLUMN IF NOT EXISTS `reminder_count` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'How many reminders sent';

-- ── gateway tracking on payments ─────────────────────
ALTER TABLE `payments`
  ADD COLUMN IF NOT EXISTS `gateway` VARCHAR(50) NULL COMMENT 'stripe | gocardless | manual',
  ADD COLUMN IF NOT EXISTS `gateway_payment_id` VARCHAR(200) NULL COMMENT 'Remote payment ID from gateway',
  ADD COLUMN IF NOT EXISTS `gateway_event_id` VARCHAR(200) NULL COMMENT 'Webhook event ID (idempotency)',
  ADD COLUMN IF NOT EXISTS `reconciled` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=matched to invoice';

-- prevent duplicate webhook replays
ALTER TABLE `payments`
  ADD UNIQUE KEY IF NOT EXISTS `ux_gateway_event` (`gateway_event_id`);

-- ── new settings keys ─────────────────────────────────
INSERT IGNORE INTO `settings` (`key`, `value`) VALUES
  -- Reminder rules
  ('reminder_fee_enabled',          '1'),
  ('reminder_fee_days_before',      '3'),
  ('reminder_fee_overdue_resend',   '7'),
  ('reminder_fee_channel',          'both'),
  ('reminder_absence_enabled',      '1'),
  ('reminder_absence_channel',      'both'),
  ('reminder_homework_enabled',     '0'),
  ('reminder_homework_channel',     'whatsapp'),
  -- Stripe webhook
  ('stripe_webhook_secret',         ''),
  -- GoCardless webhook
  ('gocardless_webhook_secret',     ''),
  -- Message templates
  ('wa_template_fee_reminder',      'Hi {parent_name}, this is a friendly reminder that {amount} is due for {child_name}. Invoice: {invoice_number}. Due: {due_date}. Please pay via BACS to Talent Pool Academy. Thank you.'),
  ('wa_template_absence',           'Hi {parent_name}, {child_name} was marked absent from {batch_name} on {date}. Please contact us if this is incorrect. — Talent Pool Academy'),
  ('wa_template_new_lead',          'Hi {parent_name}, thank you for your interest in Talent Pool Academy! We will be in touch shortly to arrange a FREE assessment for {child_name}. — TPA Team'),
  ('wa_template_assessment_booked', 'Hi {parent_name}, your assessment for {child_name} is confirmed for {date}. Please arrive 5 minutes early. — Talent Pool Academy'),
  ('wa_template_enrolled',          'Hi {parent_name}, welcome! {child_name} has been successfully enrolled at Talent Pool Academy (Ref: {student_ref}). We look forward to seeing them! — TPA');
