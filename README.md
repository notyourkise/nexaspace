<p align="center">
  <img src="public/images/nexaspace.webp" alt="NexaSpace" width="520">
</p>

<h1 align="center">NexaSpace</h1>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.12-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13.12">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Filament-5.6-FDAE4B?logo=filament&logoColor=black" alt="Filament 5.6">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4">
  <img src="https://img.shields.io/badge/Vite-8-646CFF?logo=vite&logoColor=white" alt="Vite 8">
  <img src="https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white" alt="MySQL 8">
  <img src="https://img.shields.io/badge/MikroTik-RouterOS-293239?logo=mikrotik&logoColor=white" alt="MikroTik RouterOS">
</p>

NexaSpace is a **multi-tenant SaaS** PropTech platform for boarding-house management.
The platform owner (developer) rents the system to boarding-house owners (juragan);
each juragan independently manages their own residents (anak kos), devices, and billing.

The core feature is an **Auto-Throttle WiFi Billing System**: overdue anak kos have
their MikroTik DHCP lease rate-limited automatically, and their connection is restored
after the juragan marks the payment as received.

## Live

| URL | Purpose |
|---|---|
| `https://nexaspace.site` | Public landing page |
| `https://nexaspace.site/admin` | Admin panel (developer & juragan) |
| `https://nexaspace.site/tenant` | Tenant portal (anak kos) |

## Three-Tier Role Model

| Role | Who | Panel | Scope |
|---|---|---|---|
| `developer` | NexaSpace platform owner (super admin) | `/admin` | Sees everything across all juragan |
| `juragan` | Boarding-house owner, a paying SaaS customer | `/admin` | Sees only their own anak kos / devices / bills |
| `tenant` | Anak kos, belongs to exactly one juragan | `/tenant` | Sees only their own bills |

Login emails are namespaced to each juragan's kos slug — e.g. kos "Mutiara" → juragan
logs in as `owner@mutiara.com`, anak kos as `room1@mutiara.com`. These are login
identifiers only (no real DNS / mailbox).

## Features

### Public Website

- Marketing landing page with pricing, testimonials, and contact links
- Feature comparison table (LITE / PRO / CUSTOM)
- Dedicated registration page at `/daftar/{plan}` — two-column layout with sticky plan summary
- Manual bank-transfer payment page at `/daftar/pembayaran/{registration}`
- WhatsApp confirmation CTA with prefilled payment details
- Success page at `/daftar/sukses`

### Admin Panel (Developer & Juragan)

- CRUD for users, devices, and billings — data-isolated per juragan
- Subscription management: developer marks juragan subscriptions paid/unpaid
- Registration management: developer reviews pending signups and runs "Setujui & Buat Akun" provisioning
- Device status controls: `active`, `throttled`, `blocked`; bulk MikroTik sync
- Billing bulk paid/unpaid actions; CSV export; PDF invoice download
- Dashboard: stats overview, quota widget, revenue chart (6 months), MikroTik health check, onboarding checklist

### Tenant Portal (Anak Kos)

- Personal billing history with status badges
- Outstanding bill summary widget
- Manual payment receipt upload for unpaid or throttled bills
- PDF invoice download per bill
- Self-service profile: update name, phone number, change password

### Auto-Throttle Billing

- `ThrottleOverdueTenantsJob` runs daily at `01:00` WITA
- Grace rule: throttle only when `today > due_date + 2 days` (H+3 or later)
- Overdue unpaid bills → `throttled`; active devices → `throttled`; MikroTik rate limit set to `256k/256k`
- Manually blocked devices remain unchanged

### Payment Recovery

- `BillingObserver` dispatches `RestoreDevicesJob` (async, via queue) when a bill becomes `paid`
- Throttled devices restored to `active`; MikroTik rate limit cleared
- Manually blocked devices remain unchanged

### Subscription Billing (Juragan → NexaSpace)

- `GenerateMonthlySubscriptionsJob` creates records on the 1st of each month (LITE Rp 199.000 / PRO Rp 499.000)
- `SuspendOverdueJuraganJob` runs daily at `01:30` WITA — sets `suspended_at`, blocks panel access
- `SubscriptionObserver` clears `suspended_at` and restores access when developer marks subscription paid

### Provisioning

- Developer approves a registration → `ProvisionTenantJob` creates juragan account + N anak-kos accounts
- Credentials emailed to the juragan's real email via `TenantProvisionedMail`

### Email Notifications (noreply@nexaspace.site)

| Mail class | Trigger |
|---|---|
| `BillingCreatedMail` | Monthly bills generated |
| `BillingReminderMail` | H-3 before billing due date |
| `BillingThrottledMail` | Anak kos throttled |
| `SubscriptionReminderMail` | H-3 before subscription due date |
| `JuraganSuspendedMail` | Juragan account suspended |

## Tech Stack

