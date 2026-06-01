# NexaSpace

NexaSpace is a smart PropTech platform for boarding-house management. It helps
property owners manage tenants, devices, and billing from one dashboard while
automatically limiting WiFi access for overdue tenants through MikroTik.

When a payment is completed, the tenant's connection is restored automatically.
Tenants can also review their bills, pay through Midtrans Snap, or upload a
manual payment receipt from their own portal.

## Features

### Juragan Panel

- Dashboard statistics for tenants, active devices, unpaid bills, and monthly
  revenue
- User, device, and billing management
- Device status controls: `active`, `throttled`, and `blocked`
- Bulk billing status updates
- Manual MikroTik synchronization

### Tenant Portal

- Personal billing history
- Outstanding bill summary
- Midtrans Snap payments
- Manual payment receipt uploads
- Strict query scoping so tenants only see their own bills

### Smart WiFi Billing

- Scheduled overdue billing checks every day at `01:00`
- Two-day grace period after the billing due date
- Automatic MikroTik DHCP lease rate limit of `256k/256k`
- Automatic connection recovery after payment
- Manually blocked devices remain blocked

### Public Website

- Marketing landing page
- Feature comparison
- Pricing plans
- Testimonials and contact links
- Entry points for both application panels

## Tech Stack

| Area | Technology |
|---|---|
| Backend | Laravel 13.12 |
| Runtime | PHP 8.4 |
| UI | Filament 5.6 |
| Frontend build | Vite 8, Tailwind CSS 4 |
| Local database | MySQL |
| Queue | Laravel database queue |
| Scheduler | Laravel scheduler |
| Router integration | `evilfreelancer/routeros-api-php` |
| Payment gateway | `midtrans/midtrans-php` |

## Application URLs

| URL | Purpose |
|---|---|
| `/` | Public landing page |
| `/admin` | Juragan dashboard |
| `/tenant` | Tenant portal |
| `POST /webhook/midtrans` | Midtrans payment notification endpoint |

## Requirements

- PHP 8.4 or compatible PHP 8.3+
- Composer
- Node.js and npm
- MySQL for the default local setup
- MikroTik RouterOS API access for router synchronization
- Midtrans credentials for online payment processing

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

On Windows PowerShell, replace the environment copy command with:

```powershell
Copy-Item .env.example .env
```

Configure the database connection in `.env` before running migrations.

## Service Configuration

Add the MikroTik and Midtrans variables to `.env`:

```dotenv
MIKROTIK_HOST=
MIKROTIK_USER=admin
MIKROTIK_PASS=
MIKROTIK_PORT=8728

MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
```

Never commit real credentials.

## Running Locally

Start the complete development environment:

```bash
composer run dev
```

This runs the Laravel server, queue listener, logs, and Vite development server.

For production, keep the queue worker and scheduler running:

```bash
php artisan queue:work
php artisan schedule:work
```

## Seeded Accounts

The development seeder creates one admin and ten tenants with sample devices and
billing records.

```text
Admin:  admin@nexaspace.site
Tenant: tenant1@nexaspace.site
Tenant: tenant2@nexaspace.site
Password: password
```

## Billing Flow

1. The scheduler dispatches `ThrottleOverdueTenantsJob` daily.
2. Bills remain unaffected during the two-day grace period.
3. Overdue unpaid bills are marked as `throttled`.
4. Active tenant devices are marked as `throttled`.
5. MikroTik applies a `256k/256k` DHCP lease rate limit.
6. After a verified payment, `BillingObserver` restores throttled devices.
7. MikroTik clears the rate limit and full speed returns.

The webhook verifies Midtrans notifications using:

```text
SHA512(order_id + status_code + gross_amount + server_key)
```

## Verification

Run these commands before considering a change complete:

```bash
php artisan test
php artisan view:cache
composer validate --no-check-publish
npm run build
```

Useful runtime inspection commands:

```bash
php artisan route:list
php artisan schedule:list
php artisan migrate:status
php artisan about --only=environment,drivers
```

## Project Structure

```text
app/Services/BillingService.php                 Billing throttle and recovery logic
app/Services/MikroTikService.php                RouterOS DHCP lease synchronization
app/Services/MidtransService.php                Midtrans Snap token generation
app/Http/Controllers/MidtransWebhookController.php
                                                Midtrans webhook handling
app/Observers/BillingObserver.php               Recovery trigger after payment
app/Jobs/ThrottleOverdueTenantsJob.php          Scheduled queue job
app/Providers/Filament/                         Admin and tenant panels
resources/views/landing.blade.php               Public website
routes/console.php                              Scheduler registration
routes/web.php                                  Web routes
```

## Testing Status

The current automated suite still contains the Laravel skeleton tests. Before
production deployment, add focused coverage for:

- Role-based panel access
- Tenant billing isolation
- Grace-period boundaries
- MikroTik success and failure behavior
- Device recovery after payment
- Midtrans signature validation and webhook idempotency
- Receipt upload authorization and validation

## Development Guide

See [`CLAUDE.md`](CLAUDE.md) for the detailed project guide, implementation
notes, and current backlog.
