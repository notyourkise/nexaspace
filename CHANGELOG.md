# Changelog — NexaSpace

Semua perubahan dicatat secara kronologis.
Zona waktu: **WITA (UTC+8) — Balikpapan, Kalimantan Timur**

---

## [Sesi Kerja] — 7 Juni 2026

---

### 23:17 WITA — Opsi I–L: Test Coverage, Multi-Router MikroTik, PDF Invoice, Dashboard Chart

**Apa yang Diubah:**

#### I — Test Coverage untuk Opsi D–G

| File | Perubahan |
|---|---|
| `tests/Feature/ExportTest.php` | 11 test baru: guest 403, tenant 403, juragan export CSV tagihan dengan header benar, data isolation (Juragan A tidak bisa lihat data Juragan B), developer lihat semua, filter `?month`, status diterjemahkan (Lunas/Belum Lunas/Dibatasi), export langganan isolation, developer export semua, header kolom subscription CSV. |
| `tests/Feature/TenantProfileTest.php` | 9 test baru: halaman edit-profile render untuk tenant, guest diredirect, juragan dapat 403, saveProfile update nama & HP, nama required, HP nullable, savePassword berhasil dengan kredensial benar, gagal dengan current_password salah, gagal konfirmasi tidak cocok, gagal password terlalu pendek, gagal password sama dengan lama. |
| `routes/web.php` | Hapus wrapper `middleware('auth')` dari export routes — controller sudah guard sendiri via `abort(403)`, jadi guest tidak lagi kena 500 akibat `route('login')` tidak terdefinisi. |

**Alasan Perubahan:**
Menutup gap test coverage untuk fitur export CSV dan tenant self-service profile yang ditambahkan di sesi sebelumnya.

**Hasil Akhir:**
125 test, 125 passed (sebelumnya 102).

---

#### J — Multi-Router MikroTik per Juragan

| File | Perubahan |
|---|---|
| `database/migrations/2026_06_06_145622_add_mikrotik_config_to_users_table.php` | Tambah 4 kolom nullable ke tabel `users`: `mikrotik_host` (string), `mikrotik_port` (integer), `mikrotik_user` (string), `mikrotik_pass` (string). |
| `app/Models/User.php` | Tambah 4 kolom baru ke `#[Fillable]`; tambah cast `mikrotik_port` → `integer`. |
| `app/Services/MikroTikService.php` | Refactor besar: constructor sekarang menerima parameter opsional `host/port/user/pass`, fall back ke `.env` jika null. Tambah method statis `forJuragan(User $juragan)` yang membuat instance dengan config router milik juragan. Tambah `isConfigured()`, `connectionInfo()`. Cache key `isConnected()` sekarang per-host agar multi-router tidak saling bentrok. |
| `app/Services/BillingService.php` | `checkAndThrottleOverdue()` dan `restoreDevicesForBilling()` kini panggil `MikroTikService::forJuragan($juragan)` sebelum throttle/unthrottle — masing-masing juragan pakai router-nya sendiri. |
| `app/Filament/Widgets/MikroTikStatusWidget.php` | Update `getViewData()` pakai `isConfigured()` dan `connectionInfo()` API baru. |
| `app/Filament/Resources/UserResource.php` | Tambah Section "Konfigurasi Router MikroTik" (collapsible) di form edit user — muncul hanya saat developer mengedit user dengan role `juragan`. Empat field: host, port, user, pass. Kosong = pakai global `.env`. |

**Alasan Perubahan:**
Setiap juragan bisa memiliki router MikroTik sendiri (multi-kos multi-router). Sebelumnya hanya ada satu router global via `.env`, yang tidak memadai saat platform memiliki banyak juragan dengan jaringan berbeda.

**Hasil Akhir:**
Juragan bisa dikonfigurasi dengan router sendiri via admin panel. Jika tidak diisi, sistem fallback ke global `.env`. Throttle dan restore otomatis menggunakan router yang tepat per-juragan. 125 test tetap hijau.

---

#### K — PDF Invoice untuk Tagihan Anak Kos

| File | Perubahan |
|---|---|
| `composer.json` / `vendor/` | Install `barryvdh/laravel-dompdf` |
| `app/Http/Controllers/InvoiceController.php` | Controller baru: `download(Request, Billing)` — generate PDF invoice menggunakan DomPDF. Access control: developer bisa download semua; juragan hanya milik anak kosnya; tenant hanya milik sendiri. |
| `resources/views/invoices/billing.blade.php` | Template PDF: header brand NexaSpace, detail pihak (tenant + pengelola kos), tabel tagihan dengan status badge berwarna, total, footer dengan catatan pembayaran. Desain profesional A4 dengan warna brand hijau `#306D29`. |
| `routes/web.php` | Tambah route `GET /invoice/billing/{billing}` → `InvoiceController@download` bernama `invoice.billing`. |
| `app/Filament/Tenant/Resources/BillingResource.php` | Tambah row action "Invoice PDF" (icon download) di tabel tenant — untuk semua tagihan. |
| `app/Filament/Resources/BillingResource.php` | Tambah row action "Invoice PDF" di tabel admin — untuk semua tagihan. |

**Alasan Perubahan:**
Anak kos dan juragan membutuhkan bukti tagihan tertulis (PDF) untuk keperluan pencatatan dan arsip.

**Hasil Akhir:**
Tenant dan juragan bisa download invoice PDF per tagihan langsung dari panel. Developer juga bisa. Nomor invoice diformat `INV-XXXXXX` (6 digit). Nama file: `invoice-{id}-{YYYY-MM}.pdf`.

---

#### L — Dashboard Analytics Chart (Revenue 6 Bulan)

| File | Perubahan |
|---|---|
| `app/Filament/Widgets/RevenueChartWidget.php` | Widget chart baru menggunakan Filament 5 built-in `ChartWidget` (tipe `line`). Menampilkan pendapatan (tagihan `paid`) selama 6 bulan terakhir. Developer melihat semua juragan; juragan melihat hanya kos sendiri. Warna brand hijau `#306D29`. Cache-safe: data diambil fresh setiap load. |
| `app/Providers/Filament/AdminPanelProvider.php` | Register `RevenueChartWidget::class` di daftar widgets admin panel (sort 20, tampil setelah JuraganQuotaWidget). |

**Alasan Perubahan:**
Memberikan gambaran visual tren pendapatan bulanan untuk developer dan juragan — sehingga bisa mengidentifikasi bulan dengan penerimaan rendah dan mengambil tindakan.

**Hasil Akhir:**
Grafik line chart muncul di dashboard admin, menampilkan 6 bulan terakhir dengan label bulan berbahasa Indonesia. Tooltip menampilkan nominal dalam format Rupiah (`Rp x.xxx.xxx`). Nilai Y-axis juga diformat Rupiah. Juragan hanya melihat pendapatan kos mereka sendiri.

---

### 02:00 WITA — Opsi D–G: Export CSV, Onboarding Widget, MikroTik Health, Tenant Self-Service

**Apa yang Diubah:**

#### D — Laporan & Export CSV

Export data tagihan dan langganan ke file CSV, dapat dibuka langsung di Excel/Google Sheets.

| File | Perubahan |
|---|---|
| `app/Http/Controllers/ExportController.php` | Controller baru: `billing()` — export tagihan bulan ini (filter opsional `?month=YYYY-MM`); `subscription()` — export semua langganan. Keduanya menerapkan data isolation (juragan hanya export data sendiri). BOM UTF-8 disertakan agar Excel membaca karakter Indonesia dengan benar. |
| `routes/web.php` | Tambah route group `middleware('auth')`: `GET /export/billing` dan `GET /export/subscription` |
| `app/Filament/Resources/BillingResource/Pages/ListBillings.php` | Tambah header action "Export CSV" (icon download, warna gray) yang membuka `/export/billing?month=YYYY-MM` di tab baru |
| `app/Filament/Resources/SubscriptionResource/Pages/ListSubscriptions.php` | Tambah header action "Export CSV" yang membuka `/export/subscription` di tab baru |

**Kolom CSV tagihan:** Nama Anak Kos, Nomor Kamar, Bulan Tagihan, Jatuh Tempo, Nominal (Rp), Status

**Kolom CSV langganan:** Juragan, Nama Kos, Paket, Bulan, Jatuh Tempo, Nominal (Rp), Status

#### E — Onboarding Widget untuk Juragan Baru

Widget baru di dashboard admin yang hanya tampil untuk juragan yang setup-nya belum lengkap. Auto-hidden ketika semua anak kos sudah punya tarif bulanan.

| File | Keterangan |
|---|---|
| `app/Filament/Widgets/JuraganOnboardingWidget.php` | Widget baru: `canView()` → false jika developer atau jika semua anak kos sudah punya monthly_rate. Menampilkan 3 checklist: ada anak kos, semua punya tarif, MikroTik dikonfigurasi. |
| `resources/views/filament/widgets/juragan-onboarding-widget.blade.php` | View: checklist item berwarna (hijau = selesai, kuning = perlu aksi). Daftar kamar tanpa tarif ditampilkan sebagai badge chip. |
| `app/Providers/Filament/AdminPanelProvider.php` | Daftarkan `JuraganOnboardingWidget::class` dengan sort 0 (tampil paling atas) |

#### F — MikroTik Health Check & Status Widget

Tambah kemampuan cek koneksi ke router dan tampilkan statusnya di dashboard developer.

| File | Perubahan |
|---|---|
| `app/Services/MikroTikService.php` | Tambah `isConnected(): bool` — coba koneksi ke router, hasil di-cache 5 menit (`Cache::remember('mikrotik.is_connected', 300, ...)`) agar tidak hit router di setiap page load. |
| `app/Filament/Widgets/MikroTikStatusWidget.php` | Widget baru: `canView()` → developer only. Menampilkan badge Terhubung/Tidak Terhubung/Belum Dikonfigurasi + detail host, port, user. Auto-refresh setiap 5 menit (`pollingInterval = '300s'`). |
| `resources/views/filament/widgets/mikrotik-status-widget.blade.php` | View: badge status animasi pulse (hijau) jika connected, merah jika gagal, abu jika belum dikonfigurasi. |
| `app/Providers/Filament/AdminPanelProvider.php` | Daftarkan `MikroTikStatusWidget::class` |

#### G — Tenant Self-Service (Profil & Ganti Password)

Anak kos kini bisa mengubah nama, nomor HP, dan password langsung dari portal tenant tanpa harus menghubungi juragan.

| File | Keterangan |
|---|---|
| `app/Filament/Tenant/Pages/EditProfile.php` | Page baru di tenant panel: dua form terpisah — Form Profil (nama + nomor HP) dan Form Password (current password + new password + konfirmasi). Validasi `currentPassword`, `Password::min(8)`, dan `same()`. |
| `resources/views/filament/tenant/pages/edit-profile.blade.php` | View: section informasi kamar (read-only: login email, nomor kamar, nama kos) + form profil + form password, masing-masing dengan tombol simpan terpisah. |
| `app/Providers/Filament/TenantPanelProvider.php` | Daftarkan `EditProfile::class` ke `->pages([...])` |

**Navigasi tenant panel sekarang:**

| Menu | Keterangan |
|---|---|
| Dashboard | Statistik tagihan |
| My Bills | Daftar tagihan + upload receipt |
| Profil Saya | Edit profil + ganti password ← baru |

**Alasan Perubahan:**

- **Opsi D**: Juragan dan developer perlu data dalam format spreadsheet untuk rekap keuangan bulanan.
- **Opsi E**: Juragan baru sering bingung langkah pertama setelah akun dibuat. Widget ini memberikan panduan visual yang hilang sendiri setelah setup selesai.
- **Opsi F**: Developer perlu tahu apakah router MikroTik dapat dihubungi sebelum menyimpulkan bahwa throttle/restore berfungsi. Cache 5 menit menghindari hit router di setiap request.
- **Opsi G**: Anak kos harus bisa mengganti password mereka sendiri (terutama setelah provisioning dengan password default `password`).

