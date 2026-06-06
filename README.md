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

NexaSpace adalah platform **SaaS multi-tenant** untuk manajemen kos digital. Pemilik platform (developer) menyewakan sistem ke pemilik kos (juragan); setiap juragan mengelola penghuni (anak kos), perangkat, dan tagihan mereka secara mandiri.

Fitur inti adalah **Auto-Throttle WiFi Billing System**: anak kos yang menunggak akan dibatasi bandwidth MikroTik secara otomatis, dan koneksi dipulihkan setelah juragan mengonfirmasi pembayaran.

## Live

| URL | Tujuan |
|---|---|
| `https://nexaspace.site` | Landing page publik |
| `https://nexaspace.site/admin` | Panel admin (developer & juragan) |
| `https://nexaspace.site/tenant` | Portal tenant (anak kos) |

## Three-Tier Role Model

| Role | Siapa | Panel | Scope |
|---|---|---|---|
| `developer` | Pemilik platform NexaSpace (super admin) | `/admin` | Lihat semua data semua juragan |
| `juragan` | Pemilik kos, pelanggan SaaS berbayar | `/admin` | Hanya data kos sendiri |
| `tenant` | Anak kos, milik satu juragan | `/tenant` | Hanya tagihan sendiri |

Email login di-namespace ke slug kos — misal kos "Mutiara": juragan login sebagai `owner@mutiara.com`, anak kos sebagai `room1@mutiara.com`. Ini identifier login saja, bukan mailbox nyata.

## Tech Stack

| Area | Teknologi |
|---|---|
| Backend | Laravel 13.12 |
| Runtime | PHP 8.4 |
| Admin & Tenant UI | Filament 5.6 |
| Frontend build | Vite 8, Tailwind CSS 4 |
| Database | MySQL 8 |
| Queue | Laravel database queue |
| Scheduler | Laravel scheduler |
| Router integration | `evilfreelancer/routeros-api-php` 1.7 |
| PDF generation | `barryvdh/laravel-dompdf` |
| Payment | Manual bank transfer + konfirmasi WhatsApp (tanpa payment gateway) |

## Fitur

### Landing Page Publik

- Halaman marketing di `/` dengan hero section, pricing cards, tabel perbandingan paket (LITE/PRO/CUSTOM), testimoni, dan contact links
- Badge "⭐ Paling Laris" pada paket PRO di pricing cards dan tabel perbandingan
- Halaman registrasi dedicated di `/daftar/{plan}` — dua kolom: sticky plan summary (kiri) + form pendaftaran (kanan)
- Halaman pembayaran manual di `/daftar/pembayaran/{registration}` — instruksi transfer bank + link konfirmasi WhatsApp otomatis (protected by signed URL)
- Halaman sukses di `/daftar/sukses`

### Autentikasi & Panel

- Admin panel di `/admin` (developer & juragan, login terpisah)
- Tenant panel di `/tenant` (anak kos)
- Custom login pages untuk kedua panel
- `User::canAccessPanel()` berbasis role — juragan diblokir saat `suspended_at IS NOT NULL`; anak kos diblokir saat `juragan.suspended_at IS NOT NULL`

### Admin Panel — Developer & Juragan

**User Management**
- CRUD lengkap untuk user (developer, juragan, anak kos)
- Filter berdasarkan role
- Kolom jumlah perangkat per anak kos
- Form anak kos: field `move_in_date` (tanggal masuk), `monthly_rate` (tarif bulanan), konfigurasi router MikroTik per juragan
- Upload QRIS per juragan; konfigurasi rekening bank (Repeater)
- Data isolation: juragan hanya melihat anak kos miliknya sendiri

**Billing Management**
- CRUD tagihan termasuk hapus per baris dan bulk delete
- Form create: auto-fill `move_in_date`, `amount`, `billing_month`, `due_date` saat anak kos dipilih; backfill otomatis bulan-bulan yang terlewat saat `move_in_date` di masa lalu
- Kolom ikon "Bukti Bayar" — klik langsung membuka file receipt di tab baru
- Filter: status, bukti bayar (ada/belum)
- Bulk actions: tandai lunas, tandai belum lunas, hapus, **Invoice Gabungan PDF** (gabungkan 2–36 bulan satu anak kos dalam satu PDF)
- Export CSV tagihan

**Subscription Management (Juragan → NexaSpace)**
- List subscription per juragan; filter: status, paket (LITE/PRO/CUSTOM), bulan
- Developer: bulk tandai lunas / belum lunas
- Juragan: read-only, hanya melihat subscription milik sendiri
- Export CSV subscription

**Registration Management (Developer-only)**
- Full CRUD pendaftaran juragan baru dari public form
- Badge navigasi menampilkan jumlah registrasi `pending`
- Kolom `payment_status` (Lunas/Belum Bayar)
- Bulk actions: Setujui & Buat Akun (provisioning), tandai pembayaran lunas/belum bayar, tandai ditolak, hapus
- Row actions: provisioning langsung, edit, hapus

