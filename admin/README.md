# TPA Institute Management System — Setup Guide

## Prerequisites
- PHP 8.0+
- MySQL 8.0+
- Composer (for PHPMailer and mPDF)
- A web server (Apache/Nginx) with `mod_rewrite` or equivalent

---

## 1. Create the Database

```bash
mysql -u root -p < admin/database/tpa_schema.sql
```

This creates the `tpa_ims` database with all tables and seeds:
- Admin user: `admin@talentpoolacademy.com` / password: `Admin@TPA2026`
- Default fee structures
- All system settings

---

## 2. Configure Database Credentials

Edit `/admin/includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'tpa_ims');
define('DB_USER', 'your_mysql_user');
define('DB_PASS', 'your_mysql_password');

define('SITE_URL', 'https://yourdomain.com/admin');
define('PARENT_URL', 'https://yourdomain.com/parent');
```

---

## 3. Install PHP Dependencies

```bash
cd /path/to/tpaAG
composer require phpmailer/phpmailer mpdf/mpdf
```

---

## 4. Set Up the Cron Job (Automated Invoicing)

Add to your server's crontab (`crontab -e`):

```cron
# TPA Auto-Invoice (runs nightly at midnight)
0 0 * * * /usr/bin/php /path/to/tpaAG/admin/cron/auto-invoices.php >> /var/log/tpa-cron.log 2>&1
```

---

## 5. Configure Settings in Admin

Login at `https://yourdomain.com/admin/login.php` then go to **Settings**:

### General Tab
- Academy name, phone, email, addresses
- Bank account details (BACS)

### Email Tab (SMTP)
- Gmail: Host `smtp.gmail.com`, Port `587`, use App Password
- Or any custom SMTP server

### WhatsApp Tab (Meta Cloud API)
1. Create a [Meta Business Account](https://business.facebook.com)
2. Create a WhatsApp Business App at [developers.facebook.com](https://developers.facebook.com)
3. Add a phone number to the app — you get a **Phone Number ID**
4. Create a System User and generate a **permanent access token**
5. Submit message templates for pre-approval (required for business-initiated messages)
6. Enter the Phone Number ID and token in the WhatsApp settings tab

**Cost**: First 1,000 business-initiated conversations/month are **FREE**. After that, ~£0.05–0.08 per conversation.

### Payment Gateways Tab

**GoCardless (Direct Debit — recommended for recurring fees)**
1. Sign up at [gocardless.com](https://gocardless.com)
2. Generate an API access token
3. Start in `sandbox` mode for testing, switch to `live` for production
4. Fee: 1% + 20p per payment, capped at £4

**Stripe (Card payments)**
1. Sign up at [stripe.com](https://stripe.com)
2. Copy publishable + secret keys from Stripe Dashboard
3. Fee: 1.5% + 20p for European cards

---

## 6. Default Admin Credentials

| Field | Value |
|---|---|
| Email | `admin@talentpoolacademy.com` |
| Password | `Admin@TPA2026` |

> **Change the password immediately after first login via Settings → Users**

---

## Admin Pages Summary

| URL | Purpose |
|---|---|
| `/admin/login.php` | Login |
| `/admin/index.php` | Dashboard |
| `/admin/leads/` | Lead CRM (Kanban + Table) |
| `/admin/students/` | Student directory |
| `/admin/batches/` | Batch/group management |
| `/admin/attendance/mark.php` | Mark attendance register |
| `/admin/fees/` | Invoices overview |
| `/admin/fees/reminders.php` | Send WhatsApp/email fee reminders |
| `/admin/settings/` | All settings (SMTP, WhatsApp, payments) |
| `/admin/cron/auto-invoices.php` | Cron — automated invoice generation |

---

## File Structure

```
tpaAG/
├── admin/
│   ├── index.php          # Dashboard
│   ├── login.php
│   ├── logout.php
│   ├── leads/             # CRM
│   ├── students/          # Student management
│   ├── batches/           # Batch/group management
│   ├── attendance/        # Attendance
│   ├── fees/              # Fees & invoices
│   ├── settings/          # System settings
│   ├── cron/              # Automated invoice cron
│   ├── database/          # SQL schema
│   └── includes/          # Shared PHP files
│       ├── config.php
│       ├── db.php
│       ├── auth.php
│       ├── functions.php
│       ├── header.php
│       ├── footer.php
│       ├── WhatsAppService.php
│       └── EmailService.php
├── parent/                # Parent portal (extend separately)
└── vendor/                # Composer packages
```