**Hasil Akhir:**

- Export CSV tersedia di halaman Tagihan dan Langganan.
- Dashboard juragan menampilkan onboarding checklist jika ada setup yang belum selesai, lalu hilang otomatis.
- Developer melihat status MikroTik real-time (cache 5 menit) di dashboard.
- Anak kos bisa ubah nama, nomor HP, dan password dari portal `/tenant`.
- **102/102 test tetap hijau** (tidak ada regresi).

---

### 00:30 WITA — Opsi B + Opsi C: Test Coverage Lengkap + Notifikasi Email Otomatis

**Apa yang Diubah:**

#### C1 — Kolom `contact_email` untuk Juragan

Karena `users.email` adalah login-identifier saja (bukan email asli, contoh: `owner@mutiara.com`), dibutuhkan kolom terpisah untuk email nyata juragan agar notifikasi bisa dikirim.

| File | Perubahan |
|---|---|
| `database/migrations/2026_06_06_135829_add_contact_email_to_users_table.php` | Tambah kolom `contact_email` nullable string setelah kolom `email` |
| `app/Models/User.php` | Tambah `'contact_email'` ke `#[Fillable]` |
| `app/Jobs/ProvisionTenantJob.php` | Set `contact_email = $reg->email` saat membuat akun juragan dari registration |
| `database/factories/UserFactory.php` | State `juragan()` kini mengisi `contact_email` dengan `fake()->safeEmail()` |

#### C2 — 5 Mail Classes + 5 Email View Templates

Seluruh email dikirim dari `noreply@nexaspace.site` ke `contact_email` juragan.

| Mail Class | Trigger | Penerima |
|---|---|---|
| `app/Mail/BillingCreatedMail.php` | Setelah `generateMonthlyBills()` selesai | Juragan — summary tagihan bulan baru |
| `app/Mail/BillingReminderMail.php` | H-3 sebelum jatuh tempo tagihan | Juragan — daftar anak kos yang belum bayar |
| `app/Mail/BillingThrottledMail.php` | Setelah `checkAndThrottleOverdue()` throttle | Juragan — daftar anak kos yang di-throttle |
| `app/Mail/SubscriptionReminderMail.php` | H-3 sebelum jatuh tempo langganan | Juragan — reminder bayar langganan NexaSpace |
| `app/Mail/JuraganSuspendedMail.php` | Saat `SuspendOverdueJuraganJob` suspend | Juragan — notifikasi akun ditangguhkan |

Email views yang dibuat (semua di `resources/views/emails/`):
- `billing-created.blade.php` — header hijau, summary tagihan (jumlah + total IDR)
- `billing-reminder.blade.php` — header kuning, warning box, tabel anak kos belum bayar
- `billing-throttled.blade.php` — header merah, tabel anak kos yang di-throttle
- `subscription-reminder.blade.php` — header hijau, detail langganan + warning auto-suspend
- `juragan-suspended.blade.php` — header merah, detail tagihan tertunggak + instruksi pemulihan

#### C3 — Integrasi Notifikasi ke BillingService dan SuspendOverdueJuraganJob

| File | Perubahan |
|---|---|
| `app/Services/BillingService.php` | `generateMonthlyBills()`: kirim `BillingCreatedMail` per juragan setelah chunk selesai (grouped by juragan_id); `checkAndThrottleOverdue()`: kirim `BillingThrottledMail` per juragan setelah throttle |
| `app/Jobs/SuspendOverdueJuraganJob.php` | Kirim `JuraganSuspendedMail` saat `suspended_at` pertama kali di-set (tidak duplikat jika sudah di-suspend) |

#### C4 — Dua Scheduled Reminder Jobs

| File | Schedule | Keterangan |
|---|---|---|
| `app/Jobs/SendBillingReminderJob.php` | Setiap hari 08:00 WITA | Cari tagihan unpaid dengan due_date = hari ini + 3; kirim `BillingReminderMail` per juragan |
| `app/Jobs/SendSubscriptionReminderJob.php` | Setiap hari 08:05 WITA | Cari subscription unpaid dengan due_date = hari ini + 3; kirim `SubscriptionReminderMail` per juragan |
| `routes/console.php` | — | Daftarkan kedua job dengan `->withoutOverlapping()` |

#### B1 — BillingServiceTest (Grace Period Boundaries)

File baru: `tests/Feature/BillingServiceTest.php` — **8 test baru**:

| Test | Skenario |
|---|---|
| `test_billing_due_today_is_not_throttled` | Jatuh tempo hari ini → grace, tidak di-throttle |
| `test_billing_due_two_days_ago_is_not_throttled` | H+2 → masih grace, tidak di-throttle |
| `test_billing_due_three_days_ago_is_throttled` | H+3 → grace habis, di-throttle |
| `test_already_throttled_billing_is_skipped` | Status sudah `throttled` → tidak diproses ulang |
| `test_blocked_device_is_not_touched_during_throttle` | Device `blocked` tidak tersentuh |
| `test_throttle_only_affects_overdue_tenant` | Hanya tenant overdue yang terdampak |
| `test_generate_monthly_bills_creates_billing_for_tenants` | Bill dibuat untuk tenant dengan rate |
| `test_generate_monthly_bills_is_idempotent` | Tidak dobel jika dijalankan dua kali |
| `test_generate_monthly_bills_skips_tenant_without_rate` | Tenant tanpa rate dilewati |

#### B2 — PanelAccessTest (Suspended Juragan)

File yang diperbarui: `tests/Feature/PanelAccessTest.php` — **5 test baru**:
- `test_suspended_juragan_cannot_access_admin_panel` — suspended_at di-set → 403
- `test_active_juragan_can_access_admin_panel` — suspended_at null → 200
- `test_developer_is_never_blocked_by_suspension` — developer selalu bisa akses
- `test_tenant_cannot_access_panel_when_juragan_is_suspended` — anak kos ikut terblokir
- `test_tenant_can_access_panel_when_juragan_is_active` — anak kos bisa akses jika juragan aktif

#### B3 — ReceiptUploadTest

File baru: `tests/Feature/ReceiptUploadTest.php` — **8 test baru**:
- Query scoping untuk tenants yang berbeda
- Visibility action `upload_receipt` berdasarkan status billing
- Update `payment_receipt` ke database
- Upload tidak mengubah status billing (tetap `unpaid`)
- Route `/create` mengembalikan 404 (canCreate = false)

#### EmailNotificationTest (Opsi C)

File baru: `tests/Feature/EmailNotificationTest.php` — **13 test baru**:
- `BillingCreatedMail` dikirim / tidak dikirim sesuai kondisi
- `BillingThrottledMail` dikirim / tidak dikirim sesuai kondisi
- `BillingReminderMail` dikirim hanya untuk H-3, bukan H-5 atau status paid
- `SubscriptionReminderMail` dikirim hanya untuk subscription unpaid H-3
- `JuraganSuspendedMail` dikirim saat suspend pertama; tidak dikirim jika sudah suspended; tidak dikirim tanpa contact_email

**Alasan Perubahan:**

Opsi B mengisi celah test yang tersisa di backlog: grace period boundaries, panel access untuk suspended juragan, dan receipt upload validation. Opsi C menambahkan notifikasi email otomatis ke juragan untuk semua event billing penting — dibuat dari `noreply@nexaspace.site` dan dikirim ke `contact_email` juragan (email nyata yang terpisah dari login identifier).

**Hasil Akhir:**

- **102/102 test hijau** (naik dari 66 sebelumnya, +36 test baru).
- Juragan mendapat email notifikasi otomatis untuk: tagihan bulan baru dibuat, reminder H-3 jatuh tempo tagihan, koneksi anak kos di-throttle, reminder H-3 langganan NexaSpace, dan saat akun di-suspend.
- Scheduler kini punya 6 scheduled jobs total (tambah 2 reminder).
- Grace period logic ter-cover dengan test boundary eksplisit (H0, H+2, H+3).
- Panel access juragan/anak-kos ketika suspended ter-cover lengkap.

---

## [Sesi Kerja] — 6 Juni 2026 (lanjutan malam)

---

### 22:00 WITA — Opsi A: Production Hardening + Hapus Midtrans Sepenuhnya

**Apa yang Diubah:**

#### A1 — Penghapusan Total Integrasi Midtrans

Payment gateway Midtrans dihapus sepenuhnya dari seluruh codebase. Sistem pembayaran kini sepenuhnya manual (transfer bank + konfirmasi WhatsApp).

**File yang Dihapus:**

| File | Keterangan |
|---|---|
| `app/Services/MidtransService.php` | Service Snap token generation untuk BILL- dan SUB- order |
| `app/Http/Controllers/MidtransWebhookController.php` | Controller endpoint `POST /webhook/midtrans` |
| `resources/views/filament/subscription/pay-now.blade.php` | Blade view Snap widget untuk juragan |
| `resources/views/tenant/billing/pay-now.blade.php` | Blade view Snap widget untuk anak kos |
| `tests/Feature/MidtransWebhookTest.php` | 10 test untuk webhook signature + order routing |

**Composer Package Dihapus:**

| Package | Versi |
|---|---|
| `midtrans/midtrans-php` | 2.6.x |

**File yang Dimodifikasi:**

| File | Perubahan |
|---|---|
| `app/Filament/Tenant/Resources/BillingResource.php` | Hapus action `pay_now` (Snap), hapus import `MidtransService` dan `Action`; hanya tersisa action `upload_receipt` |
| `app/Filament/Resources/SubscriptionResource.php` | Hapus action `pay_now` (Snap), hapus import `MidtransService` dan `Action`; `->actions([])` |
| `routes/web.php` | Hapus route `POST /webhook/midtrans` dan import `MidtransWebhookController` |
| `bootstrap/app.php` | Hapus CSRF exception `webhook/midtrans`; blok `->withMiddleware()` kini kosong |
| `config/services.php` | Hapus seluruh blok konfigurasi `midtrans` |
| `.env.example` | Hapus `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION`; template kini hanya berisi env yang benar-benar digunakan |
| `CLAUDE.md` | Hapus semua referensi Midtrans: Stack table, Phase 5 desc, Important Files, Configuration section, backlog test item, Development Notes; ganti dengan "Manual bank transfer + WhatsApp confirmation" |

**Sebelum → Sesudah Tenant Billing Actions:**

| Aspek | Sebelum | Sesudah |
|---|---|---|
| Actions tersedia | `pay_now` (Snap) + `upload_receipt` | `upload_receipt` saja |
| Dependensi | `MidtransService`, `\Midtrans\Snap` | Tidak ada dependensi payment gateway |
| Test coverage | 76 test (termasuk 10 MidtransWebhookTest) | 66 test (10 test Midtrans dihapus bersama file) |

#### A2 — Production Hardening: Deploy Artifacts & Checklist

| File | Keterangan |
|---|---|
| `deploy/production-checklist.md` | Panduan deploy lengkap: environment, dependency + build, database, storage link, cache optimize, Supervisor queue worker, cron scheduler, verifikasi commands, post-deploy manual checks, monitoring endpoint |
| `deploy/supervisor.conf` | Config Supervisor untuk queue worker: `--memory=128`, `--tries=3`, `--max-time=3600`, log ke `storage/logs/worker.log`, instruksi setup sebagai komentar |
| `deploy/crontab.txt` | Crontab entry scheduler Laravel + komentar jadwal tiap job (ThrottleOverdue 01:00, SuspendJuragan 01:30, GenerateBills tgl 1 00:01, GenerateSubscriptions tgl 1 00:05) |
| `.env.example` | Rewrite sebagai template production-ready: `APP_ENV=production`, `APP_DEBUG=false`, MySQL, SMTP mail, MIKROTIK_* |