**Device Management**
- CRUD perangkat anak kos (MAC address, nama, status)
- Filter status: active/throttled/blocked
- Bulk actions: tandai active, throttled, blocked
- Bulk sync MikroTik manual

**Dashboard Widgets**
- `StatsOverview` — statistik platform (developer: total juragan, anak kos, tagihan, pendapatan; juragan: kamar terisi X/Y, throttle count, unpaid count, pendapatan bulan ini, status subscription NexaSpace)
- `JuraganQuotaWidget` — progress bar kuota + daftar kamar + warning kamar tanpa tarif (juragan only)
- `BillingReminderWidget` — daftar anak kos yang perlu ditagih hari ini (berdasarkan `move_in_date`); tombol "Buat Tagihan" per baris + "Buat Semua"; preview 3 hari ke depan (juragan only)
- `RevenueChartWidget` — line chart pendapatan 6 bulan terakhir, scoped per juragan
- `JuraganOnboardingWidget` — checklist setup (ada anak kos, semua punya tarif, MikroTik dikonfigurasi); hilang otomatis setelah selesai (juragan only)
- `MikroTikStatusWidget` — badge status koneksi router, polling 5 menit, cache 5 menit (developer only)

**Halaman Tambahan**
- `RouterManagementPage` — tabel DHCP lease real-time dari RouterOS; developer pilih juragan, juragan lihat router sendiri
- `ActivityLogResource` — audit trail read-only; log perubahan status billing/subscription, suspend/unsuspend juragan, provisioning (developer only)
- `JuraganProfilePage` — halaman profil juragan: nama, HP, contact email, rekening bank (Repeater), upload QRIS, ganti password

### Tenant Portal — Anak Kos

- Daftar tagihan pribadi dengan badge status
- Widget total tagihan belum lunas
- Upload bukti transfer per tagihan (unpaid / throttled)
- Download PDF invoice per tagihan
- Aksi "Bayar" — modal pilih rekening bank juragan + upload bukti → status langsung `paid`
- Widget QRIS — tampil jika juragan sudah upload gambar QR
- `EditProfile` — ubah nama, nomor HP, ganti password

### Auto-Throttle Billing

- `ThrottleOverdueTenantsJob` berjalan setiap hari jam 01:00 WITA
- Grace period: throttle hanya jika `today > due_date + 2 hari`
- Tagihan unpaid overdue → `throttled`; perangkat aktif → `throttled`; MikroTik set rate limit `256k/256k`
- Perangkat `blocked` tidak tersentuh

### Payment Recovery

- `BillingObserver` dispatch `RestoreDevicesJob` (async via queue) saat billing → `paid`
- Perangkat `throttled` → `active`; MikroTik rate limit dihapus
- Perangkat `blocked` tidak tersentuh

### Auto-Billing Berdasarkan Tanggal Masuk

- Kolom `move_in_date` pada tabel `users` (anak kos)
- `GenerateBillsByMoveInJob` berjalan setiap hari jam 00:02 WITA — generate tagihan untuk anak kos yang `DAY(move_in_date)` cocok dengan hari ini
- Tagihan pertama kali dibuat: backfill otomatis semua bulan yang terlewat sejak bulan setelah masuk
- Anak kos tanpa `move_in_date` tetap ditagih tanggal 1 (backward compatible via `GenerateMonthlyBillsJob`)

### Subscription Billing (Juragan → NexaSpace)

- `GenerateMonthlySubscriptionsJob` berjalan tanggal 1 jam 00:05 WITA — buat tagihan langganan untuk juragan LITE (Rp 199.000) dan PRO (Rp 499.000)
- Jatuh tempo: tanggal 10; grace period: 2 hari
- `SuspendOverdueJuraganJob` berjalan jam 01:30 WITA — set `suspended_at`, blokir akses panel juragan + semua anak kosnya
- `SubscriptionObserver` — saat developer tandai `paid`, `suspended_at` otomatis dihapus → akses dipulihkan
- CUSTOM plan dikelola manual oleh developer

### Public Registration & Provisioning

- Form publik di `/daftar/{plan}` → simpan ke tabel `registrations` → signed payment URL
- Halaman pembayaran manual: pilih bank, transfer, konfirmasi via WhatsApp
- Developer verifikasi + tandai `payment_status = paid` di `RegistrationResource`
- Bulk action "Setujui & Buat Akun" → `ProvisionTenantJob`: buat akun juragan + N anak kos (sesuai kuota paket) dalam DB transaction → kirim kredensial ke email via `TenantProvisionedMail`
- Slug collision auto-resolved (`mutiara` → `mutiara-2`)

### Email Notifications (`noreply@nexaspace.site`)

