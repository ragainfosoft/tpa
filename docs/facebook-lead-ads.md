# Facebook / Instagram Lead Ads → Lead CRM

Leads submitted on a Meta lead-ad form land in **Admin → Leads** automatically,
with the same dedupe rules as the website enquiry form.

## Moving parts

| File | Role |
|---|---|
| `api/facebook-leads.php` | Public webhook Meta calls. Verifies the signature, fetches the lead, files it. |
| `admin/includes/FacebookLeadService.php` | Graph API calls, field mapping, lead insert, audit log. |
| `admin/cron/fb-lead-sync.php` | Hourly safety net — re-reads recent leads and backfills anything the webhook missed. |
| `admin/settings/index.php` → **Facebook Leads** tab | Credentials, filing rules, connection test, recent activity. |
| `migrations/003_facebook_lead_ads.sql` | `fb_lead_log` table, settings rows, new lead sources. |

## One-time setup

1. Run `migrations/003_facebook_lead_ads.sql` in phpMyAdmin.
2. In **Meta for Developers**, open your app → add the **Webhooks** product.
3. Subscribe the **Page** object to the **`leadgen`** field, using:
   - Callback URL: `https://talentpoolacademy.com/api/facebook-leads.php`
   - Verify token: any string you invent (paste the same one into Settings)
4. Generate a long-lived **Page access token** with the `leads_retrieval` and
   `pages_show_list` permissions. Subscribe the app to the Page.
5. In **Admin → Settings → Facebook Leads**, fill in the verify token, app secret,
   Page ID and Page token, set the integration to **Enabled**, and Save.
6. Click **Test connection** — it should list the lead forms on your Page.
7. Submit a test lead from Meta's
   [Lead Ads Testing Tool](https://developers.facebook.com/tools/lead-ads-testing).

## Field mapping

Meta's standard fields (`full_name`, `first_name`/`last_name`, `email`,
`phone_number`) map directly. Custom questions get per-form slugs, so they are
matched on keywords:

| Contains | Goes to |
|---|---|
| `child` + `name` | Child's Name |
| `year`, `grade`, `class` | Child's Year Group |
| `subject`, `course`, `programme` | Course Interest |
| `centre`, `center`, `location`, `branch` | Centre (normalised like the website form) |

Everything unmatched is appended to the lead's **Notes**, along with the
campaign, ad set and ad name — nothing the parent typed is lost.

## Behaviour worth knowing

- **Idempotent.** Every `leadgen_id` is recorded in `fb_lead_log` with a unique
  key, so Meta's retries and the cron can never create the same lead twice.
- **Duplicates.** A matching phone or email files a follow-up note against the
  existing lead instead of creating a second one.
- **Always answers 200.** A non-200 makes Meta retry for days and eventually
  disable the subscription, so failures are written to `fb_lead_log` and the PHP
  error log instead of being returned to Meta.
- **Fails closed on signatures.** With no app secret saved, every POST is
  rejected — otherwise anyone knowing the URL could inject leads.

## Troubleshooting

Check **Settings → Facebook Leads → Recent webhook activity** first.

| Symptom | Cause |
|---|---|
| Meta says "verify token mismatch" | Token in Settings ≠ token in Meta, or not saved yet |
| Rows with status `error` | Usually an expired Page token — regenerate it |
| Nothing at all arrives | App not subscribed to the Page, or integration left Disabled |
| `skipped` | The form asked for neither email nor phone |