#### A3 — Bug Fix Terungkap Saat Testing: `suspended_at` Tidak Fillable

*(Tercatat lebih lengkap di entri 13:40 WITA di atas — ini adalah catatan bahwa bug ini ditemukan dan diperbaiki dalam rangkaian sesi yang sama)*

**Alasan Perubahan:**

Keputusan bisnis: NexaSpace tidak lagi menggunakan Midtrans sebagai payment gateway. Semua pembayaran — baik tagihan anak kos maupun langganan juragan — menggunakan transfer bank manual yang dikonfirmasi via WhatsApp, lalu developer menandai status `paid` secara manual di panel admin. Dengan menghapus Midtrans sepenuhnya, codebase menjadi lebih sederhana, tidak ada dependensi ke layanan pihak ketiga berbayar, dan tidak ada risiko webhook timeout atau fraud check.

Production hardening (checklist, Supervisor config, crontab) dilakukan agar deployment ke `nexaspace.site` memiliki panduan yang jelas dan tidak ada langkah yang terlewat.

**Hasil Akhir:**

- Seluruh Midtrans code dihapus bersih: 5 file dihapus, 7 file dimodifikasi, 1 package diuninstall.
- Pembayaran kini 100% manual: transfer bank → konfirmasi WhatsApp → developer tandai paid di `/admin`.
- Deploy artifacts lengkap tersedia di folder `deploy/` untuk production deployment ke Hostinger/VPS.
- `CLAUDE.md` sudah bersih dari semua referensi Midtrans.
- Test suite berjalan 66/66 hijau setelah penghapusan `MidtransWebhookTest.php`.

---

## [Sesi Kerja] — 6 Juni 2026 (lanjutan sore)

---

### 13:40 WITA — Phase 6: Implementasi Penuh (Dashboard, Quota UI, Tests, Bug Fix)

**Apa yang Diubah:**

#### Phase 6A — Enhanced Juragan Dashboard

| File | Perubahan |
|---|---|
| `app/Filament/Widgets/StatsOverview.php` | Perkaya `juraganStats()`: stat "Kamar Terisi" kini tampilkan `X / Y kamar` + deskripsi sisa slot + warna danger jika kuota penuh; stat "Perangkat Aktif" + deskripsi jumlah yang di-throttle; stat "Tagihan Belum Lunas" + deskripsi jumlah yang sudah di-throttle + warna danger; stat "Pendapatan Bulan Ini" + deskripsi bulan berjalan |

**Sebelum → Sesudah Stat "Kamar Terisi":**

| Aspek | Sebelum | Sesudah |
|---|---|---|
| Label | `"Anak Kos Saya"` | `"Kamar Terisi"` |
| Nilai | Angka tenant saja | `"5 / 20 kamar"` |
| Deskripsi | `"Kuota paket: 20"` | `"15 slot tersisa"` / `"Kuota penuh"` |
| Warna | Selalu `primary` | `danger` jika kuota penuh, `primary` jika belum |

#### Phase 6B — Quota Management Widget Baru

| File | Keterangan |
|---|---|
| `app/Filament/Widgets/JuraganQuotaWidget.php` | Widget baru khusus juragan (`canView()` → false untuk developer). Menampilkan: progress bar kuota (warna hijau/kuning/merah sesuai persentase), warning box daftar kamar tanpa rate bulanan, daftar semua kamar dengan rate masing-masing |
| `resources/views/filament/widgets/juragan-quota-widget.blade.php` | Blade view widget: progress bar HTML/Tailwind, warning box kamar tanpa rate, grid daftar kamar dengan warna kuning untuk yang belum punya rate |
| `app/Providers/Filament/AdminPanelProvider.php` | Daftarkan `JuraganQuotaWidget::class` ke array `->widgets()` |

#### Phase 6C — Cross-Juragan Subscription Isolation Tests

| File | Keterangan |
|---|---|
| `database/factories/SubscriptionFactory.php` | Factory baru: state `paid()`, `overdue()`, dan `forMonth(Carbon)` |
| `app/Models/Subscription.php` | Tambah `newFactory()` method untuk link ke `SubscriptionFactory` |
| `tests/Feature/SubscriptionIsolationTest.php` | 9 test baru: isolasi query juragan A vs B, `SuspendOverdueJuraganJob` hanya suspend juragan yang tepat + grace period benar + tidak re-suspend yang sudah suspended, `SubscriptionObserver` hanya clear `suspended_at` juragan yang benar, panel access blocked/unblocked sesuai `suspended_at` |

#### Bug Fix: `suspended_at` Tidak Masuk `$fillable` User

| File | Perubahan |
|---|---|
| `app/Models/User.php` | Tambah `'suspended_at'` ke `#[Fillable]` attribute |
| `app/Models/User.php` | Tambah cast `'suspended_at' => 'datetime'` di `casts()` |

**Mengapa ini bug kritis:**

`SuspendOverdueJuraganJob` memanggil `$subscription->juragan->update(['suspended_at' => now()])` dan `SubscriptionObserver` memanggil `$subscription->juragan->update(['suspended_at' => null])`. Karena `suspended_at` tidak ada di `$fillable`, kedua panggilan `update()` tersebut **diam-diam gagal** (mass assignment protection). Artinya fitur suspend juragan dan pemulihan akses setelah bayar subscript tidak benar-benar berfungsi di production. Test Phase 6C mengekspos bug ini.

**Alasan Perubahan:**

Phase 6 adalah langkah polish & test setelah seluruh fitur inti selesai. Dashboard juragan sebelumnya kurang informatif (hanya angka polos). Quota management sama sekali belum ada tampilan visual. Dan belum ada test untuk isolasi subscription antar-juragan.

**Hasil Akhir:**

- Dashboard juragan menampilkan info yang lebih kaya: kuota terisi vs tersedia, warning perangkat throttled, dan status tagihan detail.
- Widget Quota Management baru muncul di bawah StatsOverview untuk juragan: progress bar kuota + daftar kamar lengkap + warning kamar tanpa rate.
- 9 test baru untuk isolasi subscription berhasil hijau semua.
- Bug `suspended_at` tidak fillable diperbaiki — fitur suspend juragan dan pemulihan akses kini benar-benar berfungsi.
- **Total: 76/76 test hijau** (sebelumnya 62, sekarang naik 14 dengan test-test baru).

---

### 13:15 WITA — Informasi Domain Production + Mulai Phase 6

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `CLAUDE.md` | Tambah seksi "Production Deployment": domain `nexaspace.site`, URL admin & tenant panel, status Live/Online |
| `CLAUDE.md` | Phase 6 di roadmap diubah dari `⬜` (belum mulai) menjadi `🔄` (in progress) |

**Alasan Perubahan:**

Project NexaSpace sudah online dan dapat diakses publik di `https://nexaspace.site`. Informasi ini penting dicatat di `CLAUDE.md` agar setiap AI agent di sesi berikutnya mengetahui bahwa perubahan yang dilakukan berdampak ke production, dan tidak menganggap project masih dalam tahap development lokal saja.

**Hasil Akhir:**

`CLAUDE.md` kini mencatat bahwa platform sudah live di `nexaspace.site`. Phase 6 resmi dimulai.

---

---

### 03:32 WITA — Password Default Juragan Hasil Provisioning

**Apa yang Diubah:**

Membuat akun juragan hasil approval/provisioning langsung dapat dipakai login dengan password default yang sama seperti akun anak kos:

| File / Data | Perubahan |
|---|---|
| `app/Jobs/ProvisionTenantJob.php` | Password awal juragan diubah dari random `Str::password(...)` menjadi `password`. Email kredensial tetap mengirim password juragan dan anak kos, tetapi keduanya kini konsisten memakai default `password`. |
| `tests/Feature/ProvisioningTest.php` | Test provisioning diperbarui untuk memastikan email kredensial berisi password juragan `password`, hash password juragan valid untuk `password`, dan akun tenant juga tetap valid untuk `password`. |
| `CLAUDE.md` | Dokumentasi Phase 4 diperbarui: akun juragan dan anak kos hasil provisioning memakai password awal `password`. |
| Database lokal | Password akun existing `owner@kos-reb.com` di-reset ke `password` agar akun kos reb yang sudah terlanjur diprovision bisa langsung digunakan untuk login. |

**Alasan Perubahan:**

Sebelumnya anak kos hasil provisioning memakai password default `password`, tetapi akun juragan memakai password random yang hanya dikirim lewat email/log. Di local development hal ini membingungkan karena akun juragan terlihat sudah dibuat namun tidak bisa login memakai password default yang sama.

**Hasil Akhir:**

- Akun juragan baru hasil approval dapat login ke `/admin` memakai `owner@<slug>.com` dan password `password`.
- Akun anak kos tetap login ke `/tenant` memakai `roomN@<slug>.com` dan password `password`.
- Akun `owner@kos-reb.com` sekarang sudah valid memakai password `password`.

---

### 03:19 WITA — Redesign Halaman Pendaftaran Tanpa Card Input

**Apa yang Diubah:**

Merombak tampilan `resources/views/daftar.blade.php` untuk URL `/daftar/{plan}` agar mengikuti referensi visual yang diberikan:

| File | Perubahan |
|---|---|
| `resources/views/daftar.blade.php` | Hapus seluruh wrapper card pada ringkasan paket dan form input. Layout diganti menjadi dua kolom tanpa card: kiri berisi plan terpilih, harga, kuota, benefit, note onboarding, dan plan switcher; kanan berisi heading serta form input langsung di halaman. CTA, helper payment text, error state, dan JavaScript submit tetap dipertahankan. Footer hijau full-width ditambahkan dengan kesan aman/terpercaya seperti referensi. |

**Alasan Perubahan:**

Pengguna meminta halaman `/daftar/pro` dibuat menyerupai mockup baru, dapat menyesuaikan paket yang dipilih, tidak memakai card sebagai tempat input, dan footer memiliki kesan visual yang sama dengan referensi.

**Hasil Akhir:**

Halaman `/daftar/lite`, `/daftar/pro`, dan `/daftar/custom` kini tampil sebagai halaman plan + input yang lebih bersih, tanpa card form. Konten kiri otomatis mengikuti paket aktif, sementara submit tetap mengarah ke pembayaran manual untuk LITE/PRO dan WhatsApp untuk CUSTOM.

---

### 03:13 WITA — Polish UI Halaman Pembayaran Manual

**Apa yang Diubah:**

Memoles tampilan `resources/views/daftar-pembayaran.blade.php` agar lebih menyerupai referensi visual yang diminta:

| File | Perubahan |
|---|---|
| `resources/views/daftar-pembayaran.blade.php` | Redesign layout pembayaran manual: navbar lebih bersih, hero card lebih besar dengan ilustrasi kartu/check, kartu bank dua kolom dengan logo teks/aksen warna, sidebar ringkasan lebih visual dengan header hijau, ikon ID/kos/WhatsApp, blok nominal transfer, divider `atau`, dan CTA WhatsApp lebih menonjol. Blok `Catatan rekening` beserta teks nomor rekening fiktif dihapus sepenuhnya dari UI. |
| `app/Http/Controllers/RegistrationController.php` | Data bank manual diperkaya dengan `logo` dan `logo_class` agar Blade bisa menampilkan label/logo bank sederhana per rekening. |
| `tests/Feature/RegistrationFlowTest.php` | Test halaman pembayaran manual diperbarui untuk memastikan teks `Catatan rekening` dan pesan nomor rekening fiktif tidak tampil lagi. |

**Alasan Perubahan:**

Pengguna meminta tampilan halaman pembayaran manual dipoles seperti mockup yang diberikan dan meminta catatan rekening fiktif dihapus dari halaman agar UI terlihat lebih siap pakai.

**Hasil Akhir:**

