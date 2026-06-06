# NexaSpace - Project Guide

---

## Instruksi Wajib untuk AI Agent

**Baca bagian ini lebih dulu sebelum melakukan apapun.**

### 1. Onboarding Wajib di Setiap Sesi Baru

Setiap AI agent atau sesi baru **wajib** memulai dengan mempelajari project secara menyeluruh:

1. Baca file `CLAUDE.md` ini dari awal hingga akhir tanpa terkecuali.
2. Baca file `CHANGELOG.md` untuk memahami riwayat dan konteks perubahan terbaru.
3. Pelajari seluruh codebase secara aktif: models, services, jobs, observers, resources Filament, panel providers, routes, migrations, seeders, views, dan konfigurasi.
4. Pahami domain bisnis: alur throttle otomatis, alur pemulihan pembayaran, integrasi MikroTik, dan alur pembayaran manual (transfer bank + WhatsApp).

Jangan asumsikan sesuatu tanpa membaca kode sumbernya langsung. Jika ada keraguan, baca file terkait terlebih dahulu.

### 2. Dokumentasi Wajib di CHANGELOG.md

**Setiap perubahan — sekecil apapun — wajib dicatat di `CHANGELOG.md` setelah selesai dikerjakan.**

Ini berlaku untuk semua jenis pekerjaan: perbaikan bug, penambahan fitur, refactor, perubahan konfigurasi, perubahan view/UI, perubahan migrasi, dan lain-lain.

#### Format Entri CHANGELOG

Setiap entri harus mengikuti format berikut:

```
### HH:MM WITA — [Judul Singkat Perubahan]

**Apa yang Diubah:**
Deskripsi lengkap perubahan yang dilakukan. Sebutkan nama file, fungsi, atau komponen yang terdampak.

**Alasan Perubahan:**
Jelaskan *mengapa* perubahan ini dilakukan. Apakah untuk memperbaiki bug, meningkatkan performa, memenuhi permintaan pengguna, atau alasan teknis lainnya.

**Hasil Akhir:**
Deskripsi kondisi sistem setelah perubahan. Apa yang sekarang bisa dilakukan yang sebelumnya tidak bisa, atau apa yang sekarang tidak lagi menjadi masalah.
```

#### Aturan Timestamp

- Gunakan zona waktu **WITA (UTC+8) — Kota Balikpapan, Kalimantan Timur**.
- Format jam: `HH:MM WITA`.
- Format tanggal sesi: `## [Sesi Kerja] — D MMMM YYYY` (contoh: `## [Sesi Kerja] — 5 Juni 2026`).
- Jika beberapa perubahan dalam satu sesi, kelompokkan di bawah satu header sesi yang sama.

#### Aturan Kelengkapan

- Tabel perbandingan (Sebelum → Sesudah) **sangat dianjurkan** untuk perubahan UI atau konfigurasi yang memiliki nilai lama dan nilai baru yang jelas.
- Cantumkan nama file lengkap dengan path relatif dari root project.
- Jangan meringkas terlalu pendek — detail lebih baik daripada kurang.

---

## Project Overview

NexaSpace is a **multi-tenant SaaS** PropTech platform. The developer (platform
owner) rents the platform to many boarding-house owners ("juragan"); each juragan
independently manages their own kos and their own residents ("anak kos"). Its main
feature is an Auto-Throttle WiFi Billing System: overdue anak kos have their
MikroTik DHCP lease rate-limited automatically, and their connection is restored
after payment.

### Three-Tier Role Model

| Role | Who | Panel | Scope |
|---|---|---|---|
| `developer` | NexaSpace platform owner (super admin) | `/admin` | Sees everything across all juragan |
| `juragan` | Boarding-house owner, a paying SaaS customer | `/admin` | Sees only their own anak kos / devices / bills |
| `tenant` | Anak kos, belongs to exactly one juragan | `/tenant` | Sees only their own bills |