| Area | Technology |
|---|---|
| Backend | Laravel 13.12 |
| Runtime | PHP 8.4 |
| Admin and tenant UI | Filament 5.6 |
| Frontend build | Vite 8, Tailwind CSS 4 |
| Database | MySQL |
| Queue | Laravel database queue |
| Scheduler | Laravel scheduler |
| Router integration | `evilfreelancer/routeros-api-php` 1.7 |
| PDF generation | `barryvdh/laravel-dompdf` |
| Payment | Manual bank transfer (no payment gateway) |

## Requirements

- PHP 8.4
- Composer
- Node.js and npm
- MySQL
- MikroTik RouterOS API access for router synchronization
- SMTP credentials for email notifications

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run build
```

On Windows PowerShell, replace the copy command with:

```powershell
Copy-Item .env.example .env
```

Configure the database connection in `.env` before running migrations.

## Service Configuration

Add the following variables to `.env`:

```dotenv
# MikroTik (global fallback; individual juragan can override via admin panel)
MIKROTIK_HOST=
MIKROTIK_USER=admin
MIKROTIK_PASS=
MIKROTIK_PORT=8728

# Mail — use 'log' in local dev (emails written to storage/logs/laravel.log)
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@nexaspace.site
```

Never commit real credentials.

## Running Locally

```bash
composer run dev
```

This runs the Laravel server, queue listener, logs, and Vite development server.

For production:

```bash
php artisan queue:work
php artisan schedule:work
```

## Seeded Development Accounts

```text
Developer: admin@nexaspace.site

Juragan:   owner@mutiara.com   (Kos Mutiara, plan PRO, quota 40)
Anak kos:  room1@mutiara.com … room5@mutiara.com

Juragan:   owner@melati.com    (Kos Melati, plan LITE, quota 20)
Anak kos:  room1@melati.com  … room4@melati.com

Password for all seeded accounts: password
```

## Billing Flow

1. Scheduler dispatches `ThrottleOverdueTenantsJob` daily at `01:00` WITA.
2. Bills remain unaffected during the two-day grace period after the due date.
3. Overdue unpaid bills are marked `throttled`.
4. Active tenant devices are marked `throttled`.
5. MikroTik applies a `256k/256k` DHCP lease rate limit per juragan's router.
6. Developer or juragan marks billing as `paid` via admin panel.
7. `BillingObserver` dispatches `RestoreDevicesJob` (async).
8. Throttled devices restored to `active`; MikroTik rate limit cleared.

## Verification Commands

```bash
php artisan test
php artisan view:cache
composer validate --no-check-publish
npm run build
```

Runtime inspection:

```bash
php artisan route:list
php artisan schedule:list
php artisan migrate:status
php artisan about --only=environment,drivers
```

## Project Structure

```text
app/Services/BillingService.php                  Throttle and recovery logic
app/Services/MikroTikService.php                 RouterOS DHCP lease sync (per-juragan router support)
app/Observers/BillingObserver.php                Dispatches RestoreDevicesJob on billing → paid
app/Observers/SubscriptionObserver.php           Clears suspended_at on subscription → paid
app/Jobs/ThrottleOverdueTenantsJob.php           Scheduled throttle job
app/Jobs/RestoreDevicesJob.php                   Async device recovery after payment
app/Jobs/GenerateMonthlyBillsJob.php             Monthly bill generation for anak kos
app/Jobs/GenerateMonthlySubscriptionsJob.php     Monthly subscription generation for juragan
app/Jobs/SuspendOverdueJuraganJob.php            Suspend juragan after grace period
app/Jobs/ProvisionTenantJob.php                  Create juragan + anak kos accounts on approval
app/Jobs/SendBillingReminderJob.php              H-3 billing reminder (08:00 WITA)
app/Jobs/SendSubscriptionReminderJob.php         H-3 subscription reminder (08:05 WITA)
app/Http/Controllers/RegistrationController.php  /daftar/{plan} registration + payment flow
app/Http/Controllers/ExportController.php        CSV export for billing and subscriptions
app/Http/Controllers/InvoiceController.php       PDF invoice download via dompdf
app/Filament/Resources/                          Admin panel resources (RBAC-scoped)
app/Filament/Tenant/                             Tenant portal resources and pages
app/Filament/Widgets/                            Dashboard widgets (stats, chart, MikroTik, onboarding)
app/Mail/                                        Email notification classes
resources/views/landing.blade.php                Public marketing site
resources/views/daftar*.blade.php                Registration and payment views
resources/views/emails/                          Email templates
resources/views/invoices/billing.blade.php       PDF invoice template
routes/console.php                               Scheduler registration
routes/web.php                                   Web routes
```

## Testing

```text
125 tests, 125 passed
```

Test files cover: juragan data isolation, subscription isolation, billing service
grace-period logic, email notifications, CSV export isolation, PDF invoice access
control, tenant profile self-service, panel access (suspended juragan / tenant),
billing observer recovery, receipt upload authorization, registration flow,
provisioning, and tenant billing isolation.