Halaman `/daftar/pembayaran/{registration}` tampil lebih premium dan fokus pada instruksi transfer + konfirmasi WhatsApp. Tidak ada lagi teks `Catatan rekening` maupun peringatan nomor rekening fiktif di halaman pembayaran.

---

### 02:57 WITA — Alur Pembayaran Pendaftaran Manual via Transfer Bank + WhatsApp

**Apa yang Diubah:**

Mengganti alur pembayaran pendaftaran publik LITE/PRO dari Midtrans Snap menjadi pembayaran manual:

| File | Perubahan |
|---|---|
| `app/Http/Controllers/RegistrationController.php` | Hapus pemanggilan `MidtransService::getSnapTokenForRegistration()` dari `store()`. Setelah registration tersimpan, LITE/PRO kini mengembalikan signed `payment_url` ke `/daftar/pembayaran/{registration}`; CUSTOM tetap mengembalikan `wa_url`. Tambah method `payment()` untuk render halaman pembayaran manual, daftar bank fiktif, nominal paket, dan URL konfirmasi WhatsApp otomatis. |
| `routes/web.php` | Tambah route `GET /daftar/pembayaran/{registration}` bernama `daftar.payment`, diletakkan sebelum route `/daftar/{plan?}` agar tidak bentrok dengan parameter plan, dan dilindungi middleware `signed` agar detail pendaftaran tidak mudah diakses dari ID berurutan. |
| `resources/views/daftar-pembayaran.blade.php` | File baru: halaman instruksi transfer manual dengan opsi BCA, Mandiri, BRI, BNI memakai nomor rekening fiktif, ringkasan paket/nominal, ID pendaftaran, dan tombol `Konfirmasi via WhatsApp` dengan pesan otomatis. |
| `resources/views/daftar.blade.php` | Hapus pemuatan Midtrans Snap dari JavaScript form pendaftaran. Submit form kini redirect ke `payment_url`. Copy tombol/loading dan catatan pembayaran diubah menjadi transfer bank manual + konfirmasi WhatsApp. |
| `app/Filament/Resources/RegistrationResource.php` | Tambah bulk action developer untuk `Tandai Pembayaran: Lunas` dan `Tandai Pembayaran: Belum Bayar`, karena pembayaran pendaftaran kini diverifikasi manual dari konfirmasi WhatsApp/bukti transfer. |
| `app/Services/MidtransService.php` | Hapus method Snap khusus registration (`getSnapTokenForRegistration`). Midtrans tetap dipakai untuk billing tenant (`BILL-`) dan subscription juragan (`SUB-`). |
| `app/Http/Controllers/MidtransWebhookController.php` | Hapus handler `REG-`; webhook kini hanya memproses prefix `BILL-` dan `SUB-`, sedangkan prefix tidak dikenal dikembalikan `200 OK` tanpa mengubah data. |
| `tests/Feature/RegistrationFlowTest.php` | Update ekspektasi response non-CUSTOM dari field Snap menjadi `payment_url`; tambah test halaman pembayaran manual menampilkan bank, nominal, dan link WhatsApp; tambah test akses tanpa signature ditolak. |
| `CLAUDE.md` | Update dokumentasi project agar public registration payment dijelaskan sebagai manual transfer + WhatsApp confirmation, bukan Midtrans Snap upfront payment. |

**Alasan Perubahan:**

Pengguna memutuskan untuk tidak memakai Midtrans pada pembayaran pendaftaran juragan. Flow baru dibutuhkan agar calon juragan tetap bisa melewati step pembayaran tanpa konfigurasi payment gateway: pilih/lihat rekening bank, transfer manual, lalu mengirim konfirmasi otomatis ke WhatsApp NexaSpace.

**Hasil Akhir:**

- `/daftar/pro` dan `/daftar/lite` tidak lagi bergantung pada `MIDTRANS_SERVER_KEY` / `MIDTRANS_CLIENT_KEY`.
- Setelah submit form, calon juragan diarahkan ke signed URL `/daftar/pembayaran/{registration}`.
- Halaman pembayaran menampilkan opsi rekening bank fiktif dan nominal paket.
- Tombol konfirmasi membuka WhatsApp dengan detail `REG-{id}`, paket, nominal, nama kos, email, nomor WA, dan jumlah kamar.
- Developer bisa menandai pembayaran pendaftaran sebagai `Lunas` atau `Belum Bayar` secara manual dari `RegistrationResource`.
- Test `RegistrationFlowTest` hijau untuk 9 skenario.

---

### 22:15 WITA — Halaman Daftar Baru + Tabel Perbandingan Paket (Landing Page Redesign)

**Apa yang Diubah:**

Dua perubahan besar pada landing page dan alur pendaftaran:

1. **Modal form → Halaman Dedicated `/daftar/{plan}`**: tombol paket di landing page (LITE, PRO, CUSTOM) sekarang menavigasi ke halaman baru `/daftar/{plan}` alih-alih membuka modal. Halaman ini menampilkan layout dua kolom: kiri = ringkasan paket + plan switcher (sticky), kanan = form pendaftaran + tombol bayar. Setelah submit, Midtrans Snap dibuka langsung di halaman tersebut. Setelah pembayaran sukses, user dialihkan ke `/daftar/sukses`.

2. **Tabel Perbandingan Paket**: section baru `#perbandingan` ditambahkan di bawah pricing cards, sebelum testimoni. Menampilkan tabel fitur per kolom (LITE / PRO / CUSTOM) dengan 11 baris fitur, kolom PRO di-highlight dengan warna brand hijau dan badge "⭐ Paling Laris". Di bawah tabel ada CTA button per paket.

3. **Webhook Midtrans diperluas**: menangani prefix `REG-{id}-{ts}` untuk pembayaran pendaftaran — mengupdate `payment_status = 'paid'` dan `midtrans_order_id` di tabel `registrations`.

4. **RegistrationResource Filament**: kolom `payment_status` (BadgeColumn) ditambahkan setelah kolom `status` — menampilkan "Lunas" (hijau) atau "Belum Bayar" (kuning).

**File yang Dibuat:**

| File | Keterangan |
|---|---|
| `resources/views/daftar.blade.php` | Halaman pendaftaran dua kolom: plan summary (sticky) + form + Midtrans Snap |
| `resources/views/daftar-sukses.blade.php` | Halaman sukses setelah pembayaran berhasil |
| `database/migrations/2026_06_06_020411_add_payment_fields_to_registrations.php` | Kolom `payment_status ENUM('unpaid','paid')` dan `midtrans_order_id VARCHAR NULL` di tabel `registrations` |

**File yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/landing.blade.php` | Ganti 3 tombol paket dari `onclick="openInquiryModal()"` ke `<a href="{{ route('daftar', plan) }}">`. Hapus seluruh modal HTML (~280 baris) dan script block (~100 baris). Tambah section `#perbandingan` di antara harga dan testimoni. |
| `app/Http/Controllers/RegistrationController.php` | Tambah method `show()` untuk halaman `/daftar/{plan}`. Update `store()`: CUSTOM → kembalikan `wa_url`; LITE/PRO → panggil `MidtransService::getSnapTokenForRegistration()`, kembalikan `{snap_token, client_key, snap_url}`. |
| `app/Models/Registration.php` | Tambah `payment_status` dan `midtrans_order_id` ke `$fillable`. |
| `app/Services/MidtransService.php` | Tambah method `getSnapTokenForRegistration()`: order_id `REG-{id}-{ts}`, amount per paket (LITE=199k, PRO=499k), return null untuk CUSTOM. |
| `app/Http/Controllers/MidtransWebhookController.php` | Extend `handle()` dengan routing prefix `REG-` → `handleRegistrationPayment()`. Method baru: update `payment_status='paid'` dan `midtrans_order_id` di registration. |
| `app/Filament/Resources/RegistrationResource.php` | Tambah `BadgeColumn::make('payment_status')` setelah kolom `status`: Lunas (success) / Belum Bayar (warning). |
| `routes/web.php` | Tambah 2 route: `GET /daftar/sukses` (view daftar-sukses) dan `GET /daftar/{plan?}` (RegistrationController@show, where plan=lite|pro|custom). |
| `tests/Feature/RegistrationFlowTest.php` | Split test `test_response_returns_whatsapp_url_with_details` menjadi dua: satu untuk non-CUSTOM plan (ekspek snap_token), satu untuk CUSTOM plan (ekspek wa_url). |

**Alasan Perubahan:**

Permintaan perombakan UX pendaftaran: registrasi via modal dianggap kurang profesional dan tidak mengakomodasi Midtrans payment inline. Halaman dedicated `/daftar/{plan}` memberi konteks lebih lengkap (ringkasan paket + harga) sambil langsung memfasilitasi pembayaran di tempat yang sama. Tabel perbandingan paket ditambahkan untuk membantu calon juragan membandingkan fitur antar paket sebelum memilih.

**Hasil Akhir:**

- Klik paket di landing page → navigasi ke `/daftar/{plan}` (bukan modal)
- Isi form → klik Bayar → Midtrans Snap muncul → setelah sukses redirect ke `/daftar/sukses`
- Webhook `REG-` diproses: `payment_status` diupdate ke `paid`, developer bisa lihat di RegistrationResource
- Section perbandingan paket tampil di antara pricing cards dan testimoni
- Modal HTML dan script lama telah dihapus sepenuhnya dari landing page
- 63 test hijau (1 test baru ditambahkan, 1 test diupdate)

---

### 10:00 WITA — Fase 5: Subscription Billing (Juragan → NexaSpace)

**Apa yang Diubah:**

Implementasi penuh sistem tagihan langganan juragan ke NexaSpace. Juragan membayar bulanan sesuai paket; jika telat melebihi grace period, akun juragan dan seluruh anak kosnya diblokir otomatis. Pembayaran via Midtrans Snap; akses dipulihkan otomatis setelah webhook diterima.

**File yang Dibuat:**

| File | Keterangan |
|---|---|
| `database/migrations/2026_06_06_013020_create_subscriptions_table.php` | Tabel `subscriptions`: juragan_id, amount, subscription_month, due_date, status (unpaid/paid/overdue), soft delete |
| `database/migrations/2026_06_06_013022_add_suspended_at_to_users_table.php` | Kolom `suspended_at TIMESTAMP NULL` di tabel `users` |
| `app/Models/Subscription.php` | Model Subscription, relasi `juragan()`, cast date & integer |
| `app/Observers/SubscriptionObserver.php` | Ketika status → `paid`: clear `suspended_at` juragan secara otomatis |
| `app/Jobs/GenerateMonthlySubscriptionsJob.php` | Buat tagihan bulanan untuk juragan plan LITE (Rp199k) & PRO (Rp499k); idempotent, chunk(50) |
| `app/Jobs/SuspendOverdueJuraganJob.php` | Grace 2 hari: set subscription → `overdue`, set `juragan.suspended_at = now()` |
| `app/Filament/Resources/SubscriptionResource.php` | Resource Filament: developer lihat semua, juragan lihat milik sendiri; action Bayar Sekarang (Midtrans Snap); bulk action Tandai Lunas/Belum Lunas (developer only) |
| `app/Filament/Resources/SubscriptionResource/Pages/ListSubscriptions.php` | Halaman list standar |
| `resources/views/filament/subscription/pay-now.blade.php` | Modal Midtrans Snap untuk pembayaran langganan |