| Mail Class | Trigger | Penerima |
|---|---|---|
| `BillingCreatedMail` | Tagihan bulanan selesai digenerate | Juragan |
| `BillingReminderMail` | H-3 sebelum jatuh tempo tagihan | Juragan |
| `BillingThrottledMail` | Anak kos di-throttle | Juragan |
| `SubscriptionReminderMail` | H-3 sebelum jatuh tempo subscription | Juragan |
| `JuraganSuspendedMail` | Akun juragan disuspend | Juragan |
| `TenantProvisionedMail` | Provisioning selesai | Juragan (kredensial lengkap) |

Email dikirim ke `contact_email` (email nyata juragan), bukan ke login email.

### Audit Trail (Activity Log)

Setiap event penting dicatat otomatis ke tabel `activity_logs`:
- Perubahan status tagihan (`billing.status_changed`)
- Perubahan status subscription (`subscription.status_changed`)
- Suspend juragan (`juragan.suspended`)
- Unsuspend juragan (`juragan.unsuspended`)
- Provisioning akun (`juragan.provisioned`)

Developer dapat melihat seluruh log di `/admin/activity-logs` dengan filter per event.

### Multi-Router MikroTik

- Setiap juragan dapat dikonfigurasi dengan router MikroTik sendiri (`mikrotik_host`, `mikrotik_port`, `mikrotik_user`, `mikrotik_pass` di tabel `users`)
- Fallback ke global `.env` jika tidak dikonfigurasi
- `MikroTikService::forJuragan(User $juragan)` — factory method untuk per-juragan router instance
- Throttle dan restore menggunakan router yang tepat per juragan secara otomatis

### QRIS Statis per Juragan

- Juragan upload gambar QRIS statis di halaman edit user (admin) atau halaman profil juragan
- Anak kos melihat widget QRIS di dashboard tenant portal
- Invoice PDF menyertakan gambar QRIS untuk tagihan yang belum dibayar

### PDF Invoice

- Invoice single: `/invoice/billing/{billing}` — A4, brand header NexaSpace, detail pihak, tabel tagihan, status badge, total, QRIS block jika ada
- Invoice gabungan: `/invoice/billing-merged?ids=1,2,3` — multi-row table per bulan, summary boxes (lunas/belum/total), QRIS block
- Access control: developer (semua), juragan (anak kos miliknya), tenant (milik sendiri)

## Instalasi

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

Di Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

## Konfigurasi Service

Tambahkan ke `.env`:

```dotenv
# MikroTik (global fallback; juragan bisa override via admin panel)
MIKROTIK_HOST=
MIKROTIK_USER=admin
MIKROTIK_PASS=
MIKROTIK_PORT=8728

# Mail — gunakan 'log' di local dev (email masuk ke storage/logs/laravel.log)
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@nexaspace.site
```

Jangan commit kredensial nyata.

## Menjalankan Secara Lokal

```bash
composer run dev
```

Perintah ini menjalankan Laravel server, queue listener, log tail, dan Vite dev server secara bersamaan.

Untuk production:

```bash
php artisan queue:work
php artisan schedule:work
```

## Akun Development (Seeded)

```text
Developer:  admin@nexaspace.site       password: password

Juragan:    owner@mutiara.com          password: password
            (Kos Mutiara, plan PRO, quota 40)
Anak kos:   room1@mutiara.com … room5@mutiara.com

Juragan:    owner@melati.com           password: password
            (Kos Melati, plan LITE, quota 20)
Anak kos:   room1@melati.com … room4@melati.com
```

## Scheduler Jobs

| Job | Jadwal | Fungsi |
|---|---|---|
| `GenerateBillsByMoveInJob` | Daily 00:02 WITA | Buat tagihan anak kos berdasarkan `move_in_date` |
| `GenerateMonthlyBillsJob` | Tgl 1 jam 00:01 WITA | Buat tagihan bulanan anak kos tanpa `move_in_date` |
| `GenerateMonthlySubscriptionsJob` | Tgl 1 jam 00:05 WITA | Buat tagihan langganan juragan LITE & PRO |
| `ThrottleOverdueTenantsJob` | Daily 01:00 WITA | Throttle anak kos overdue (H+3) |
| `SuspendOverdueJuraganJob` | Daily 01:30 WITA | Suspend juragan overdue (H+3) |
| `SendBillingReminderJob` | Daily 08:00 WITA | Email reminder tagihan H-3 ke juragan |
| `SendSubscriptionReminderJob` | Daily 08:05 WITA | Email reminder subscription H-3 ke juragan |

## Alur Billing Anak Kos

