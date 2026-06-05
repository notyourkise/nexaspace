# NexaSpace - Project Guide

## Project Overview

NexaSpace is a smart PropTech platform for boarding-house management. Its main
feature is an Auto-Throttle WiFi Billing System: overdue tenants have their
MikroTik DHCP lease rate-limited automatically, and their connection is restored
after payment.

This document describes the current codebase. Keep it updated when behavior,
dependencies, or deployment assumptions change.

## Current Stack

| Area | Technology |
|---|---|
| Backend | Laravel 13.12 |
| Runtime | PHP 8.4 |
| Admin and tenant UI | Filament 5.6 |
| Frontend build | Vite 8, Tailwind CSS 4 |
| Local database | MySQL (`nexaspace`) |
| Queue | Laravel database queue |
| Scheduler | Laravel scheduler |
| Router integration | `evilfreelancer/routeros-api-php` 1.7 |
| Payment gateway | `midtrans/midtrans-php` 2.6 |

The repository still includes the default SQLite file for skeleton compatibility,
but the active local `.env` uses MySQL. Do not assume PostgreSQL or Supabase is
configured unless the target environment explicitly provides it.

## Implemented Features

### Public Website

- Marketing landing page at `/`
- Pricing, feature comparison, testimonials, contact links, and links to both
  application panels
- Branded image assets in `public/images`

### Authentication and Panels

- Juragan/admin panel at `/admin`
- Tenant panel at `/tenant`
- Custom login pages for both panels
- Panel access is role-based through `User::canAccessPanel()`:
  - `admin` users may access the admin panel
  - `tenant` users may access the tenant panel

### Admin Panel

- CRUD for users, devices, and billings
- User role filter and device count
- Device status filter and bulk status actions
- Manual bulk MikroTik synchronization
- Billing status filter and bulk paid/unpaid actions
- Dashboard statistics:
  - total tenants
  - active devices
  - unpaid bills
  - revenue for the current billing month

### Tenant Portal

- Read-only list of the authenticated tenant's own bills
- Tenant billing totals widget
- Midtrans Snap payment action for unpaid or throttled bills
- Manual payment receipt upload for unpaid or throttled bills

### Auto-Throttle Billing

- Daily queued job: `ThrottleOverdueTenantsJob`
- Schedule: every day at `01:00`
- Grace rule: throttle only when `today > due_date + 2 days`
- Overdue unpaid bills become `throttled`
- Active devices belonging to the tenant become `throttled`
- MikroTik DHCP lease rate limit is set to `256k/256k`
- Manually blocked devices are intentionally left unchanged

### Payment Recovery

- `BillingObserver` listens for bill status changes
- When a bill becomes `paid`, throttled devices for that tenant become `active`
- MikroTik DHCP lease rate limits are cleared
- Manually blocked devices remain blocked

### Midtrans Webhook

- Public endpoint: `POST /webhook/midtrans`
- CSRF exemption is configured in `bootstrap/app.php`
- Signature verification formula:

```text
SHA512(order_id + status_code + gross_amount + server_key)
```

- A bill is marked paid for:
  - `settlement`
  - `capture` with `fraud_status=accept`
- Updating the bill triggers `BillingObserver`, which restores throttled devices

## Domain Model

### `users`

| Column | Notes |
|---|---|
| `id` | Primary key |
| `name` | User display name |
| `email` | Unique login email |
| `password` | Hashed password |
| `role` | `admin` or `tenant` |
| `room_number` | Nullable tenant room number |
| `phone_number` | Nullable tenant phone number |

Relationships:

- `User hasMany Device`
- `User hasMany Billing`

Users are not soft-deleted. Deleting a user cascades to their devices and bills.

### `devices`

| Column | Notes |
|---|---|
| `id` | Primary key |
| `user_id` | Tenant foreign key |
| `device_name` | Human-readable device name |
| `mac_address` | MikroTik DHCP lease MAC address |
| `status` | `active`, `throttled`, or `blocked` |
| `deleted_at` | Soft delete timestamp |