**File yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Services/MidtransService.php` | Tambah `getSnapTokenForSubscription()`: order_id `SUB-{id}-{ts}`, item detail nama langganan & bulan |
| `app/Http/Controllers/MidtransWebhookController.php` | Extend `handle()` untuk routing ke `handleBilling()` (prefix `BILL-`) atau `handleSubscription()` (prefix `SUB-`) |
| `app/Models/User.php` | `canAccessPanel()`: juragan blocked jika `suspended_at !== null`; anak kos blocked jika `juragan.suspended_at !== null` |
| `app/Providers/AppServiceProvider.php` | Register `SubscriptionObserver` di `boot()` |
| `routes/console.php` | Tambah 2 scheduler: `generate-monthly-subscriptions` (tanggal 1 jam 00:05) dan `suspend-overdue-juragan` (harian 01:30) |
| `app/Filament/Widgets/StatsOverview.php` | Tambah kartu "Langganan NexaSpace" di `juraganStats()`: tampilkan status bulan ini (Lunas/Belum Lunas/Menunggak/Belum Ada Tagihan) + jatuh tempo |

**Alasan Perubahan:**

Fase 5 dari roadmap SaaS multi-tenant. Setelah provisioning berjalan otomatis (Fase 4), perlu mekanisme billing agar juragan benar-benar membayar ke NexaSpace — bukan hanya mendapatkan akun gratis selamanya.

**Hasil Akhir:**

- Setiap tanggal 1, tagihan langganan di-generate otomatis untuk juragan LITE & PRO.
- Jika melewati jatuh tempo + 2 hari grace period, juragan dan semua anak kosnya diblokir dari panel.
- Pembayaran via Midtrans Snap di halaman Langganan; setelah webhook dikonfirmasi, akses dipulihkan dalam hitungan detik.
- CUSTOM plan dikelola manual oleh developer (harga nego).
- 62 test tetap hijau setelah implementasi.

---

### 08:30 WITA — Fix Password Default Akun Anak Kos

**Apa yang Diubah:**

1. `app/Jobs/ProvisionTenantJob.php` — password anak kos diubah dari `Str::password(10, ...)` (random) menjadi `'password'` sebagai default. Password yang dikirim ke email juragan sesuai (tetap berisi `password`).
2. `app/Console/Commands/ResetTenantPasswords.php` — artisan command baru `tenants:reset-passwords {password=password}` untuk mereset seluruh akun anak kos (role `tenant`) ke password default. Dilengkapi konfirmasi interaktif sebelum eksekusi.
3. Command langsung dijalankan: 89 akun anak kos yang sudah ada di-reset ke `password`.

**Alasan Perubahan:**

Akun anak kos yang dibuat via `ProvisionTenantJob` sebelumnya menggunakan password acak (`Str::password`). Di environment development dengan `MAIL_MAILER=log`, password tersebut tidak langsung terlihat — hanya tersimpan di `storage/logs/laravel.log` dan tidak praktis untuk digunakan saat testing. Hal ini mengharuskan penambahan password manual satu per satu.

**Hasil Akhir:**

- Semua 89 akun anak kos yang sudah ada kini dapat login dengan password `password`.
- Akun anak kos yang dibuat via provisioning ke depannya juga langsung menggunakan `password` sebagai default, sehingga juragan bisa langsung login tanpa harus mencari password di log.
- Tersedia command `php artisan tenants:reset-passwords` untuk mereset ulang kapan saja jika dibutuhkan.

---

## [Sesi Kerja] — 5 Juni 2026 (lanjutan)

---

### 02:00 WITA — Pivot SaaS Multi-Tenant: FASE 4 — Approval & Provisioning Otomatis

**Apa yang Diubah:**

Menyelesaikan inti SaaS: saat developer menyetujui pendaftaran, sistem otomatis membuat akun juragan + seluruh akun anak kos (sesuai kuota paket) dan mengirim kredensial ke email juragan. (Langkah 6–7 dari alur 9 langkah.)

**File yang Dibuat:**

| File | Keterangan |
|---|---|
| `app/Jobs/ProvisionTenantJob.php` | Membuat 1 akun juragan (`owner@<slug>.com`) + N akun anak kos (`room1..roomN@<slug>.com`, N = kuota paket) dalam 1 transaksi DB, kirim email kredensial, set registrasi `active`. Idempotent (skip jika sudah `active`), slug otomatis unik (`mutiara`→`mutiara-2`) |
| `app/Mail/TenantProvisionedMail.php` | Email kredensial, pengirim dipaksa `noreply@nexaspace.site` |
| `resources/views/emails/tenant-provisioned.blade.php` | Template email: akun juragan + tabel akun anak kos + URL login |
| `tests/Feature/ProvisioningTest.php` | 7 test: PRO=40/LITE=20/CUSTOM=max(50,room_count), email terkirim, password ter-hash, idempotensi, resolusi bentrok slug |

**File yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Resources/RegistrationResource.php` | Bulk action `approve_provision` ("Setujui & Buat Akun") → `ProvisionTenantJob::dispatchSync()` per record + notifikasi ringkasan |
| `CLAUDE.md` | Tambah variabel MAIL di Configuration + catatan provisioning |

**Kuota Akun per Paket:**

| Paket | Akun Anak Kos di-generate |
|---|---|
| LITE | 20 (`room1`…`room20`) |
| PRO | 40 (`room1`…`room40`) |
| CUSTOM | `max(50, jumlah kamar)` |

**Catatan teknis:**
- Pembuatan akun + pengiriman email dibungkus `DB::transaction` (all-or-nothing) sehingga tidak ada kondisi setengah jadi; retry aman.
- Password di-generate acak (huruf+angka), di-hash di DB (cast `hashed`), versi plain hanya dikirim via email.
- Di dev (`MAIL_MAILER=log`), kredensial muncul di `storage/logs/laravel.log` — provisioning tetap berhasil tanpa SMTP.

**Alasan Perubahan:** Sebelumnya juragan & anak kos dibuat manual oleh developer. Ini adalah fitur inti SaaS: onboarding pelanggan sepenuhnya otomatis sejak persetujuan.

**Hasil Akhir:** Developer cukup memilih pendaftaran `pending` → "Setujui & Buat Akun" → seluruh akun terbentuk sesuai paket, kredensial terkirim ke email juragan, status jadi `active`. **Seluruh 62 test hijau** (55 + 7 baru).

---