1. `GenerateBillsByMoveInJob` / `GenerateMonthlyBillsJob` membuat tagihan baru setiap bulan
2. Tagihan berstatus `unpaid`; juragan atau developer bisa tandai `paid` manual di panel admin
3. Jika melewati H+2 grace period, `ThrottleOverdueTenantsJob` ubah status → `throttled`
4. Router MikroTik mengaplikasikan rate limit `256k/256k` pada perangkat anak kos
5. Juragan/developer tandai `paid` → `BillingObserver` dispatch `RestoreDevicesJob` (async)
6. Perangkat dipulihkan ke `active`, rate limit dihapus dari MikroTik

## Alur Subscription Juragan

1. Tanggal 1: `GenerateMonthlySubscriptionsJob` buat tagihan langganan
2. Juragan transfer manual ke rekening NexaSpace, konfirmasi via WhatsApp
3. Developer verifikasi dan tandai `paid` di `/admin/subscriptions`
4. `SubscriptionObserver` deteksi status → `paid`, hapus `suspended_at` → akses dipulihkan
5. Jika tidak bayar sampai H+2: `SuspendOverdueJuraganJob` set `suspended_at` → akses diblokir

## Alur Pendaftaran Juragan Baru

1. Calon juragan isi form di `/daftar/{plan}`
2. Submit → data tersimpan di `registrations` (status: `pending`, `payment_status`: `unpaid`)
3. Redirect ke halaman pembayaran manual `/daftar/pembayaran/{registration}` (signed URL)
4. Calon juragan transfer dan kirim konfirmasi via WhatsApp
5. Developer cek WhatsApp, verifikasi, tandai `payment_status = paid` di `RegistrationResource`
6. Developer klik "Setujui & Buat Akun" → `ProvisionTenantJob` jalan sync
7. Akun juragan (`owner@<slug>.com`) + N akun anak kos (`room1..N@<slug>.com`) dibuat
8. Kredensial dikirim ke email juragan via `TenantProvisionedMail`
9. Registrasi berubah status → `active`

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
app/Services/BillingService.php                  Throttle, restore, bill generation logic
app/Services/MikroTikService.php                 RouterOS DHCP lease sync (per-juragan router support)
app/Observers/BillingObserver.php                Dispatch RestoreDevicesJob on billing → paid; activity log
app/Observers/SubscriptionObserver.php           Clear suspended_at on subscription → paid; activity log
app/Models/ActivityLog.php                       Audit trail model with static record() helper
app/Jobs/ThrottleOverdueTenantsJob.php           Scheduled throttle job (01:00 WITA)
app/Jobs/RestoreDevicesJob.php                   Async device recovery after payment
app/Jobs/GenerateMonthlyBillsJob.php             Monthly bill generation (tenants without move_in_date)
app/Jobs/GenerateBillsByMoveInJob.php            Daily bill generation by move_in_date (00:02 WITA)
app/Jobs/GenerateMonthlySubscriptionsJob.php     Monthly subscription generation for juragan
app/Jobs/SuspendOverdueJuraganJob.php            Suspend juragan after grace period (01:30 WITA)
app/Jobs/ProvisionTenantJob.php                  Create juragan + anak kos accounts on approval
app/Jobs/SendBillingReminderJob.php              H-3 billing reminder email (08:00 WITA)
app/Jobs/SendSubscriptionReminderJob.php         H-3 subscription reminder email (08:05 WITA)
app/Http/Controllers/RegistrationController.php  /daftar/{plan} registration + payment flow
app/Http/Controllers/ExportController.php        CSV export for billing and subscriptions
app/Http/Controllers/InvoiceController.php       PDF invoice: single + merged multi-billing
app/Filament/Resources/                          Admin panel resources (RBAC-scoped)
app/Filament/Tenant/                             Tenant portal resources and pages
app/Filament/Widgets/                            Dashboard widgets (stats, chart, MikroTik, quota, reminder, onboarding)
app/Filament/Pages/                              RouterManagementPage, JuraganProfilePage
app/Mail/                                        Email notification classes (6 mail classes)
resources/views/landing.blade.php                Public marketing site
resources/views/daftar*.blade.php                Registration, payment, and success views
resources/views/emails/                          Email templates
resources/views/invoices/                        PDF invoice templates (single + merged)
resources/views/filament/                        Custom widget and page Blade views
routes/console.php                               Scheduler registration (7 scheduled jobs)
routes/web.php                                   Web routes (landing, registration, export, invoice)
database/seeders/DatabaseSeeder.php              3-tier seed: developer + 2 juragan + anak kos + devices + billings
deploy/                                          Production deployment artifacts (checklist, Supervisor, crontab)
```

## Testing

```text
125 tests, 125 passed
```

Test coverage meliputi: juragan data isolation, subscription isolation, billing service grace-period boundary, email notifications (6 mail classes), CSV export isolation, PDF invoice access control, tenant self-service profile, panel access (suspended juragan/tenant), billing observer recovery, receipt upload authorization, registration flow, provisioning, tenant billing isolation.