### `billings`

| Column | Notes |
|---|---|
| `id` | Primary key |
| `user_id` | Tenant foreign key |
| `amount` | Billing amount in IDR |
| `billing_month` | Billing period |
| `due_date` | Payment deadline |
| `status` | `unpaid`, `paid`, or `throttled` |
| `payment_receipt` | Nullable uploaded receipt path |
| `deleted_at` | Soft delete timestamp |

## Important Files

| File | Responsibility |
|---|---|
| `app/Services/BillingService.php` | Throttle and restore business logic |
| `app/Services/MikroTikService.php` | RouterOS DHCP lease lookup and rate-limit updates |
| `app/Services/MidtransService.php` | Midtrans Snap token generation |
| `app/Http/Controllers/MidtransWebhookController.php` | Midtrans webhook verification and payment updates |
| `app/Observers/BillingObserver.php` | Device recovery after payment |
| `app/Jobs/ThrottleOverdueTenantsJob.php` | Queue wrapper for overdue checks |
| `routes/console.php` | Daily scheduler registration |
| `routes/web.php` | Landing page and webhook routes |
| `app/Providers/Filament/AdminPanelProvider.php` | Admin Filament panel |
| `app/Providers/Filament/TenantPanelProvider.php` | Tenant Filament panel |
| `resources/views/landing.blade.php` | Public marketing site |

## Configuration

Required service variables:

```dotenv
MIKROTIK_HOST=
MIKROTIK_USER=admin
MIKROTIK_PASS=
MIKROTIK_PORT=8728

MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
```

The application also expects the standard Laravel database, queue, cache,
session, mail, and filesystem variables. Never commit real credentials.

For receipt uploads, ensure the public storage link exists:

```bash
php artisan storage:link
```

For production, run both the queue worker and scheduler:

```bash
php artisan queue:work
php artisan schedule:work
```

## Local Setup

```bash
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run build
```

Useful development command:

```bash
composer run dev
```

Seeded development credentials:

```text
Admin:  admin@nexaspace.site
Tenant: tenant1@nexaspace.site
Tenant: tenant2@nexaspace.site
Password for seeded accounts: password
```

## Current Seed Scenario

`DatabaseSeeder` creates:

- 1 static admin
- 2 static tenants
- 8 random tenants
- 1 to 3 devices per tenant
- 3 billing months per tenant
- Current-month bill as `unpaid`
- Previous-month bills as `paid`

## Verification Commands

Run these before considering a change complete:

```bash
php artisan test
php artisan view:cache
composer validate --no-check-publish
npm run build
```

Useful runtime inspection:

```bash
php artisan route:list
php artisan schedule:list
php artisan migrate:status
php artisan about --only=environment,drivers
```

## Known Gaps and Backlog

The current automated tests are still Laravel skeleton examples. Add focused
coverage before production deployment:

1. Test role-based access for admin and tenant panels.
2. Test tenant billing query isolation.
3. Test grace-period boundaries in `BillingService`.
4. Test MikroTik success and failure behavior with mocked RouterOS calls.
5. Test billing observer recovery without affecting blocked devices.
6. Test Midtrans webhook signature validation and idempotency.
7. Test payment receipt upload permissions and file validation.
8. Review production queue worker, scheduler, storage link, and HTTPS setup.
9. Decide and document the production database target explicitly.

## Development Notes

- Keep MikroTik network calls behind `MikroTikService`.
- Keep payment gateway calls behind `MidtransService`.
- Update billing statuses through Eloquent so `BillingObserver` can react.
- Preserve tenant query scoping in tenant resources.
- Do not expose `.env` credentials in logs, tests, screenshots, or commits.
- `public/build` and published Filament assets are generated artifacts. Rebuild
  Vite assets with `npm run build` after frontend changes.