### 01:30 WITA — Animasi Sukses CRUD (Check Hijau Berputar)

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Providers/Filament/AdminPanelProvider.php` | Render hook `HEAD_END` menyuntik CSS keyframe `nexaCheckPop` — ikon check pada toast sukses (`.fi-color-success`) pop + berputar 0.6s |
| `app/Filament/Resources/RegistrationResource.php` | `successNotificationTitle` pada bulk Disetujui/Ditolak |
| `app/Filament/Resources/BillingResource.php` | `successNotificationTitle` pada Mark Paid/Unpaid |
| `app/Filament/Resources/DeviceResource.php` | `successNotificationTitle` pada Throttle/Active/Block |

**Alasan Perubahan:** Bulk action dengan `->action()` kustom tidak memunculkan notifikasi bawaan, sehingga aksi seperti persetujuan pendaftaran terasa tanpa umpan balik. Pengguna meminta animasi ringan berupa tanda check hijau.

**Hasil Akhir:** Setiap aksi sukses (termasuk persetujuan di `/admin/registrations`) memunculkan toast check hijau yang muncul dengan efek pop + putar singkat. Ringan, tanpa dependensi tambahan.

---

### 01:15 WITA — Pivot SaaS Multi-Tenant: FASE 3 — Alur Pendaftaran Publik

**Apa yang Diubah:**

Mengubah modal "inquiry" lama menjadi alur **pendaftaran langganan** penuh (langkah 1–5 dari alur 9 langkah). Tabel `inquiries` dievolusi menjadi `registrations`.

**File yang Dibuat:**

| File | Keterangan |
|---|---|
| `database/migrations/2026_06_06_010000_transform_inquiries_into_registrations.php` | Rename `inquiries`→`registrations`; tambah kolom `kos_name` & `email`; ubah enum status `new/contacted/active/closed` → `pending/approved/active/rejected` (data lama dipetakan) |
| `app/Models/Registration.php` | Model baru (fillable + accessor `plan_label`) |
| `app/Http/Controllers/RegistrationController.php` | Validasi (+ email & kos_name), simpan status `pending`, kembalikan WA URL konfirmasi langganan |
| `app/Filament/Resources/RegistrationResource.php` | Resource developer-only; kolom nama juragan/kos/email/paket/status; badge navigasi jumlah `pending`; bulk action Disetujui/Ditolak |
| `app/Filament/Resources/RegistrationResource/Pages/ListRegistrations.php` | List page |
| `tests/Feature/RegistrationFlowTest.php` | 6 test: simpan pending, WA URL berisi detail, email/kos_name wajib, plan invalid ditolak, status tak bisa di-inject |

**File yang Dihapus:**

`app/Models/Inquiry.php`, `app/Http/Controllers/InquiryController.php`, `app/Filament/Resources/InquiryResource.php`, `.../Pages/ListInquiries.php`.

**File yang Diubah:**

| File | Perubahan |
|---|---|
| `routes/web.php` | `POST /inquiry` → `POST /register` (`registration.store`) |
| `app/Filament/Widgets/StatsOverview.php` | "New Inquiries" → "Pendaftaran Baru" (hitung status `pending`) |
| `resources/views/landing.blade.php` | Modal jadi form pendaftaran: tambah **Nama Kos** & **Email**, relabel jadi "Nama Juragan", header "Daftar Berlangganan", tombol "Daftar & Konfirmasi via WhatsApp", link **Konsultasi via WhatsApp** (opsional, tanpa form), success state diperbarui; JS `clearErrors` + fetch ke `registration.store` |
| `tests/Feature/JuraganIsolationTest.php` | `Inquiry`/`InquiryResource` → `Registration`/`RegistrationResource` |
| **`bootstrap/app.php`** | **Perbaikan bug:** `shouldRenderJsonWhen` kini juga `\|\| $request->expectsJson()` |

**🐛 Bug yang Ditemukan & Diperbaiki:**

`bootstrap/app.php` sebelumnya hanya merender error sebagai JSON untuk path `api/*`. Karena form landing posting ke `/register` (bukan `api/*`) lewat `fetch` dengan `Accept: application/json`, **error validasi tidak dikembalikan sebagai JSON** — inline error di form rusak (berlaku juga untuk `/inquiry` lama). Diperbaiki dengan menambah `|| $request->expectsJson()`, sehingga setiap permintaan yang meminta JSON mendapat respons 422 JSON yang benar.

**Alasan Perubahan:** Modal lama hanya menangkap lead sederhana. Sebagai SaaS, dibutuhkan pendaftaran langganan lengkap (termasuk email tujuan kredensial & nama kos untuk namespace) dengan status persetujuan sebelum akun diaktifkan.

**Hasil Akhir:** Pengunjung dapat mendaftar langganan langsung dari landing → tersimpan sebagai `registrations` status `pending` → diarahkan ke WhatsApp dengan pesan konfirmasi lengkap. Developer melihat & menyetujui pendaftaran di `/admin/registrations` (tersembunyi dari juragan). **Seluruh 55 test hijau** (49 + 6 baru). Provisioning otomatis akun saat approve = Fase 4.

---

### 00:30 WITA — Pivot SaaS Multi-Tenant: FASE 2 — RBAC & Isolasi Data

**Apa yang Diubah:**

Menerapkan isolasi data per-juragan di panel `/admin`. Sebelumnya developer & juragan sama-sama melihat SEMUA data; kini juragan hanya melihat miliknya sendiri.

**File yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Resources/UserResource.php` | `getEloquentQuery()`: juragan hanya lihat anak kos miliknya (`juragan_id`=self, role tenant); field role/kos/juragan_id hanya tampil untuk developer |
| `app/Filament/Resources/DeviceResource.php` | `getEloquentQuery()`: juragan hanya lihat device milik anak kosnya (`whereHas('user', juragan_id=self)`); opsi select `user_id` di-scope |
| `app/Filament/Resources/BillingResource.php` | `getEloquentQuery()` + opsi select `user_id` di-scope sama seperti Device |
| `app/Filament/Resources/InquiryResource.php` | `canAccess()`, `canViewAny()`, `shouldRegisterNavigation()` → developer-only (juragan tidak bisa lihat lead prospek lain) |
| `app/Filament/Widgets/StatsOverview.php` | Dipecah: `developerStats()` (platform-wide + Total Juragan + inquiries + failed jobs) vs `juraganStats()` (hanya anak kos/device/tagihan/pendapatan miliknya) |
| `app/Filament/Resources/UserResource/Pages/CreateUser.php` | `mutateFormDataBeforeCreate()`: juragan create user → paksa `role=tenant` & `juragan_id=self` |
| `app/Filament/Resources/DeviceResource/Pages/CreateDevice.php` | `mutateFormDataBeforeCreate()`: `abort_unless` device dibuat untuk anak kos milik juragan sendiri (defense in depth) |
| `app/Filament/Resources/BillingResource/Pages/CreateBilling.php` | Sama seperti CreateDevice untuk billing |

**File yang Dibuat:**

| File | Keterangan |
|---|---|
| `tests/Feature/JuraganIsolationTest.php` | 7 test: juragan lihat anak kos/device/billing sendiri saja, developer lihat semua, query developer tidak ter-scope, InquiryResource developer-only |

**Tabel Perbandingan Akses:**

| Resource | Developer | Juragan |
|---|---|---|
| Users | Semua user | Hanya anak kos miliknya |
| Devices | Semua device | Hanya device anak kosnya |
| Billings | Semua billing | Hanya billing anak kosnya |
| Inquiries | ✅ Akses penuh | ❌ Tidak terlihat (nav & route) |
| Stats widget | Platform-wide | Khusus kosnya sendiri |

**Alasan Perubahan:** Sebagai produk SaaS multi-tenant, isolasi data adalah keharusan keamanan — satu juragan tidak boleh melihat atau mengutak-atik data juragan lain. Tanpa ini, platform tidak aman dipakai banyak juragan.

**Hasil Akhir:** Isolasi data per-juragan aktif di seluruh resource admin, dengan enforcement ganda (query scope untuk read/edit + `mutateFormDataBeforeCreate` untuk write). InquiryResource sepenuhnya tersembunyi dari juragan. **Seluruh 49 test hijau** (42 lama + 7 baru).

---

### 23:55 WITA — Pivot SaaS Multi-Tenant: FASE 1 — Fondasi Role & Model Data

**Apa yang Diubah:**

NexaSpace dipivot dari aplikasi single-kos menjadi **SaaS multi-tenant** — Haikal (developer) menyewakan platform ke banyak juragan kos, tiap juragan mengelola anak kosnya sendiri secara terisolasi. Fase 1 membangun fondasi role 3-tingkat dan model data multi-tenant.

**Keputusan arsitektur yang dikunci (hasil diskusi dengan Haikal):**
- Domain "menyesuaikan nama kos" = **email/username namespacing saja** (BUKAN subdomain DNS). Kos "Mutiara" → juragan `owner@mutiara.com`, anak kos `room1@mutiara.com`…`roomN@mutiara.com`. Hanya identifier login, tidak menerima email asli.
- Billing **2 lapis**: (L1) juragan bayar langganan ke NexaSpace; (L2) anak kos bayar ke juragan (sistem throttle WiFi yang sudah ada, kini di-scope per-juragan).
- Akun anak kos di-generate **penuh sesuai kuota paket** (PRO=40, LITE=20, CUSTOM=50+).
- Pengirim kredensial: `noreply@nexaspace.site`.

**File yang Dibuat:**

| File | Keterangan |
|---|---|
| `database/migrations/2026_06_05_193000_restructure_users_for_multitenant_saas.php` | Ubah enum `role` dari `admin/tenant` → `developer/juragan/tenant` (migrasi data `admin`→`developer`), tambah kolom `juragan_id` (self-FK, nullOnDelete), `kos_name`, `kos_slug` (unique), `plan` (enum), `room_quota` |

**File yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Models/User.php` | `#[Fillable]` + kolom baru; cast `room_quota`/`monthly_rate` ke integer; helper `isDeveloper()`/`isJuragan()`/`isTenant()`; relasi `juragan()` (belongsTo) & `anakKos()` (hasMany); `canAccessPanel()` — panel admin kini untuk `developer` & `juragan`, panel tenant untuk `tenant` |
| `database/factories/UserFactory.php` | State baru `developer()` & `juragan()` (lengkap dgn kos_name/slug/plan/quota); `admin()` jadi alias `developer()` agar test lama tetap hijau |
| `database/seeders/DatabaseSeeder.php` | Rebuild jadi 3-tingkat: 1 developer → 2 juragan (Kos Mutiara PRO/40, Kos Melati LITE/20) → anak kos per juragan (5 & 4) dengan email `roomN@<slug>.com`, devices, dan 3 bulan billing |
| `app/Filament/Resources/UserResource.php` | Opsi role `developer/juragan/anak kos`; field kondisional juragan (kos_name, kos_slug, plan, room_quota) & anak kos (juragan via relationship, room_number); badge & filter role diperbarui; kolom Kos baru |

**Tabel Perbandingan Role:**

| Aspek | Sebelum | Sesudah |
|---|---|---|
| Enum role | `admin`, `tenant` | `developer`, `juragan`, `tenant` |
| Akses panel `/admin` | hanya `admin` | `developer` + `juragan` |
| Hierarki data | datar (semua tenant 1 kolam) | developer → juragan → anak kos (`juragan_id`) |
| Identitas kos | ❌ tidak ada | `kos_name`, `kos_slug`, `plan`, `room_quota` di user juragan |

**Alasan Perubahan:** Model lama hanya mendukung 1 kos. Sebagai produk SaaS, NexaSpace perlu menampung banyak juragan dengan data terisolasi. Fase 1 adalah pondasi — tanpa role & relasi ini, fase RBAC, pendaftaran, dan provisioning tidak bisa dibangun.

**Hasil Akhir:** Struktur 3-tingkat aktif & terverifikasi (`migrate:fresh --seed`: 1 developer, 2 juragan, 9 anak kos; relasi `anakKos`↔`juragan` berfungsi). Seluruh **42 test tetap hijau**. Catatan: isolasi data per-juragan (juragan hanya lihat datanya sendiri) BELUM diterapkan — itu Fase 2.

---

### 20:30 WITA — Opsi A: Otomatisasi Tagihan Bulanan

**Apa yang Diubah:**

1. **Migration baru** `database/migrations/2026_06_05_192449_add_monthly_rate_to_users_table.php` — menambahkan kolom `monthly_rate` (unsignedInteger, default 0) ke tabel `users`.

2. **`app/Models/User.php`** — `monthly_rate` ditambahkan ke atribut `#[Fillable]`.

3. **`app/Services/BillingService.php`** — method baru `generateMonthlyBills()`:
   - Query semua tenant dengan `monthly_rate > 0` menggunakan `chunk(50)` (hemat memori)
   - Untuk setiap tenant, cek apakah tagihan bulan ini sudah ada (idempotency)
   - Jika belum: buat `Billing` baru dengan `amount = monthly_rate`, `billing_month` = awal bulan, `due_date` = tanggal 10, `status = unpaid`

4. **`app/Jobs/GenerateMonthlyBillsJob.php`** — job baru yang memanggil `BillingService::generateMonthlyBills()`. `tries = 3`, `backoff = 60` detik.

5. **`routes/console.php`** — scheduler baru:
   - `ThrottleOverdueTenantsJob` → setiap hari `01:00` WITA (+ `withoutOverlapping`)
   - `GenerateMonthlyBillsJob` → setiap tanggal 1 jam `00:01` WITA (+ `withoutOverlapping`)

6. **`app/Filament/Resources/UserResource.php`** — form dan tabel tenant:
   - Field `monthly_rate` di form (hanya muncul saat role = tenant), prefix Rp, step 1000
   - Kolom `Rate/Bln` di tabel dengan format `Rp xxx.xxx` atau `—` jika 0

**Alasan Perubahan:** Tagihan sebelumnya dibuat manual oleh admin setiap bulan. Dengan `monthly_rate` per tenant dan job terjadwal, tagihan dibuat otomatis setiap tanggal 1 tanpa intervensi admin.

**Hasil Akhir:** Admin set `monthly_rate` sekali saat onboarding tenant → setiap tanggal 1 jam 00:01 WITA, tagihan seluruh tenant aktif dibuat otomatis. Mekanisme idempotency mencegah duplikasi tagihan jika job dijalankan ulang.

---

### 20:45 WITA — Opsi D: Production Hardening

**Apa yang Diubah:**

1. **`routes/web.php`** — endpoint baru `GET /health`:
   - Cek koneksi database
   - Tampilkan jumlah failed jobs, timestamp, timezone, dan environment
   - Return HTTP 200 jika sehat, 503 jika DB error
   - Dapat dipasang di UptimeRobot atau monitoring eksternal

2. **`app/Filament/Widgets/StatsOverview.php`** — widget baru **Failed Jobs** di dashboard admin:
   - Warna `success` (hijau) jika 0, `danger` (merah) jika ada job gagal
   - Admin langsung tahu jika ada queue job yang perlu diselesaikan

3. **`deploy/supervisor.conf`** — template konfigurasi Supervisor untuk menjalankan queue worker secara permanen di VPS/shared hosting.

4. **`deploy/crontab.txt`** — baris crontab siap pakai untuk Hostinger cPanel > Cron Jobs, menjalankan `php artisan schedule:run` setiap menit.

**Alasan Perubahan:** Tanpa queue worker yang terus berjalan dan scheduler yang terdaftar di crontab, fitur auto-throttle dan auto-generate tagihan tidak akan berjalan di production. Failed jobs yang tidak termonitor bisa membuat pembayaran tidak diproses.

**Hasil Akhir:** Admin punya visibilitas penuh terhadap kesehatan sistem dari dashboard. Panduan deploy siap pakai di folder `deploy/`.

---

## [Sesi Kerja] — 1 Juni 2026

---

### 14:00 WITA — Studi & Audit Codebase Menyeluruh

Membaca dan mempelajari seluruh struktur project NexaSpace dari awal hingga akhir tanpa terkecuali.

**File yang dipelajari:**
- Semua model: `User`, `Device`, `Billing`
- Semua migrasi dan seeder/factory
- Semua Filament resources: Admin (`UserResource`, `DeviceResource`, `BillingResource`) dan Tenant (`BillingResource`)
- Semua widgets: `StatsOverview`, `TenantStatsWidget`
- Semua services: `BillingService`, `MikroTikService`, `MidtransService`
- Job queue: `ThrottleOverdueTenantsJob`
- Observer: `BillingObserver`
- Controllers: `MidtransWebhookController`
- Panel providers: `AdminPanelProvider`, `TenantPanelProvider`
- Semua halaman login kustom dan layout
- Semua view: `landing.blade.php`, `pay-now.blade.php`, login views
- Konfigurasi: `bootstrap/app.php`, `routes/web.php`, `routes/console.php`, `config/services.php`, `.env`

**Kesimpulan audit:** Seluruh 6 fase pengembangan sudah selesai diimplementasikan dan melebihi rencana awal di `CLAUDE.md`.

---

### 14:30 WITA — Perbaikan Section Perbandingan (landing.blade.php)

**Perubahan pada section `#sebelum-sesudah`:**

| # | Apa yang Diubah | Sebelum | Sesudah |
|---|---|---|---|
| 1 | Background sisi kiri | `bg-red-50` + rounded border | Polos, tanpa background |
| 2 | Background sisi kanan | Gradien hijau inline style | Polos, tanpa background |
| 3 | Icon list item kiri | Lingkaran merah dengan karakter `✕` | Dihapus |
| 4 | Icon list item kanan | Lingkaran hijau dengan karakter `✓` | Dihapus |
| 5 | Garis pemisah vertikal | `w-px bg-gray-200` (tidak muncul jelas) | `w-px bg-gray-200 self-stretch` (full height) |
| 6 | Jarak ke tombol WhatsApp | `mt-20` | `mt-32` |

---

### 14:45 WITA — Restrukturisasi Footer (landing.blade.php)

**Perubahan pada `<footer>`:**

| # | Apa yang Diubah | Sebelum | Sesudah |
|---|---|---|---|
| 1 | Struktur kolom | Kolom 3 berisi "Akses Panel" + "Kontak" sekaligus | 4 kolom tunggal: Brand, Navigasi, Akses Panel + Jam, Kontak + Alamat |
| 2 | Alamat kantor | Jl. Raya Serpong No. 28, BSD City, Tangerang Selatan | Jl. Pemuda No. 47, Rawamangun, Pulo Gadung, Jakarta Timur 13220 |
| 3 | Teks bottom bar | Teks kiri + kanan terpisah | Akan diperbarui di sesi berikutnya |
| 4 | Icon sosial media | 4 icon sosial (X, Instagram, WA, YouTube) | 2 icon fungsional: WhatsApp + Email |

---

### 15:15 WITA — Perbaikan Lanjutan Footer & Section Perbandingan

**Dihapus:**

- Teks `"Dibuat dengan sepenuh hati di Indonesia"` dari bottom bar footer

**Em dash dan en dash dihapus/diganti:**

| Lokasi | Teks Lama | Teks Baru |
|---|---|---|
| Deskripsi brand footer | `kos modern — tagihan otomatis` | `kos modern, tagihan otomatis` |
| Poin sesudah ke-6 | `Portal mandiri — penyewa cek` | `Portal mandiri, penyewa cek dan bayar` |
| CTA WhatsApp | `Senin–Sabtu, 08.00–21.00 WIB` | `Senin sampai Sabtu, 08.00 s.d. 21.00 WIB` |
| Jam operasional footer | `Senin – Sabtu` / `08.00 – 21.00 WIB` | `Senin s.d. Sabtu` / `08.00 s.d. 21.00 WIB` |

**Ditambahkan:**

- CSS class `.footer-link` dengan animasi underline keluar dari tengah ke kiri-kanan saat hover
- Semua link navigasi, akses panel, dan kontak di footer menggunakan class tersebut
- Style: `font-bold text-white` (sebelumnya `text-white/60`)

**Diperbesar:**

- Logo NexaSpace di footer: dari `h-14` (3.5rem) menjadi `8.75rem` (2.5x lipat)

**Ditambahkan:**

- Tanda checklist hijau (SVG `stroke` warna `#306D29`) pada setiap poin di sisi "Sesudah NexaSpace"

**Diperbaiki:**

- Bottom bar copyright: layout `flex justify-between` diganti `text-center`, teks menjadi `font-bold text-white`

---

### 15:45 WITA — Penghapusan Em Dash Section Fitur & Penyembunyian Link Admin

**Em dash dihapus dari section `#fitur`:**

| Lokasi | Teks Lama | Teks Baru |
|---|---|---|
| Subjudul section fitur | `kos — dari pencatatan hingga` | `kos, dari pencatatan hingga` |
| Deskripsi Pembayaran Otomatis | `Midtrans Snap — penyewa bayar` | `Midtrans Snap, penyewa bayar` |
| Deskripsi Portal Penyewa Mandiri | `bukti sendiri — 24/7` | `bukti sendiri. Tersedia 24/7` |

**Keamanan — Link admin panel disembunyikan dari publik:**

Halaman `/admin/login` sekarang tidak dapat ditemukan melalui navigasi publik manapun di landing page.

| Lokasi | Perubahan |
|---|---|
| Navbar kanan | Tombol `Login Juragan` dihapus sepenuhnya; hanya tersisa tombol `Portal Anak Kos` |
| Footer kolom "Akses Panel" | Link `Login Juragan` dihapus; hanya tersisa link `Portal Anak Kos` |

**Alasan:** Halaman admin panel bersifat rahasia dan tidak seharusnya terekspos ke pengunjung publik landing page.

---

---

## [Sesi Kerja] — 5 Juni 2026

---

### 23:30 WITA — Perbaikan Nomor WA & Hapus Emoji Bermasalah di Pesan WA

**Apa yang Diubah:**

1. Nomor WhatsApp diperbarui di seluruh codebase:

| File | Sebelum | Sesudah |
|---|---|---|
| `resources/views/landing.blade.php` — 4 URL wa.me | `6285651384990` | `6285249678700` |
| `resources/views/landing.blade.php` — teks footer | `+62 856-5138-4991` | `+62 852-4967-8700` |
| `app/Http/Controllers/InquiryController.php` — wa.me URL | `6285651384990` | `6285249678700` |

2. Emoji `👋` dihapus dari string pesan WhatsApp di `app/Http/Controllers/InquiryController.php` baris 39.

**Alasan Perubahan:** Nomor WA bisnis berganti ke `085249678700`. Emoji `👋` menyebabkan karakter tanda tanya aneh (`?`) saat pesan dikirim melalui URL `wa.me` karena encoding multibyte karakter tidak selalu ditangani dengan benar oleh semua perangkat penerima.

**Hasil Akhir:** Semua link WhatsApp (hero, tombol sticky, section perbandingan, footer) mengarah ke nomor baru. Pesan otomatis ke WA kini bersih tanpa karakter aneh.

---

### 23:00 WITA — Perbaikan Nomor WhatsApp

**Apa yang Diubah:**

Nomor WhatsApp di seluruh codebase diperbarui.

| File | Sebelum | Sesudah |
|---|---|---|
| `resources/views/landing.blade.php` (4 lokasi) | `6285651384991` | `6285651384990` |
| `app/Http/Controllers/InquiryController.php` (1 lokasi) | `6285651384991` | `6285651384990` |

**Alasan Perubahan:** Koreksi digit terakhir nomor WA dari `91` menjadi `90` sesuai permintaan.

**Hasil Akhir:** Semua link WhatsApp di hero section, section perbandingan, footer, dan URL redirect inquiry kini mengarah ke nomor yang benar.

---

### 22:30 WITA — Opsi 2 & 3: Sistem Inquiry + Modal Form + Notifikasi WhatsApp

**Apa yang Diubah:**

Menggantikan tombol `mailto:` yang tidak fungsional di section harga landing page dengan sistem inquiry lengkap — form tersimpan ke database, admin melihat lead di panel, dan setelah submit pengguna otomatis diarahkan ke WhatsApp admin dengan pesan pre-filled.

**File yang Dibuat:**

| File | Keterangan |
|---|---|
| `database/migrations/2026_06_05_184221_create_inquiries_table.php` | Tabel `inquiries`: name, phone, room_count, plan (enum: lite/pro/custom), message, status (enum: new/contacted/active/closed) |
| `app/Models/Inquiry.php` | Model Inquiry dengan accessor `plan_label` |
| `app/Http/Controllers/InquiryController.php` | POST handler: validasi, simpan ke DB, kembalikan JSON `{wa_url}` dengan pesan WA pre-filled per paket |
| `app/Filament/Resources/InquiryResource.php` | Admin resource: tabel list inquiry, filter paket & status, bulk action ubah status, nomor WA bisa diklik langsung |
| `app/Filament/Resources/InquiryResource/Pages/ListInquiries.php` | List page untuk resource |

**File yang Diubah:**

| File | Perubahan |
|---|---|
| `routes/web.php` | Tambah `POST /inquiry` → `InquiryController@store` |
| `app/Filament/Widgets/StatsOverview.php` | Tambah stat "New Inquiries" (hitung status=new) |
| `resources/views/landing.blade.php` | Tiga tombol `<a href="mailto:...">` diganti `<button onclick="openInquiryModal(plan)">` + tambah section H (modal HTML + vanilla JS) sebelum `</body>` |

**Alur Kerja Baru:**

1. Pengunjung klik "Pilih LITE / Mulai Paket PRO / Hubungi Layanan"
2. Modal terbuka dengan nama paket sudah terisi otomatis di header
3. Pengunjung isi: Nama, Nomor WA, Jumlah Kamar, Pesan (opsional)
4. Submit via `fetch()` ke `POST /inquiry` (AJAX, tidak reload halaman)
5. Backend validasi, simpan ke DB `inquiries` dengan status `new`
6. Backend kembalikan JSON `{wa_url}` berisi link WA dengan pesan pre-filled:
   - Nama paket + harga
   - Nama, nomor WA, jumlah kamar, pesan
7. Modal tampilkan animasi sukses (1.5 detik)
8. Browser buka WA admin di tab baru → admin langsung menerima chat lengkap
9. Admin panel dashboard menampilkan counter "New Inquiries"
10. Admin buka `/admin/inquiries`, lihat semua lead, ubah status via bulk action

**Validasi yang Diterapkan:**

| Field | Aturan |
|---|---|
| name | required, string, max 255 |
| phone | required, string, max 20 |
| room_count | required, integer, min 1, max 999 |
| plan | required, in: lite/pro/custom |
| message | nullable, string, max 1000 |

Error validasi 422 ditampilkan inline di bawah field terkait (tanpa reload halaman).

**Alasan Perubahan:**

Sebelumnya tidak ada cara bagi calon pelanggan untuk menunjukkan minat kecuali membuka email client (yang seringkali tidak otomatis terbuka di mobile). Dengan sistem ini, setiap lead tersimpan di database untuk tracking, dan admin langsung mendapat notifikasi via WhatsApp tanpa harus membuka panel secara aktif.

**Hasil Akhir:**

Tiga tombol CTA di section harga kini fungsional sepenuhnya. Data lead tersimpan di tabel `inquiries` dan dapat dikelola di `/admin/inquiries`. Admin mendapat pesan WA otomatis dari setiap calon pelanggan. Semua 42 automated test tetap hijau setelah perubahan ini.

---

### 21:30 WITA — Opsi B: Test Coverage Lengkap (42 Test Cases)

**Apa yang Diubah:**

Penambahan test suite penuh untuk seluruh alur bisnis kritis NexaSpace. Seluruh test berjalan di atas database MySQL `nexaspace_testing` (terpisah dari database development).

**File yang Dibuat:**

| File | Jumlah Test | Cakupan |
|---|---|---|
| `tests/Unit/BillingServiceTest.php` | 11 test | Grace period boundaries, throttle DB vs MikroTik, restore logic, device isolation |
| `tests/Feature/PanelAccessTest.php` | 8 test | Role-based access admin/tenant panel, redirect vs 403 |
| `tests/Feature/MidtransWebhookTest.php` | 9 test | Signature SHA512, status settlement/capture/pending/challenge, idempotency |
| `tests/Feature/BillingObserverTest.php` | 6 test | Dispatch RestoreDevicesJob, device throttled vs blocked vs active |
| `tests/Feature/TenantBillingIsolationTest.php` | 5 test | Query scoping tenant, canCreate false, admin unrestricted |

**File yang Diubah:**

| File | Perubahan |
|---|---|
| `phpunit.xml` | Switch dari SQLite (tidak tersedia) ke MySQL `nexaspace_testing` |
| `app/Jobs/RestoreDevicesJob.php` | `$billing` diubah dari `private` ke `public readonly` agar bisa diakses dalam Queue assertion test |

**Skenario yang Diuji:**

*BillingService — Grace Period:*
- Billing due hari ini → **tidak** di-throttle
- Billing due kemarin (H+1) → **tidak** di-throttle
- Billing due 2 hari lalu (H+2) → **tidak** di-throttle (batas terakhir grace)
- Billing due 3 hari lalu (H+3) → **harus** di-throttle
- Billing status `paid` → diabaikan meskipun due_date sudah lewat
- Device `blocked` → tidak disentuh saat throttle berjalan
- MikroTik gagal (return false) → DB tetap ter-update

*BillingService — Restore:*
- Device `throttled` → dikembalikan ke `active` + MikroTik unthrottle
- Device `blocked` → tidak disentuh saat restore
- Device `active` → tidak dipanggil ke MikroTik (tidak perlu restore)
- Restore hanya menyentuh device milik tenant dari billing tersebut (isolation)

*Panel Access:*
- Admin → `/admin` ✓, `/tenant` ✗ (403)
- Tenant → `/tenant` ✓, `/admin` ✗ (403)
- Guest → keduanya redirect ke login masing-masing

*Midtrans Webhook:*
- Signature valid → 200
- Signature salah → 403
- Payload di-tamper (ubah gross_amount setelah signing) → 403
- `settlement` → billing paid
- `capture` + `fraud_status=accept` → billing paid
- `capture` + `fraud_status=challenge` → billing **tidak** paid
- `pending` → billing **tidak** berubah
- Double webhook (idempotency) → `RestoreDevicesJob` hanya dispatch sekali
- Billing ID tidak dikenal → 200 (graceful)
- Order ID format salah → 200 (graceful)

*BillingObserver:*
- Status → `paid` → dispatch `RestoreDevicesJob`
- Status → `throttled` → **tidak** dispatch job
- Update field non-status → **tidak** dispatch job
- End-to-end: device throttled → active setelah billing paid
- Device `blocked` → tidak berubah setelah billing paid
- Hanya device milik tenant yang bersangkutan yang dipulihkan

*Tenant Billing Isolation:*
- Tenant A tidak bisa melihat billing Tenant B via query scoping
- `/tenant/billings/create` → 404 (canCreate false)
- Admin melihat semua billing tanpa batasan
- Widget stats hanya menghitung billing milik tenant yang login

**Alasan Perubahan:**

Test suite dibutuhkan untuk memverifikasi bahwa semua logika bisnis kritis berjalan sesuai dengan yang diharapkan. Tanpa test, perubahan di masa depan bisa secara tidak sengaja merusak behavior inti seperti grace period, query isolation, atau keamanan webhook Midtrans.

**Hasil Akhir:**

`php artisan test` menghasilkan **42/42 passed, 0 failures, 0 errors**. Semua alur kritis project terlindungi oleh automated test dan bisa dijalankan kapan saja untuk verifikasi regression.

---

### 20:30 WITA — Opsi A: Perbaikan 3 Isu Kritis Production

#### A1 — BillingObserver Dimigrasikan ke Queue Job (Async MikroTik)

**Apa yang Diubah:**

- **Dibuat:** `app/Jobs/RestoreDevicesJob.php` — Job baru yang menerima model `Billing` dan memanggil `BillingService::restoreDevicesForBilling()` dari dalam queue worker. Job dikonfigurasi dengan `$tries = 3` dan `$backoff = 30` detik.
- **Diubah:** `app/Observers/BillingObserver.php` — Constructor injection `BillingService` dihapus. Method `updated()` sekarang hanya memanggil `RestoreDevicesJob::dispatch($billing)` dan langsung return, tanpa menunggu eksekusi MikroTik selesai.

| File | Sebelum | Sesudah |
|---|---|---|
| `BillingObserver.php` | Inject `BillingService`, panggil `restoreDevicesForBilling()` langsung (synchronous) | Dispatch `RestoreDevicesJob` ke queue (asynchronous), tidak ada dependency injection |
| `RestoreDevicesJob.php` | *(belum ada)* | Job baru: `$tries=3`, `$backoff=30s`, handle via `BillingService` |

**Alasan Perubahan:**

Sebelumnya, saat Midtrans mengirim webhook `POST /webhook/midtrans`, server harus menunggu seluruh koneksi MikroTik selesai sebelum bisa mengembalikan respons HTTP. Jika router lambat atau down, Midtrans akan menganggap webhook gagal dan mengirim ulang berulang kali. Dengan memindahkan eksekusi MikroTik ke queue, webhook langsung merespons `200 OK` dan pemulihan perangkat berjalan di latar belakang.

**Hasil Akhir:**

Webhook Midtrans kini selalu merespons cepat tanpa tergantung kondisi jaringan MikroTik. Pemulihan perangkat tetap berjalan melalui queue worker, dengan 3 kali retry jika router sementara tidak dapat dijangkau.

---

#### A2 — Timezone Diubah ke Asia/Makassar (WITA)

**Apa yang Diubah:**

- **Diubah:** `config/app.php` baris `timezone`

| File | Sebelum | Sesudah |
|---|---|---|
| `config/app.php` | `'timezone' => 'UTC'` | `'timezone' => 'Asia/Makassar'` |

**Alasan Perubahan:**

Scheduler `ThrottleOverdueTenantsJob` dijadwalkan jam `01:00`. Dengan timezone UTC, jam `01:00` = `09:00 WITA` — artinya penyewa bisa diblokir WiFi-nya di tengah jam kuliah atau kerja. Dengan `Asia/Makassar`, jadwal `01:00` benar-benar berjalan jam 01:00 dini hari WITA.

**Hasil Akhir:**

Scheduler kini berjalan pada waktu yang benar sesuai zona waktu operasional bisnis (WITA). Semua timestamp Laravel (Carbon, `created_at`, `updated_at`, log) juga menggunakan WITA.

---

#### A3 — Chunking pada Query Throttle + Jeda Antar Panggilan MikroTik

**Apa yang Diubah:**

- **Diubah:** `app/Services/BillingService.php`, method `checkAndThrottleOverdue()`

| Aspek | Sebelum | Sesudah |
|---|---|---|
| Cara ambil data | `.with(...)->get()->each(...)` — semua dimuat ke memori sekaligus | `.with(...)->chunk(50, ...)` — diproses 50 billing per batch |
| Jeda antar MikroTik | Tidak ada | `usleep(150_000)` = 150ms setelah setiap panggilan MikroTik |

**Alasan Perubahan:**

Dengan `.get()`, seluruh billing overdue dimuat ke RAM sekaligus. Di shared hosting Hostinger, ini memicu *Resource Limit Reached* (503) saat jumlah penyewa besar. Dengan `.chunk(50)`, memori selalu konstan karena hanya 50 billing yang ada di RAM pada satu waktu. Jeda 150ms juga mencegah router MikroTik dibombardir request berturut-turut tanpa henti.

**Hasil Akhir:**

`checkAndThrottleOverdue()` kini aman dijalankan di shared hosting meskipun jumlah penyewa terus bertambah. Penggunaan memori tetap konstan (O(1)) dan router MikroTik tidak kelebihan beban.

---

### 20:00 WITA — Pembaruan CLAUDE.md: Instruksi AI Agent & Dokumentasi Isu Kritis

**Apa yang Diubah:**

File `CLAUDE.md` diperbarui dengan dua penambahan besar:

**A. Seksi baru "Instruksi Wajib untuk AI Agent"** ditambahkan di bagian paling atas dokumen (sebelum Project Overview), berisi:
- Prosedur onboarding wajib di setiap sesi baru: baca `CLAUDE.md`, baca `CHANGELOG.md`, pelajari seluruh codebase secara aktif, pahami domain bisnis.
- Format baku entri `CHANGELOG.md` yang wajib diikuti: field "Apa yang Diubah", "Alasan Perubahan", "Hasil Akhir", beserta aturan timestamp WITA dan aturan kelengkapan dokumentasi.

**B. Seksi "Known Gaps and Backlog"** diperluas dengan sub-seksi "Isu Kritis — Harus Diselesaikan Sebelum Production" yang mendokumentasikan tiga masalah teknis berisiko tinggi:

1. **BillingObserver Memanggil MikroTik Secara Synchronous** — `BillingObserver` memanggil `BillingService::restoreDevicesForBilling()` secara langsung, memblokir respons webhook Midtrans selama koneksi MikroTik berlangsung. Solusi: dispatch ke Queue Job.
2. **Zona Waktu Scheduler Belum Disesuaikan** — Jadwal `01:00` di server UTC = jam `09:00 WITA`, menyebabkan penyewa diblokir di pagi hari. Solusi: set `timezone = Asia/Makassar` di `config/app.php`.
3. **Tidak Ada Chunking pada Query Throttle** — `BillingService::checkAndThrottleOverdue()` menggunakan `.get()` yang memuat semua data sekaligus, berbahaya di shared hosting dengan ratusan tenant. Solusi: gunakan `.cursor()` atau `.chunk(N)` dan tambahkan delay antar pemanggilan MikroTik.

**Alasan Perubahan:**

- Instruksi AI Agent dibuat agar setiap sesi baru (new chat, new agent, new AI) dapat langsung memahami alur kerja yang benar tanpa perlu penjelasan ulang dari pengguna.
- Format CHANGELOG dibuat baku agar dokumentasi riwayat perubahan selalu konsisten, detail, dan bisa ditelusuri dengan mudah.
- Tiga isu kritis didokumentasikan karena ketiganya berpotensi menyebabkan kegagalan produksi (webhook spam, blokir di jam sibuk, dan 503 di shared hosting) dan harus menjadi prioritas sebelum deployment ke Hostinger.

**Hasil Akhir:**

`CLAUDE.md` sekarang berfungsi sebagai panduan lengkap untuk AI agent: mulai dari prosedur onboarding, format dokumentasi yang wajib diikuti, hingga peringatan teknis kritis yang harus diselesaikan sebelum production. Setiap AI agent atau sesi baru yang membaca `CLAUDE.md` akan langsung memahami konteks, konvensi kerja, dan risiko yang ada.

---

---

## Ringkasan File yang Dimodifikasi (Sesi 1 Juni 2026)

| File | Jumlah Perubahan |
|---|---|
| `resources/views/landing.blade.php` | 12 perubahan terpisah |

## File yang Dibuat (Sesi 1 Juni 2026)

| File | Keterangan |
|---|---|
| `CHANGELOG.md` | File ini |

---

## Ringkasan File yang Dimodifikasi (Sesi 5 Juni 2026 — Lengkap)

| File | Perubahan |
|---|---|
| `CLAUDE.md` | Penambahan instruksi AI agent + tiga isu kritis production |
| `app/Jobs/RestoreDevicesJob.php` | **Dibuat baru** — async job untuk restore device via queue |
| `app/Observers/BillingObserver.php` | Diganti dari synchronous call ke dispatch job |
| `config/app.php` | Timezone UTC → Asia/Makassar |
| `app/Services/BillingService.php` | `.get()` → `.chunk(50)` + jeda 150ms antar MikroTik call |
| `phpunit.xml` | Switch SQLite → MySQL `nexaspace_testing` |
| `tests/Unit/BillingServiceTest.php` | **Dibuat baru** — 11 unit test grace period & restore logic |
| `tests/Feature/PanelAccessTest.php` | **Dibuat baru** — 8 test role-based panel access |
| `tests/Feature/MidtransWebhookTest.php` | **Dibuat baru** — 9 test webhook signature & idempotency |
| `tests/Feature/BillingObserverTest.php` | **Dibuat baru** — 6 test observer dispatch & device isolation |
| `tests/Feature/TenantBillingIsolationTest.php` | **Dibuat baru** — 5 test query scoping tenant |
| `database/migrations/2026_06_05_184221_create_inquiries_table.php` | **Dibuat baru** — tabel inquiries |
| `app/Models/Inquiry.php` | **Dibuat baru** — model Inquiry |
| `app/Http/Controllers/InquiryController.php` | **Dibuat baru** — store + WA redirect |
| `app/Filament/Resources/InquiryResource.php` | **Dibuat baru** — admin resource lead management |
| `app/Filament/Resources/InquiryResource/Pages/ListInquiries.php` | **Dibuat baru** |
| `routes/web.php` | Tambah route POST /inquiry |
| `app/Filament/Widgets/StatsOverview.php` | Tambah stat New Inquiries |
| `resources/views/landing.blade.php` | Tombol mailto → modal form + vanilla JS |