Anak kos point to their juragan via `users.juragan_id`. Each juragan has a
`kos_slug` that namespaces login emails: kos "Mutiara" → juragan logs in as
`owner@mutiara.com`, anak kos as `room1@mutiara.com` … `roomN@mutiara.com`.
These emails are **login identifiers only** (no real DNS / mailbox).

> The full SaaS transformation is phased. See "Known Gaps and Backlog → SaaS
> Multi-Tenant Roadmap". Phase 1 (role + data model foundation) is complete.

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
| Payment | Manual bank transfer + WhatsApp confirmation (no payment gateway) |

The repository still includes the default SQLite file for skeleton compatibility,
but the active local `.env` uses MySQL. Do not assume PostgreSQL or Supabase is
configured unless the target environment explicitly provides it.

## Production Deployment

- **Domain:** [nexaspace.site](https://nexaspace.site)
- **Admin panel:** `https://nexaspace.site/admin`
- **Tenant panel:** `https://nexaspace.site/tenant`
- Status: **Live / Online**

## Implemented Features

### Public Website

- Marketing landing page at `/`
- Pricing section with feature comparison table (LITE / PRO / CUSTOM columns)
- Testimonials, contact links, and links to both application panels
- Branded image assets in `public/images`
- Dedicated registration page at `/daftar/{plan}` (lite|pro|custom): two-column
  layout with sticky plan summary card and form + manual bank-transfer payment page
- Signed manual payment page at `/daftar/pembayaran/{registration}` with Indonesian
  bank options, fictitious account numbers for testing, and WhatsApp confirmation CTA
- Success page at `/daftar/sukses` after payment

### Authentication and Panels

- Admin panel at `/admin` (shared by `developer` and `juragan`)
- Tenant panel at `/tenant` (anak kos)
- Custom login pages for both panels
- Panel access is role-based through `User::canAccessPanel()`:
  - `developer` and `juragan` users may access the admin panel; juragan is also
    blocked when `users.suspended_at IS NOT NULL`
  - `tenant` users may access the tenant panel; also blocked when their
    `juragan.suspended_at IS NOT NULL`
- Role helpers on `User`: `isDeveloper()`, `isJuragan()`, `isTenant()`
- **RBAC data isolation (Phase 2, enforced):** each admin resource overrides
  `getEloquentQuery()` so a juragan sees only their own anak kos / devices / bills;
  the developer sees everything. `RegistrationResource` is developer-only (`canAccess()`,
  `canViewAny()`, `shouldRegisterNavigation()`). Create forms scope their selects and
  re-enforce ownership server-side via `mutateFormDataBeforeCreate()`. `StatsOverview`
  shows platform-wide stats to the developer and own-kos stats to a juragan.

### Admin Panel

- CRUD for users, devices, and billings
- User role filter and device count
- Device status filter and bulk status actions
- Manual bulk MikroTik synchronization
- Billing status filter and bulk paid/unpaid actions
- Subscription management: list juragan subscriptions; developer bulk mark paid/unpaid;
  juragan sees only their own subscriptions
- Registration management: developer-only list of pending signups; columns include
  `payment_status` badge (Lunas / Belum Bayar); bulk actions to mark payment
  paid/unpaid manually; bulk "Setujui & Buat Akun" action
- Dashboard statistics:
  - total tenants
  - active devices
  - unpaid bills
  - revenue for the current billing month
  - juragan subscription status for current month (own-kos view)

### Tenant Portal

- Read-only list of the authenticated tenant's own bills
- Tenant billing totals widget
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

### Subscription Billing (Juragan → NexaSpace)

- Monthly subscriptions generated automatically on the 1st via `GenerateMonthlySubscriptionsJob`
  - LITE = Rp 199.000 / bulan, PRO = Rp 499.000 / bulan, CUSTOM = manual
  - Due date: 10th of each month; grace period: 2 days after due date
- `SuspendOverdueJuraganJob` runs daily at `01:30`: sets `juragan.suspended_at = now()` and
  marks subscription `overdue` — blocks juragan + all anak kos from panels
- `SubscriptionObserver`: when subscription status becomes `paid`, clears `suspended_at`
- Juragan pays via manual bank transfer; developer marks subscription paid manually in SubscriptionResource
- When subscription status becomes `paid`, `SubscriptionObserver` restores panel access

### Public Registration & Payment Flow

- Landing page pricing buttons navigate to `/daftar/{plan}` (dedicated page, not modal)
- `/daftar/{plan}` shows two-column layout: sticky plan summary + registration form
- On submit (LITE/PRO): AJAX POST `/register` saves the registration, then returns
  signed `payment_url` for `/daftar/pembayaran/{registration}`. The payment page
  shows manual bank-transfer instructions and a WhatsApp confirmation link with
  the registration/payment details prefilled.
- On submit (CUSTOM): redirect to WhatsApp consultation
- Developer verifies the WhatsApp confirmation/bukti transfer, marks
  `payment_status = paid` manually in `RegistrationResource`, then approves the
  registration for provisioning.

### Payment Flow (Manual)

All payments — anak kos billing and juragan subscriptions — are handled via manual
bank transfer. No payment gateway is used.

- **Anak kos billing**: tenant uploads receipt in the tenant portal; developer or
  juragan marks billing as `paid` via bulk action in admin panel. `BillingObserver`
  fires → dispatches `RestoreDevicesJob` → throttled devices restored.
- **Juragan subscription**: juragan transfers manually and confirms via WhatsApp;
  developer marks subscription as `paid` via bulk action in `SubscriptionResource`.
  `SubscriptionObserver` fires → clears `suspended_at` → panel access restored.

## Domain Model

### `users`

| Column | Notes |
|---|---|
| `id` | Primary key |
| `name` | User display name |
| `email` | Unique login email (namespaced to kos slug for juragan/anak kos) |
| `password` | Hashed password |
| `role` | `developer`, `juragan`, or `tenant` |
| `juragan_id` | Nullable self-FK → `users.id`; set on anak kos, null on developer/juragan |
| `kos_name` | Nullable; juragan's boarding-house name |
| `kos_slug` | Nullable unique; juragan's login-email namespace (e.g. `mutiara`) |
| `plan` | Nullable enum `lite`/`pro`/`custom`; juragan's subscription package |
| `room_quota` | Anak-kos account capacity for the juragan's plan (20/40/50+) |
| `monthly_rate` | Anak kos monthly rent in IDR (used by auto-generate bills) |
| `room_number` | Nullable anak kos room number |
| `phone_number` | Nullable phone number |
| `suspended_at` | Nullable timestamp; set by `SuspendOverdueJuraganJob`; cleared by `SubscriptionObserver` |
| `mikrotik_host` | Nullable; per-juragan router host/IP — overrides global `.env` |
| `mikrotik_port` | Nullable integer; per-juragan API port (default 8728) |
| `mikrotik_user` | Nullable; per-juragan router username |
| `mikrotik_pass` | Nullable; per-juragan router password |
| `contact_email` | Nullable; juragan's real email (for notifications); populated from `registrations.email` on provisioning |

Relationships:

- `User hasMany Device`
- `User hasMany Billing`
- `User belongsTo User as juragan` (anak kos → their juragan)
- `User hasMany User as anakKos` (juragan → their anak kos)

Users are not soft-deleted. Deleting a juragan sets dependent anak kos
`juragan_id` to null (`nullOnDelete`). Deleting an anak kos cascades to their
devices and bills.

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

### `registrations`

Public subscription requests from the landing page (formerly `inquiries`).

| Column | Notes |
|---|---|
| `id` | Primary key |
| `name` | Juragan / PIC name |
| `kos_name` | Boarding-house name |
| `email` | Juragan's real email (destination for provisioned credentials) |
| `phone` | WhatsApp number |
| `room_count` | Declared number of rooms |
| `plan` | `lite`, `pro`, or `custom` |
| `message` | Nullable free text |
| `status` | `pending` → `approved` → `active`, or `rejected` |
| `payment_status` | `unpaid` or `paid`; developer updates this manually after verifying WhatsApp/payment proof |
| `midtrans_order_id` | Nullable; legacy column — no longer used (Midtrans removed) |

### `subscriptions`

Juragan monthly subscription records (juragan → NexaSpace billing).

| Column | Notes |
|---|---|
| `id` | Primary key |
| `juragan_id` | FK → `users.id`; cascadeOnDelete |
| `amount` | Subscription amount in IDR (199000 for LITE, 499000 for PRO) |
| `subscription_month` | First day of the billing month |
| `due_date` | Payment deadline (10th of the month) |
| `status` | `unpaid`, `paid`, or `overdue` |
| `deleted_at` | Soft delete timestamp |

Relationship: `Subscription belongsTo User as juragan`

## Important Files

| File | Responsibility |
|---|---|
| `app/Services/BillingService.php` | Throttle and restore business logic |
| `app/Services/MikroTikService.php` | RouterOS DHCP lease lookup and rate-limit updates |
| `app/Http/Controllers/RegistrationController.php` | `show()` renders `/daftar/{plan}`; `store()` saves registration + returns signed manual payment URL or WA URL; `payment()` renders `/daftar/pembayaran/{registration}` |
| `app/Filament/Resources/RegistrationResource.php` | Developer-only review; payment_status badge; "Setujui & Buat Akun" provisioning |
| `app/Filament/Resources/SubscriptionResource.php` | Juragan subscription list; developer bulk mark paid/unpaid |
| `app/Models/Subscription.php` | Subscription model; `juragan()` relation; date + integer casts; soft deletes |
| `app/Observers/SubscriptionObserver.php` | Clears `juragan.suspended_at` when subscription status → `paid` |
| `app/Jobs/GenerateMonthlySubscriptionsJob.php` | Creates monthly subscription records for LITE/PRO juragan; runs 1st of month 00:05 |
| `app/Jobs/SuspendOverdueJuraganJob.php` | 2-day grace: sets subscription → `overdue`, sets `juragan.suspended_at`; runs daily 01:30 |
| `app/Jobs/ProvisionTenantJob.php` | Creates juragan + N anak-kos accounts and emails credentials |
| `app/Mail/TenantProvisionedMail.php` | Credentials email sent to the juragan after approval |
| `app/Observers/BillingObserver.php` | Dispatches `RestoreDevicesJob` when billing status → `paid` |
| `app/Jobs/ThrottleOverdueTenantsJob.php` | Queue wrapper for overdue anak kos checks |
| `app/Jobs/RestoreDevicesJob.php` | Async job: restores throttled devices via `BillingService` after payment |
| `app/Http/Controllers/ExportController.php` | CSV export: `billing()` and `subscription()`; data isolation; BOM for Excel |
| `app/Http/Controllers/InvoiceController.php` | PDF invoice: `download(Request, Billing)` via dompdf; access control: developer/juragan/tenant |
| `app/Filament/Tenant/Pages/EditProfile.php` | Tenant self-service: profile form (name, phone) + password change |
| `app/Filament/Widgets/JuraganOnboardingWidget.php` | Checklist onboarding juragan; hidden when complete |
| `app/Filament/Widgets/MikroTikStatusWidget.php` | Developer-only router health check; polls every 5 min |
| `app/Filament/Widgets/RevenueChartWidget.php` | Line chart: revenue last 6 months; scoped per juragan |
| `resources/views/invoices/billing.blade.php` | DomPDF invoice template: brand header, parties, table, status badge, total |
| `app/Mail/BillingCreatedMail.php` | Email ke juragan saat tagihan bulanan digenerate |
| `app/Mail/BillingReminderMail.php` | Email H-3 sebelum jatuh tempo tagihan anak kos |
| `app/Mail/BillingThrottledMail.php` | Email ke juragan saat anak kos di-throttle |
| `app/Mail/SubscriptionReminderMail.php` | Email H-3 sebelum jatuh tempo subscription NexaSpace |
| `app/Mail/JuraganSuspendedMail.php` | Email ke juragan saat akun disuspend |
| `app/Jobs/SendBillingReminderJob.php` | Daily 08:00 WITA: kirim reminder tagihan H-3 |
| `app/Jobs/SendSubscriptionReminderJob.php` | Daily 08:05 WITA: kirim reminder subscription H-3 |
| `routes/console.php` | Scheduler: throttle (01:00), generate-bills (1st 00:01), generate-subscriptions (1st 00:05), suspend-juragan (01:30) |
| `routes/web.php` | Landing page, `/daftar/{plan}`, `/daftar/sukses`, `/register`, webhook routes |
| `app/Providers/Filament/AdminPanelProvider.php` | Admin Filament panel |
| `app/Providers/Filament/TenantPanelProvider.php` | Tenant Filament panel |
| `resources/views/landing.blade.php` | Public marketing site (no modal — pricing links to `/daftar/{plan}`) |
| `resources/views/daftar.blade.php` | Registration page: sticky plan summary + form |
| `resources/views/daftar-pembayaran.blade.php` | Manual payment page: bank transfer options + WhatsApp confirmation |
| `resources/views/daftar-sukses.blade.php` | Post-payment success page |

## Configuration

Required service variables:

```dotenv
MIKROTIK_HOST=
MIKROTIK_USER=admin
MIKROTIK_PASS=
MIKROTIK_PORT=8728
```

Mail (required for Phase 4 provisioning — credentials are emailed to the juragan):

```dotenv
MAIL_MAILER=smtp            # 'log' in local dev (emails written to storage/logs/laravel.log)
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@nexaspace.site
```

In local dev `MAIL_MAILER=log`, so provisioned credentials appear in
`storage/logs/laravel.log` instead of being sent. The `TenantProvisionedMail`
envelope forces the sender to `noreply@nexaspace.site` regardless of
`MAIL_FROM_ADDRESS`. For production, configure a working SMTP transport so
juragan actually receive their accounts.

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
Developer: admin@nexaspace.site
Juragan:   owner@mutiara.com   (Kos Mutiara, plan PRO, quota 40)
Juragan:   owner@melati.com    (Kos Melati, plan LITE, quota 20)
Anak kos:  room1@mutiara.com … room5@mutiara.com
Anak kos:  room1@melati.com  … room4@melati.com
Password for seeded accounts: password
```

## Current Seed Scenario

`DatabaseSeeder` creates a three-tier structure:

- 1 developer (super admin)
- 2 juragan, each with `kos_name`, `kos_slug`, `plan`, `room_quota`
- Anak kos per juragan (5 for Mutiara, 4 for Melati), emailed `roomN@<slug>.com`
- 1 to 3 devices per anak kos
- 3 billing months per anak kos (current = `unpaid`, previous = `paid`)
- Each anak kos has a random `monthly_rate` (Rp 150k–350k)

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

### SaaS Multi-Tenant Roadmap

The platform is being transformed from a single-kos app into a multi-juragan SaaS,
phased and approved per phase. Decisions locked: email/username namespacing (no real
subdomains), two-layer billing (juragan→NexaSpace + anak kos→juragan), anak-kos
accounts auto-generated to the package quota, credentials sent from
`noreply@nexaspace.site`.

- ✅ **Phase 1 — Role & data model foundation (DONE):** `developer`/`juragan`/`tenant`
  roles, `juragan_id` self-FK, `kos_name`/`kos_slug`/`plan`/`room_quota` columns,
  role helpers, 3-tier seeder.
- ✅ **Phase 2 — RBAC & data isolation (DONE):** every admin resource scopes
  `getEloquentQuery()` to the juragan's own anak kos / devices / bills; create forms
  enforce ownership server-side; `RegistrationResource` is developer-only; `StatsOverview`
  is role-scoped. Covered by `tests/Feature/JuraganIsolationTest.php`.
- ✅ **Phase 3 — Public registration flow (DONE):** landing modal is now a subscription
  registration form (nama juragan, nama kos, email, phone, room_count, plan, message)
  posting to `POST /register` → saved to `registrations` with `pending` status, then
  redirects to WhatsApp with a pre-filled confirmation message. Optional "Konsultasi
  via WhatsApp" link (no form). `inquiries` table migrated → `registrations`. Covered by
  `tests/Feature/RegistrationFlowTest.php`.
- ✅ **Phase 4 — Approval & provisioning (DONE):** the developer's "Setujui & Buat Akun"
  bulk action on `RegistrationResource` runs `ProvisionTenantJob` (via `dispatchSync`),
  which creates the juragan account (`owner@<slug>.com`) plus N anak-kos accounts
  (`room1@<slug>.com` … `roomN@<slug>.com`, N = package quota) inside a DB transaction,
  sets the initial password for both the juragan and anak-kos accounts to `password`,
  emails the credentials to the registration email via `TenantProvisionedMail`
  (from `noreply@nexaspace.site`), and flips the registration to `active`. Idempotent
  (skips already-active), slug collisions auto-resolved (`mutiara` → `mutiara-2`).
  Covered by `tests/Feature/ProvisioningTest.php`.
- ✅ **Phase 5 — Subscription billing (juragan → NexaSpace) (DONE):** `subscriptions`
  table, `GenerateMonthlySubscriptionsJob` (1st of month), `SuspendOverdueJuraganJob`
  (daily 01:30, 2-day grace), `SubscriptionObserver` (auto-unsuspend on paid),
  `SubscriptionResource` (developer bulk actions), `suspended_at`
  on users, panel access blocked for suspended juragan + their anak kos.
- ✅ **Phase 5b — Public registration + manual payment (DONE):** `/daftar/{plan}`
  dedicated page, signed manual bank-transfer payment page for LITE/PRO at
  `/daftar/pembayaran/{registration}`, WhatsApp confirmation link with prefilled
  registration/payment details, and `RegistrationResource` payment-status badge
  plus manual paid/unpaid bulk actions.
- ✅ **Phase 6 — Polish & tests (DONE):** juragan dashboard enhanced (kuota X/Y, throttle warning, revenue description); `JuraganQuotaWidget` baru (progress bar kuota + daftar kamar + warning kamar tanpa rate); `SubscriptionIsolationTest` 9 test baru; bug fix `suspended_at` tidak masuk `$fillable` + tidak di-cast sebagai datetime (menyebabkan `SuspendOverdueJuraganJob` dan `SubscriptionObserver` diam-diam gagal update). **76/76 test hijau.**
- ✅ **Phase 7 — Production hardening & feature expansion (DONE):** email notifications (5 mail classes: BillingCreatedMail, BillingThrottledMail, BillingReminderMail, SubscriptionReminderMail, JuraganSuspendedMail) via `noreply@nexaspace.site`; CSV export (billing + subscription) dengan data isolation; tenant self-service profile page (`EditProfile`); onboarding widget (`JuraganOnboardingWidget`); MikroTik health widget (`MikroTikStatusWidget`); multi-router MikroTik per juragan (`mikrotik_host/port/user/pass` di tabel users, `MikroTikService::forJuragan()`); PDF invoice via dompdf (`InvoiceController`, template A4); revenue chart widget (`RevenueChartWidget` 6 bulan terakhir); test coverage 125/125 hijau.

### Isu Kritis — ✅ SUDAH DISELESAIKAN (Sesi 5 Juni 2026)

Ketiga isu di bawah sudah diperbaiki (lihat CHANGELOG "Opsi A"). Dipertahankan
sebagai catatan historis:

- **#1 Async MikroTik:** `BillingObserver` kini dispatch `RestoreDevicesJob` ke queue.
- **#2 Timezone:** `config/app.php` disetel `Asia/Makassar`.
- **#3 Chunking:** `checkAndThrottleOverdue()` pakai `chunk(50)` + jeda 150ms.

<details>
<summary>Detail asli isu kritis (historis)</summary>

### Isu Kritis — Harus Diselesaikan Sebelum Production

#### 1. BillingObserver Memanggil MikroTik Secara Synchronous (Risiko Timeout Webhook)

**Masalah:** `BillingObserver` saat ini memanggil `BillingService::restoreDevicesForBilling()` secara langsung (synchronous) ketika sebuah billing berubah menjadi `paid`. Artinya, setiap kali Midtrans mengirim notifikasi webhook ke `POST /webhook/midtrans`, server harus menunggu seluruh koneksi ke router MikroTik selesai sebelum bisa mengembalikan respons HTTP ke Midtrans.

**Akibat:** Jika koneksi ke router MikroTik lambat atau down, respons webhook akan tertunda melewati batas waktu Midtrans (biasanya 15–30 detik). Midtrans akan menganggap webhook gagal (timeout / 504) dan akan mengirim ulang permintaan tersebut berulang-ulang, berpotensi membebani server hingga habis napas.

**Solusi yang harus diterapkan:** Pindahkan semua pemanggilan `MikroTikService` dari dalam `BillingObserver` ke sebuah Job yang dilempar ke Queue. `BillingObserver` hanya perlu men-dispatch job tersebut dan langsung return — webhook bisa langsung merespons `200 OK` ke Midtrans, sementara pemulihan perangkat berjalan di latar belakang.

#### 2. Zona Waktu Scheduler Belum Disesuaikan (Risiko Blokir di Jam Sibuk)

**Masalah:** Scheduler `ThrottleOverdueTenantsJob` dijadwalkan berjalan setiap hari `01:00`. Secara default, Laravel dan server Hostinger menggunakan zona waktu UTC. Jam `01:00 UTC` setara dengan jam `09:00 WITA`.

**Akibat:** Penyewa bisa tiba-tiba kehilangan koneksi WiFi di jam 09:00 pagi saat sedang kuliah atau bekerja, bukan di tengah malam seperti yang diinginkan.

**Solusi yang harus diterapkan:** Pastikan `timezone` di `config/app.php` disetel ke `Asia/Makassar` (WITA) atau `Asia/Jakarta` (WIB), sesuai target pasar. Verifikasi dengan `php artisan schedule:list` bahwa waktu eksekusi yang ditampilkan sudah sesuai.

#### 3. Tidak Ada Chunking pada Query Throttle (Risiko 503 di Shared Hosting)

**Masalah:** `BillingService::checkAndThrottleOverdue()` saat ini memuat seluruh billing yang overdue sekaligus dengan `.get()`, lalu memanggil MikroTik API untuk setiap perangkat secara berturut-turut dalam satu eksekusi.

**Akibat:** Jika NexaSpace sudah menangani ratusan penyewa, eksekusi semua query database dan tembakan API MikroTik secara bersamaan dalam satu job pada jam 01:00 akan memicu *Resource Limit Reached* (Error 503) di shared hosting.

**Solusi yang harus diterapkan:** Gunakan `.cursor()` atau `.chunk(N)` saat mengambil data billing di dalam job untuk mengurangi penggunaan memori. Pertimbangkan menambahkan `sleep()` kecil (misalnya 100–200ms) antar pemanggilan MikroTik API, atau memecah menjadi beberapa job yang di-chain dengan delay.

</details>

---

### Backlog Testing

The current automated tests are still Laravel skeleton examples. Add focused
coverage before production deployment:

1. Test role-based access for admin and tenant panels.
2. Test tenant billing query isolation.
3. Test grace-period boundaries in `BillingService`.
4. Test MikroTik success and failure behavior with mocked RouterOS calls.
5. Test billing observer recovery without affecting blocked devices.
6. Test payment receipt upload permissions and file validation.
8. Review production queue worker, scheduler, storage link, and HTTPS setup.
9. Decide and document the production database target explicitly.

## Development Notes

- Keep MikroTik network calls behind `MikroTikService`.
- Update billing statuses through Eloquent so `BillingObserver` can react.
- Preserve tenant query scoping in tenant resources.
- Do not expose `.env` credentials in logs, tests, screenshots, or commits.
- `public/build` and published Filament assets are generated artifacts. Rebuild
  Vite assets with `npm run build` after frontend changes.
