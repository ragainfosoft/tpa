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

Order matters: the CRM side must be live **before** Meta will verify the webhook,
because Meta checks the URL the moment you click Verify.

### Part A — CRM side (do this first)

1. Deploy the code (cPanel → Git Version Control → Update from Remote → Deploy),
   so `https://talentpoolacademy.com/api/facebook-leads.php` actually exists.
2. Run `migrations/003_facebook_lead_ads.sql` in phpMyAdmin.
3. Go to **Admin → Settings → Facebook Leads**.
4. Invent a **Webhook Verify Token** — any string, e.g. `tpa-leads-2026`. It is a
   shared password used once during the handshake; it is not issued by Meta.
   Type it in and click **Save**. It must be saved before Part C.

### Part B — Create the Meta app and get its secret

1. Go to [developers.facebook.com](https://developers.facebook.com) → **My Apps**
   → **Create App**.
2. Business portfolio: pick the one that owns the Facebook Page.
3. Use case: choose **Other**, then app type **Business** → name it (e.g. "TPA
   Leads") → Create. *Other* matters here: the narrower use cases pre-select a
   product bundle that can leave **Webhooks** out of the sidebar. If the list has
   no *Other* option, pick **Manage everything on your Page** and add Webhooks
   yourself from *Add products*.
4. In the left menu: **App settings → Basic**. Copy the **App Secret** (click
   *Show*) into Settings → Facebook Leads → **App Secret**, and Save.

### Part C — Point Meta at the webhook

1. In your app's left menu, find **Webhooks** (add it from *Add products* if it
   is not listed yet).
2. In the dropdown at the top, choose **Page**, then click
   **Subscribe to this object**.
3. Fill in:
   - **Callback URL:** `https://talentpoolacademy.com/api/facebook-leads.php`
   - **Verify Token:** the exact string you saved in Part A step 4
4. Click **Verify and Save**. Meta calls the URL with a challenge; our endpoint
   answers it, and the dialog closes. If it errors, see Troubleshooting below.
5. Back in the Page object's field list, find the row **`leadgen`** and click its
   **Subscribe** button. This is a separate click from step 4 — subscribing to
   the *object* does not subscribe you to the *field*.

### Part D — Generate a Page access token that never expires

The webhook only receives a lead **ID**; the answers are fetched with this token.

1. Open the [Graph API Explorer](https://developers.facebook.com/tools/explorer).
2. Top right: select your app. Set **User or Page** to *User Token*.
3. Under **Permissions**, add: `pages_show_list`, `pages_manage_metadata`,
   `pages_read_engagement`, `leads_retrieval`.
4. Click **Generate Access Token** and complete the login/consent popup, choosing
   the Talent Pool Academy Page. Copy the token — this is a *short-lived user*
   token, not the one you want yet.
5. Swap it for a long-lived one. In the Explorer's URL bar, run this as a **GET**
   (replace the three placeholders):

   ```
   /oauth/access_token?grant_type=fb_exchange_token&client_id=YOUR_APP_ID&client_secret=YOUR_APP_SECRET&fb_exchange_token=SHORT_LIVED_USER_TOKEN
   ```

   Copy `access_token` from the response — a long-lived **user** token.
6. Paste that long-lived user token into the Explorer's token box, then GET:

   ```
   /me/accounts
   ```

   In the response, find your Page. Its `id` is the **Page ID** and its
   `access_token` is the **Page token** — derived from a long-lived user token,
   these do not expire.
7. Paste both into Settings → Facebook Leads (**Facebook Page ID**,
   **Page Access Token (long-lived)**) and Save.
8. Sanity check the token in the
   [Access Token Debugger](https://developers.facebook.com/tools/debug/accesstoken):
   Type should be *Page* and Expires should say **Never**.

### Part E — Subscribe the app to the Page

The step most setups miss. Part C told Meta *which URL* to call; this tells it
*which Page* to watch.

In the Graph API Explorer:

- Method: **POST** (not the default GET)
- Token: the **Page** token from Part D — not the user token
- Path: `/YOUR_PAGE_ID/subscribed_apps?subscribed_fields=leadgen`

Substitute the real numeric Page ID and drop the braces — `/1029384756/…`, not
`/{1029384756}/…`.

A `{"success": true}` response means it is wired up. To confirm later, GET
`/YOUR_PAGE_ID/subscribed_apps` and check your app is listed.

If you get *"Object with ID … does not exist, cannot be loaded due to missing
permissions, or does not support this operation"*, work through those three
bullets in order — a user token, a GET, or an unsubstituted placeholder all
produce that same message.

### Part F — Switch on and test

1. Settings → Facebook Leads → set to **✅ Enabled** → Save.
2. Click **Test connection** — it should list the lead forms on your Page. If it
   lists nothing, the Page has no lead forms yet, which is fine at this stage.
3. Open the
   [Lead Ads Testing Tool](https://developers.facebook.com/tools/lead-ads-testing),
   pick your Page and form, click **Preview form**, submit it, then **Track**.
4. The lead should appear in **Admin → Leads** within a few seconds, tagged
   *Facebook Ad*, and a row should show in Settings → Facebook Leads →
   *Recent webhook activity*.

### A note on App Review

While the app is in **Development** mode, `leads_retrieval` works for people who
admin both the app and the Page — enough for you to test end to end. Before the
app is switched to **Live**, Meta requires **Advanced Access** for
`leads_retrieval` and `pages_show_list`, which means submitting for App Review
(App Review → Permissions and Features). Real campaign leads will not be
retrievable until that is granted, so start the review early.

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
| "The URL couldn't be validated" on Verify and Save | Code not deployed yet, or the URL is wrong — open it in a browser, it should say *Verification failed* (a 403 page proves the file is reachable) |
| Meta says "verify token mismatch" | Token in Settings ≠ token in Meta, or not saved yet |
| Verified fine, but nothing arrives | `leadgen` field not subscribed (Part C step 5), or the app not subscribed to the Page (Part E) |
| Works in the testing tool, not for real leads | `leads_retrieval` still on Standard Access — needs App Review |
| Rows with status `error` | Usually an expired Page token — regenerate it |
| Nothing at all arrives | Integration left **Disabled** in Settings |
| `skipped` | The form asked for neither email nor phone |
