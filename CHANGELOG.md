# Changelog — NexaSpace

Semua perubahan dicatat secara kronologis.
Zona waktu: **WITA (UTC+8) — Balikpapan, Kalimantan Timur**

---

## [Sesi Kerja] — 7 Juni 2026

---

### 12:48 WITA — Hapus Card QRIS dari Dashboard Anak Kos

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/filament/tenant/widgets/tenant-dashboard-widget.blade.php` | Hapus blok card "Pembayaran via QRIS" beserta seluruh CSS `nxt-qris-*` yang tidak lagi terpakai. |
| `app/Filament/Tenant/Widgets/TenantDashboardWidget.php` | Hapus data `qrisUrl` dan `phone` dari `getViewData()` karena tidak lagi dipakai di dashboard. |

**Alasan Perubahan:**
Opsi pembayaran QRIS sudah tersedia langsung di modal "Bayar" pada `/tenant/billings`, sehingga card QRIS terpisah di dashboard menjadi redundan.

**Hasil Akhir:**
Dashboard anak kos kini lebih ringkas (hero + Informasi Kamar + 4 stat card) tanpa card QRIS. QRIS tetap dapat diakses saat membayar tagihan via modal.

**Validasi:**
- `php artisan view:cache` → berhasil.

---

### 12:42 WITA — Perbaikan Modal Bayar Anak Kos: Kontras Input/Teks + Opsi QRIS

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Tenant/Resources/BillingResource.php` | Modal aksi "Bayar": (1) Select rekening diberi label "Metode Pembayaran". (2) Jika juragan punya `qris_image`, ditambahkan opsi **"QRIS — Scan untuk bayar"** ke dropdown. (3) Select dibuat `->live()` dan ditambah `Placeholder` preview gambar QRIS yang muncul saat opsi QRIS dipilih (gambar bisa diklik untuk buka resolusi penuh). |
| `app/Providers/Filament/TenantPanelProvider.php` | Tambah blok CSS khusus modal (`.fi-modal-window`): input (select, file upload) background putih dengan teks hitam; panel opsi select putih + teks hitam; sedangkan heading, deskripsi, label field, dan helper text di luar input dibuat putih bold. |

**Alasan Perubahan:**
Pada modal pembayaran di `/tenant/billings`, teks di dalam dropdown nyaris tidak terbaca (teks terang di atas latar putih) dan teks di luar input tampil pudar di latar gelap. Selain itu belum ada opsi membayar via QRIS meskipun juragan sudah mengunggah QRIS.

**Hasil Akhir:**
- Input (dropdown rekening + area upload bukti) berlatar putih dengan teks hitam yang jelas terbaca.
- Semua teks di luar input (judul, deskripsi, label, helper) berwarna putih bold.
- Jika juragan sudah mengunggah QRIS, anak kos dapat memilih "QRIS" di dropdown dan langsung melihat gambar QRIS untuk di-scan, lalu tetap mengunggah bukti transfer.

**Validasi:**
- `php artisan test` → 135/135 hijau.
- `php artisan view:cache` → berhasil.

---

### 12:30 WITA — Fix QRIS Anak Kos Tidak Bisa Di-scan (Ukuran & Rasio)

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/filament/tenant/widgets/tenant-dashboard-widget.blade.php` | (1) Gambar QRIS diperbesar dari `11rem` (≈176px) menjadi `17rem` dengan `height: auto`. (2) Dihapus pemaksaan `aspect-ratio: 1/1` + `object-fit: contain` yang membuat poster QRIS portrait ter-letterbox sehingga area QR mengecil — kini gambar mengikuti rasio aslinya. (3) Gambar dibungkus link (`target="_blank"`) + tombol "Perbesar / unduh QR" agar anak kos bisa membuka QRIS resolusi penuh untuk di-scan/disimpan. |

**Alasan Perubahan:**
Anak kos melaporkan QRIS di dashboard `/tenant` tidak bisa di-scan dari aplikasi bank. Penyebabnya gambar ditampilkan terlalu kecil dan dipaksa kotak (1:1) padahal QRIS aslinya berupa poster portrait, sehingga modul QR ter-letterbox dan menyusut.

**Hasil Akhir:**
QRIS tampil lebih besar dan proporsional sesuai gambar asli sehingga mudah di-scan langsung; tersedia juga opsi membuka/mengunduh QRIS resolusi penuh.

**Validasi:**
- `php artisan view:cache` → berhasil.

---

### 12:20 WITA — Jarak Antar Card di Halaman Profil Juragan

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/filament/pages/juragan-profile.blade.php` | Root halaman diberi class `nxt-juraganprofile` dengan layout `flex column` + `gap: 2rem` (plus `1.75rem` antar section dalam satu form), sama seperti `/tenant/edit-profile`. |

**Alasan Perubahan:**
Card di `/admin/juragan-profile-page` (Informasi Kos, form profil & rekening, form password) masih terlalu menempel. Disamakan dengan jarak proporsional pada halaman edit profil anak kos.

**Hasil Akhir:**
Halaman profil juragan kini memiliki jarak antar card yang lega dan konsisten dengan halaman edit profil tenant.

**Validasi:**
- `php artisan view:cache` → berhasil.

---

### 12:15 WITA — Tombol Hapus/Cancel QRIS di Profil Juragan

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Pages/JuraganProfilePage.php` | Tambah import `Storage` dan method `deleteQris()`: menghapus file QRIS dari storage (`disk('public')`), mengosongkan kolom `qris_image` di DB, dan membersihkan preview FileUpload (`infoData['qris_image'] = null`) tanpa mengganggu field lain. Disertai notifikasi sukses. |
| `resources/views/filament/pages/juragan-profile.blade.php` | Tambah tombol "Hapus QRIS" (merah, ikon trash) di baris tombol form profil, muncul hanya saat juragan sudah punya QRIS. Memakai `wire:click="deleteQris"` + `wire:confirm` untuk konfirmasi sebelum menghapus. |

**Alasan Perubahan:**
Sebelumnya juragan tidak punya cara jelas untuk membatalkan/menghapus gambar QRIS yang sudah terupload, sehingga menyulitkan saat ingin mengganti foto QRIS. Tombol khusus ini memberikan aksi "cancel" yang eksplisit.

**Hasil Akhir:**
Di `/admin/juragan-profile-page`, ketika QRIS sudah ada, muncul tombol "Hapus QRIS". Klik → konfirmasi → QRIS dihapus dari storage, DB, portal anak kos, dan invoice; preview ikut kosong sehingga juragan langsung bisa upload gambar QRIS baru.

**Validasi:**
- `php artisan test` → 135/135 hijau.
- `php artisan view:cache` → berhasil.

---

### 12:04 WITA — Jarak Antar Card di Halaman Edit Profil Anak Kos

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/filament/tenant/pages/edit-profile.blade.php` | Wrapper `.nxt-editprofile` dijadikan `flex column` dengan `gap: 2rem` agar jarak antar card (Informasi Kamar, form profil, form password) proporsional dan tidak menempel; tambah `1.75rem` antar section dalam satu form. |

**Alasan Perubahan:**
Card pada halaman edit profil anak kos terlalu menempel sehingga kurang rapi. Pengguna meminta jarak antar card diperbaiki agar tampilan lebih lega dan konsisten.

**Hasil Akhir:**
Halaman `/tenant/edit-profile` kini memiliki jarak antar card yang proporsional — tidak menempel antar Informasi Kamar, form profil, dan form password.

**Validasi:**
- `php artisan view:cache` → berhasil.

---

### 11:56 WITA — Input Hitam & Teks Putih Bold di Halaman Edit Profil Anak Kos

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/filament/tenant/pages/edit-profile.blade.php` | Root halaman diberi class `nxt-editprofile` dan ditambah blok `<style>` scoped: semua input/select/textarea memakai background hitam (`#000`) dengan teks putih bold, semua teks pada section form dipaksa putih bold, label/value/judul card Informasi Kamar dibuat putih penuh & bold (ikon tetap hijau), dan judul halaman dibuat putih bold. |

**Alasan Perubahan:**
Pengguna meminta seluruh input pada `/tenant/edit-profile` berlatar hitam dan semua teks di halaman tersebut berwarna putih bold agar lebih kontras dan konsisten dengan tema gelap.

**Hasil Akhir:**
Halaman edit profil anak kos kini menampilkan field input hitam dengan teks putih bold, dan seluruh teks (judul, label, helper, nilai informasi kamar) berwarna putih bold. Scope dibatasi pada halaman ini via class `nxt-editprofile` sehingga tidak mempengaruhi halaman lain.

**Validasi:**
- `php artisan view:cache` → berhasil.

---

### 11:48 WITA — Redesign Dashboard & Profil Anak Kos: Tema Dark Premium Sama dengan Admin Dev

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Providers/Filament/TenantPanelProvider.php` | (1) Brand diseragamkan: `brandName('NEXASPACE')` + logo `logo-nexa.png`. (2) `maxContentWidth(Width::Full)`. (3) Widget dashboard diganti menjadi hanya `TenantDashboardWidget` (hapus `AccountWidget`). (4) Tambah `renderHook(HEAD_END)` memanggil method baru `darkThemeCss()` yang menyuntik tema dark premium untuk seluruh chrome panel tenant (background radial gelap, sidebar, topbar, section, form, input, tabel, modal, dropdown, scrollbar) — sama persis nuansanya dengan panel admin developer. Script memaksa class `dark`. |
| `app/Filament/Tenant/Widgets/TenantDashboardWidget.php` | Widget baru full-width khusus anak kos (`canView()` = `isTenant`). Mengirim data scoped per anak kos: nama, email, nomor kamar, nama kos, total tagihan belum dibayar, jumlah tagihan belum lunas/lunas, total perangkat (aktif/throttled), status koneksi (normal/dibatasi), dan data QRIS juragan. |
| `resources/views/filament/tenant/widgets/tenant-dashboard-widget.blade.php` | View baru self-contained (style `nxt-*`): hero welcome (nama, kos, tanggal, jam realtime WITA, sign out), card Informasi Kamar (email, nomor kamar, nama kos, status koneksi), 4 stat card (tagihan belum dibayar, belum lunas, lunas, perangkat), dan blok QRIS (muncul jika juragan punya QRIS) dengan instruksi + tombol WhatsApp. Layout dan visual mengikuti dashboard developer; semua anak kos lihat tampilan sama, hanya datanya berbeda. |
| `app/Filament/Tenant/Widgets/TenantStatsWidget.php` | `canView()` → `false` (statistik dipindah ke `TenantDashboardWidget`). |
| `app/Filament/Tenant/Widgets/QrisWidget.php` | `canView()` → `false` (blok QRIS dipindah ke `TenantDashboardWidget`). |
| `resources/views/filament/tenant/pages/edit-profile.blade.php` | Card "Informasi Kamar" dirombak agar identik dengan card "Informasi Platform" milik developer (style `nxp-platform-*`): header berikon, 3 item horizontal berikon (Email Login, Nomor Kamar, Nama Kos) dengan teks putih bold, border/glow gelap, responsif. |

**Alasan Perubahan:**

Dashboard dan halaman profil anak kos sebelumnya masih bertema terang (putih) bawaan Filament, kontras dengan panel admin/developer yang sudah dark premium. Pengguna meminta tampilan anak kos diseragamkan dengan dashboard admin developer — layout diperbaiki, tema gelap, dan kartu informasi kamar dibuat sama persis dengan kartu informasi platform developer. Tampilan dibuat seragam untuk semua anak kos lintas kos; hanya datanya yang menyesuaikan masing-masing akun.

**Hasil Akhir:**

- `/tenant` (dashboard anak kos) kini tampil dark premium: hero welcome + Informasi Kamar + 4 stat card + QRIS, konsisten dengan dashboard developer.
- Seluruh halaman panel tenant (Tagihan Saya, Laporan, Profil Saya) ikut bertema gelap berkat CSS chrome yang disuntik di panel.
- `/tenant/edit-profile` menampilkan card "Informasi Kamar" yang identik gayanya dengan "Informasi Platform" developer.

**Validasi:**
- `php artisan test` → 135/135 hijau.
- `php artisan view:cache` → berhasil.
- Route `filament.tenant.auth.logout` terverifikasi untuk tombol sign out.

---

### 11:30 WITA — Tambah Step "Isi Rekening Bank" di Card Setup Kos Juragan

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Widgets/JuraganOnboardingWidget.php` | (1) `hasPendingSetup()` kini juga mengembalikan `true` jika `bank_accounts` juragan masih kosong — sehingga card "Setup Kos Anda" tetap tampil sampai rekening diisi. (2) `getViewData()` menambah step ke-4 "Rekening bank sudah diisi" (done jika `bank_accounts` tidak kosong, menampilkan jumlah rekening terdaftar). (3) Mengirim `hasBank` dan `profileUrl` (URL `JuraganProfilePage`) ke view. |
| `resources/views/filament/widgets/juragan-onboarding-widget.blade.php` | (1) Grid step diubah dari `repeat(3, ...)` ke `repeat(auto-fit, minmax(12rem, 1fr))` agar 4 step tertata rapi dan responsif. (2) Tambah panel CTA amber "Rekening bank belum diisi" dengan tombol "Buka Profil & Rekening" yang mengarah langsung ke halaman profil — hanya muncul saat rekening belum diisi. |

**Alasan Perubahan:**

Juragan sering lupa mengisi rekening bank, padahal rekening ini wajib agar anak kos bisa memilih tujuan transfer saat membayar tagihan. Sebelumnya checklist onboarding hanya mengingatkan anak kos & tarif, tanpa menyinggung rekening. Step baru ini membuat juragan ingat dan diarahkan langsung ke menu Profil & Rekening.

**Hasil Akhir:**

Card "Setup Kos Anda" di dashboard juragan kini punya 4 langkah (anak kos, tarif, **rekening bank**, MikroTik). Selama rekening belum diisi, card tetap tampil dan menampilkan panel ajakan dengan tombol pintas ke halaman Profil & Rekening. Card otomatis hilang setelah semua langkah — termasuk pengisian rekening — selesai.

**Validasi:**
- `php artisan test` → 135/135 hijau.
- `php artisan view:cache` → berhasil.

---

### 11:18 WITA — Status Pembayaran Pendaftaran Otomatis Lunas Saat Approval

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Jobs/ProvisionTenantJob.php` | Saat provisioning, `registrations.payment_status` kini ikut di-set `paid` bersamaan dengan `status = active` (sebelumnya hanya `status` yang berubah). |
| `tests/Feature/ProvisioningTest.php` | Tambah assertion bahwa `payment_status` menjadi `paid` setelah provisioning. |
| Database lokal | Registrasi aktif (REG-3 "M79") yang masih `unpaid` diperbarui menjadi `paid`. |

**Alasan Perubahan:**

Di `/admin/registrations`, setelah developer klik "Setujui & Buat Akun", kolom Pembayaran masih menampilkan "Belum Bayar". Penyebabnya: `ProvisionTenantJob` hanya mengubah `status` registrasi menjadi `active`, tidak menyentuh `payment_status`. Padahal menyetujui & membuat akun berarti developer sudah memverifikasi pembayaran bulan pertama — sehingga `payment_status` seharusnya otomatis `paid`. Perbaikan diletakkan di `ProvisionTenantJob` agar konsisten untuk action tunggal "Setujui & Buat Akun" maupun bulk action.

**Hasil Akhir:**

Setelah developer menyetujui pendaftaran: kolom Status berubah ke "Aktif" **dan** kolom Pembayaran berubah ke "Lunas" secara bersamaan — konsisten dengan subscription bulan pertama yang juga langsung lunas.

**Validasi:**
- `php artisan test` → 135/135 hijau.

---

### 11:05 WITA — Subscription Bulan Pertama Otomatis Lunas Saat Approval

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Jobs/ProvisionTenantJob.php` | Subscription bulan pertama yang dibuat saat provisioning kini berstatus `paid` (sebelumnya `unpaid`). Saat developer menyetujui registrasi (= memverifikasi pembayaran pendaftaran bulan pertama), subscription bulan itu langsung tercatat lunas. Subscription bulan-bulan berikutnya tetap digenerate `unpaid` oleh `GenerateMonthlySubscriptionsJob` dan harus dibayar juragan. |
| `tests/Feature/ProvisioningTest.php` | Tambah 4 test: PRO membuat subscription bulan pertama `paid` (Rp 499.000), LITE `paid` (Rp 199.000), CUSTOM tidak membuat subscription, dan provisioning idempotent tidak menduplikasi subscription bulan pertama. |
| Database lokal | Subscription #1 milik juragan "M78" (Juni 2026) yang sebelumnya `unpaid` (dibuat saat pengujian dengan kode lama) diperbarui menjadi `paid`. |

**Alasan Perubahan:**

Alur bisnis yang benar: juragan mendaftar → membayar bulan pertama via transfer manual → developer memverifikasi pembayaran lalu menyetujui & membuat akun. Karena pembayaran bulan pertama sudah diverifikasi pada saat approval, maka subscription bulan pertama harus langsung **lunas**, bukan `unpaid`. Sebelumnya subscription dibuat `unpaid` sehingga juragan seolah masih harus membayar lagi untuk bulan pertama yang sudah dibayar.

**Alur lengkap (final):**

1. Juragan mendaftar di landing page → data masuk ke `/admin/registrations` (developer-only).
2. Developer memverifikasi bukti pembayaran bulan pertama.
3. Developer klik "Setujui & Buat Akun" → `ProvisionTenantJob` membuat akun juragan + anak kos **dan** subscription bulan pertama berstatus `paid`.
4. Di `/admin/subscriptions` milik juragan, subscription bulan ini langsung tampil **Lunas** — konsisten dengan tampilan developer.
5. Auto-lunas ini **hanya** untuk bulan pertama. `GenerateMonthlySubscriptionsJob` (tanggal 1) membuat tagihan bulan berikutnya berstatus `unpaid`.
6. Bulan berikutnya, juragan klik "Bayar" + upload bukti → status langsung `paid` (via `SubscriptionResource`), developer tidak perlu approve lagi, hanya melihat bukti transfer.

**Validasi:**
- `php artisan test` → 135/135 hijau.

---

### 10:44 WITA — Tombol Tambah Device ke Table Header + Subscription Otomatis Saat Provisioning

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Resources/DeviceResource.php` | Tambah `headerActions()` pada tabel dengan tombol "Tambah Device" — menggunakan pola yang sama dengan BillingResource agar tombol selalu muncul di toolbar tabel, bukan di page header yang tidak selalu terlihat pada layout dark NexaSpace. |
| `app/Filament/Resources/DeviceResource/Pages/ListDevices.php` | Hapus `CreateAction` dari `getHeaderActions()` (page header) karena sudah dipindah ke table `headerActions`. Method dikosongkan agar tidak ada duplikasi tombol. |
| `app/Jobs/ProvisionTenantJob.php` | Tambah import `Subscription` dan `Carbon`. Setelah juragan baru dibuat di dalam DB transaction, sistem langsung membuat record `Subscription` untuk bulan berjalan jika plan adalah `lite` (Rp 199.000) atau `pro` (Rp 499.000). Idempotent: cek bulan yang sama sebelum membuat, sehingga aman jika `GenerateMonthlySubscriptionsJob` berjalan pada tanggal 1 setelah provisioning. Plan `custom` tidak di-generate otomatis (sesuai pola yang sudah ada). |

**Alasan Perubahan:**

1. **Tombol Tambah Device**: Tombol create device ditempatkan di `getHeaderActions()` (page header). Pada layout dark NexaSpace saat ini, page header area kadang tidak tampil, sehingga tombol tidak terlihat juragan. Pola yang sudah terbukti benar adalah meletakkan create action di `headerActions()` milik tabel itu sendiri — persis seperti yang sudah dilakukan untuk BillingResource.

2. **Subscription otomatis saat provisioning**: `GenerateMonthlySubscriptionsJob` hanya berjalan pada tanggal 1 setiap bulan. Juragan yang baru di-provisioning di pertengahan bulan tidak akan memiliki data subscription apapun di halaman `/admin/subscriptions` hingga tanggal 1 bulan berikutnya — padahal mereka sudah aktif dan seharusnya sudah mendapatkan tagihan bulan ini.

**Hasil Akhir:**

1. Juragan membuka `/admin/devices` → tombol "Tambah Device" muncul di toolbar tabel (konsisten dengan tombol "Buat Billing" di halaman billings). Form sudah lengkap: dropdown anak kos, nama device, MAC address, dan status.

2. Developer menyetujui registrasi juragan baru (paket LITE/PRO) → setelah provisioning selesai, halaman `/admin/subscriptions` juragan tersebut langsung menampilkan 1 record subscription bulan ini dengan status `unpaid`. Juragan bisa langsung klik "Bayar" dan upload bukti transfer.

**Validasi:**
- `php artisan test` → 131/131 hijau.

---

### 11:27 WITA — Juragan Bisa Tambah Device Anak Kos

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Resources/DeviceResource.php` | Tambah `canCreate()` eksplisit untuk role developer dan juragan. Form device dirapikan: dropdown anak kos menampilkan kombinasi kamar, nama, dan email; field `Nama Device`, `MAC Address`, dan `Status Device` diberi label, placeholder, helper text, serta validasi format MAC address. Query pilihan anak kos tetap di-scope untuk juragan agar hanya bisa memilih anak kos miliknya sendiri. |
| `app/Filament/Resources/DeviceResource/Pages/ListDevices.php` | Tombol create diberi label `Tambah Device`, icon plus circle, warna primary, dan visibility eksplisit mengikuti `DeviceResource::canCreate()`. |
| `tests/Feature/JuraganIsolationTest.php` | Tambah test bahwa juragan bisa membuka `/admin/devices/create`, melihat input lengkap device, dan melihat tombol `Tambah Device` di halaman list devices. |

**Alasan Perubahan:**
Juragan perlu bisa mendaftarkan perangkat milik anak kos dari halaman `/admin/devices` tanpa menunggu developer. Permission create sebelumnya belum eksplisit, sehingga tombol/akses create berisiko tidak muncul seperti kasus billing.

**Hasil Akhir:**
Juragan dapat menambahkan device anak kos dari admin panel dengan input lengkap: anak kos, nama device, MAC address, dan status awal. Data isolation tetap terjaga karena juragan hanya dapat memilih anak kos miliknya.

---

### 11:06 WITA — Seeder Dibersihkan, Sisakan Admin Developer NexaSpace

**Apa yang Diubah:**

| File / Data | Perubahan |
|---|---|
| `database/seeders/DatabaseSeeder.php` | Seeder demo juragan, tenant, device, dan billing dihapus. Seeder sekarang hanya `updateOrCreate` akun developer `admin@nexaspace.site` dengan nama `Haikal`, role `developer`, dan password default `password`. |
| Database development `nexaspace` | Data demo dari seeder dibersihkan: `activity_logs`, `billings`, `devices`, `maintenance_tickets`, `subscriptions`, dan `registrations` dikosongkan; semua user selain `admin@nexaspace.site` role `developer` dihapus. |

**Alasan Perubahan:**
Pengguna meminta seluruh data seeder dihapus dan hanya menyisakan admin developer NexaSpace agar database development kembali bersih tanpa juragan/anak kos demo.

**Hasil Akhir:**
Database development hanya memiliki 1 akun: `admin@nexaspace.site` (`developer`). Tabel billing, device, subscription, laporan, dan registrasi kosong.

---

### 11:00 WITA — Pulihkan Akun Login Dev dan Tambah Guard `.env.testing`

**Apa yang Diubah:**

| File / Data | Perubahan |
|---|---|
| Database development `nexaspace` | Menjalankan `php artisan db:seed` untuk memulihkan akun default setelah tabel user kosong. Akun developer kembali tersedia: `admin@nexaspace.site` dengan password `password`; data demo juragan/tenant dari `DatabaseSeeder` juga kembali dibuat. |
| `.env.testing` | File testing environment lokal ditambahkan agar perintah `php artisan ... --env=testing` eksplisit memakai database `nexaspace_testing`, bukan fallback ke database development `nexaspace`. Driver cache/session/queue/mail dibuat ringan untuk testing. |

**Alasan Perubahan:**
Login admin gagal dengan pesan `These credentials do not match our records` karena database development tidak memiliki akun role `developer`/`juragan`. Selain itu, project belum memiliki `.env.testing`, sehingga command artisan dengan `--env=testing` berisiko fallback ke konfigurasi `.env` development.

**Hasil Akhir:**
Login development kembali bisa memakai kredensial default `admin@nexaspace.site / password`. Command testing artisan kini diarahkan ke `nexaspace_testing` untuk mencegah database development ter-reset saat menjalankan migrasi testing.

---

### 10:55 WITA — Fix Tombol Gray Create Another dan Cancel Billing Create

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Providers/Filament/AdminPanelProvider.php` | Perkuat override CSS tombol action form Filament untuk varian gray/non-primary. Selector kini mencakup `.fi-color-gray`, `.fi-btn.bg-white`, `.fi-btn[class*="bg-white"]`, serta fallback custom property `--bg`, `--hover-bg`, `--text`, `--dark-bg`, dan `--dark-text`. |

**Alasan Perubahan:**
Tombol `Create another` dan `Cancel` di `/admin/billings/create` berasal dari default `CreateRecord` Filament dengan `->color('gray')`. Komponen button Filament memberi varian gray background putih (`bg-white`), sehingga override sebelumnya belum cukup kuat untuk mengubah kedua tombol tersebut.

**Hasil Akhir:**
Tombol `Create another` dan `Cancel` di halaman create billing kini ikut menggunakan dark glass tone dengan teks putih bold, bukan background putih.

---

### 10:46 WITA — Tone Tombol Form Create Billing Disamakan

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Providers/Filament/AdminPanelProvider.php` | Tambah override CSS global untuk action button di halaman form admin: `.fi-ac`, `.fi-form-actions`, `form .fi-btn`, dan `.fi-page-header-actions`. Tombol primary seperti submit/create dibuat hijau gelap neon dengan teks putih bold, sedangkan tombol secondary/gray dibuat dark glass agar tidak lagi tampil putih di halaman `/admin/billings/create`. |

**Alasan Perubahan:**
Tombol di halaman create billing masih belum mengikuti tone visual NexaSpace setelah tombol tabel billing diperbaiki. Karena tombol create form berasal dari action button Filament, styling perlu ditambahkan di level form/page action.

**Hasil Akhir:**
Button di `/admin/billings/create` kini senada dengan tombol `Buat Billing`: hijau gelap, teks putih bold, border hijau lembut, dan hover glow konsisten dengan tema admin.

---

### 03:40 WITA — Revert: Hapus CSS "Fixed No-Scroll" yang Merusak Layout Dashboard

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Providers/Filament/AdminPanelProvider.php` | Menghapus seluruh blok CSS `@media (min-width: 1241px)` "FIXED, NON-SCROLLING DEVELOPER DASHBOARD" yang ditambahkan pada 03:36. Sidebar presisi (ikon lebar-tetap, garis aksen aktif) dan penghapusan hint MIKROTIK_HOST **tetap dipertahankan**. |

**Alasan Perubahan:**
Blok CSS no-scroll memaksa `main.fi-main` ke tinggi tetap `calc(100dvh - 5rem)` + `overflow: hidden` dan menjadikan `.nxd-root` flex-column dengan `flex: 1 1 0` per baris. Kombinasi `min-height: 0` + `overflow: hidden` + height-distribution membuat kartu kolaps/terpotong — hanya menyisakan potongan baris stat di bagian atas dengan area kosong besar di bawah. Pengguna meminta hanya sidebar yang disentuh; dashboard dikembalikan ke kondisi semula yang sudah benar.

**Hasil Akhir:**
Dashboard developer kembali ke layout full-width yang benar (hasil perbaikan 03:27): hero + overview, grid stat 4×2, dan MikroTik + chart tampil normal tanpa terpotong. Sidebar tetap dengan label sejajar presisi dan aksen aktif bersih. Hint teknis `.env` tetap tidak muncul.

---

### 03:36 WITA — Sidebar Presisi, Hapus Hint MIKROTIK_HOST, Dashboard Fixed Tanpa Scroll

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Providers/Filament/AdminPanelProvider.php` | (1) **Sidebar lebih presisi**: ikon nav dibuat lebar-tetap (`flex: 0 0 1.4rem`) sehingga semua label sejajar pada satu garis vertikal; tinggi item dirapikan (`min-height: 2.95rem`); aksen item aktif diganti dari `border-left` membulat menjadi **garis aksen vertikal bersih** via pseudo-element `::before` (3px, tinggi 1.5rem, terpusat vertikal, glow hijau). (2) **Dashboard fixed tanpa scroll**: blok CSS baru di `@media (min-width: 1241px)` yang mengunci `main.fi-main:has(.nxd-root)` ke `height: calc(100dvh - 5rem)` + `overflow: hidden`, `body:has(.nxd-root) { overflow: hidden }`, dan menjadikan `.nxd-root` flex-column 3 baris yang membagi tinggi rata. Semua kartu (hero, overview, stat 4×2, MikroTik, chart) memakai `height: 100%` + padding/font berbasis `clamp(...vh...)` agar muat compact tanpa scroll. Scoped via `:has(.nxd-root)` sehingga halaman lain (tabel Users/Billings dll.) tetap bisa scroll normal. |
| `resources/views/filament/widgets/developer-welcome-widget.blade.php` | Menghapus teks hint `Isi MIKROTIK_HOST di file .env` (`<p class="nxd-mk-hint">…</p>`) pada kartu Status Router MikroTik saat router belum dikonfigurasi. |

**Alasan Perubahan:**
Permintaan pengguna: (1) garis/penataan menu sidebar dibuat lebih presisi; (2) menghilangkan instruksi teknis `MIKROTIK_HOST`/`.env` dari dashboard agar tampilan lebih bersih untuk pengguna akhir; (3) dashboard tidak perlu di-scroll — seluruh konten dimuat compact dalam satu layar.

**Perbandingan Sebelum → Sesudah:**

| Aspek | Sebelum | Sesudah |
|---|---|---|
| Alignment label sidebar | Ikon lebar variabel → label bisa sedikit bergeser | Ikon lebar-tetap 1.4rem → semua label sejajar presisi |
| Aksen item aktif | `border-left` mengikuti sudut membulat | Garis aksen vertikal 3px terpusat, bersih + glow |
| Hint MikroTik | "Isi MIKROTIK_HOST di file .env" tampil | Dihapus (hanya badge status) |
| Tinggi dashboard | Melebihi viewport → perlu scroll | Dikunci `100dvh − 5rem`, fit compact tanpa scroll |

**Hasil Akhir:**
Sidebar tampak lebih rapi dan sejajar dengan aksen aktif yang bersih. Dashboard developer kini memuat seluruh konten dalam satu layar penuh tanpa scroll (di layar ≥1241px), dengan tinggi tiap baris dibagi otomatis dan ukuran elemen menyesuaikan tinggi viewport. Hint teknis `.env` tidak lagi muncul. Halaman lain tetap scroll normal.

---

### 03:27 WITA — Perbaikan Akar Masalah: Dashboard Developer Hanya Setengah Layar + Lingkaran Profil Tidak Presisi

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/filament/widgets/developer-welcome-widget.blade.php` | Membungkus seluruh isi widget dengan komponen resmi `<x-filament-widgets::widget class="nxd-widget">` (sebelumnya root-nya hanya `<div class="nxd-root">` polos). `.nxd-root` kini berada di dalam wrapper tersebut. |
| `app/Providers/Filament/AdminPanelProvider.php` | (1) Mengganti blok CSS mati `.fi-wi { display:grid; grid-template-columns: repeat(12,...) }` + `.fi-wi > .fi-wi-widget` — yang menargetkan struktur DOM yang **tidak ada** di Filament 5.6 — dengan rule yang menargetkan grid schema sebenarnya: `main.fi-main .fi-sc` dibuat full-width dan widget developer (`.nxd-widget` / `.fi-wi-widget:has(.nxd-root)`) dipaksa `grid-column: 1 / -1`. (2) Memperbaiki lingkaran avatar profil: `.fi-user-menu-trigger` dijadikan lingkaran sempurna (`flex: 0 0 2.875rem`, `aspect-ratio: 1/1`, `min-width/min-height`, flex-center, `overflow: hidden`) dan avatar di dalamnya dipaksa mengisi penuh (`width/height: 100%`, `border-radius: 999px`, `object-fit: cover`). |

**Alasan Perubahan:**
Dashboard developer tampil hanya menempati ~setengah kiri layar dengan sisi kanan kosong melompong. Akar masalahnya bersifat **struktural**, bukan jumlah kartu:

- Filament 5.6 me-render widget dashboard di dalam **schema grid 2 kolom** (`.fi-sc.fi-grid`, dari `Dashboard::getColumns() = 2`). Setiap widget di-`liberatedFromContainerGrid()`, sehingga **root view widget itu sendiri** yang harus membawa informasi column-span.
- Widget Filament normal otomatis dibungkus `<x-filament-widgets::widget>`, yang menambahkan class `.fi-wi-widget` + memanggil `gridColumn($this->getColumnSpan())`. Karena `columnSpan = 'full'`, ini menghasilkan `--col-span-lg: 1 / -1` sehingga widget membentang penuh.
- Blade `developer-welcome-widget` **tidak** memakai wrapper itu — root-nya `<div class="nxd-root">` polos — sehingga `columnSpan = 'full'` tidak pernah diterapkan. `.nxd-root` jadi grid-item biasa yang hanya mengisi **1 dari 2 kolom** (~790px dari ~1600px area konten) → tepat setengah layar, rata kiri.
- CSS override lama menargetkan `.fi-wi` (kontainer widget lawas/`@deprecated`) yang **sudah tidak ada** di DOM dashboard Filament 5.6, sehingga override itu mati total dan tidak pernah memperbaiki apa pun.

Selain itu, lingkaran avatar profil di topbar tergencet menjadi oval karena `.fi-user-menu-trigger` berada di flex container tanpa `flex-shrink: 0`/`aspect-ratio`, dan avatar di dalamnya tidak mengisi ring sepenuhnya.

**Perbandingan Sebelum → Sesudah:**

| Aspek | Sebelum | Sesudah |
|---|---|---|
| Lebar dashboard | ~50% (1 dari 2 kolom grid), rata kiri, kanan kosong | Penuh — widget membentang `grid-column: 1 / -1`, konten terpusat maks 90rem |
| Penerapan `columnSpan='full'` | Tidak pernah diterapkan (tidak ada wrapper widget) | Diterapkan via `<x-filament-widgets::widget>` resmi |
| Target CSS grid | `.fi-wi` (tidak ada di DOM → mati) | `.fi-sc` + `.nxd-widget` (struktur schema Filament 5.6 yang nyata) |
| Avatar profil | Oval/tidak presisi, isi tidak mengisi ring | Lingkaran sempurna, avatar mengisi penuh ring hijau |

**Hasil Akhir:**
Dashboard developer kini membentang penuh selebar area konten (tidak lagi setengah layar), dengan grid stat 4×2 dan baris hero/overview serta MikroTik/chart tersusun rapi dan proporsional. Lingkaran avatar profil di pojok kanan atas kini bulat presisi. Seluruh 125 test tetap hijau.

---

### 03:14 WITA — Perbaikan Layout Dashboard Developer: Tambah Stat Card ke-8 (Subscription Aktif)

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Widgets/DeveloperWelcomeWidget.php` | Tambah `use App\Models\Subscription;`, query `$subscriptionAktif` (jumlah subscription berstatus `paid` bulan ini), dan key `subscriptionAktif` ke return array `getViewData()`. |
| `resources/views/filament/widgets/developer-welcome-widget.blade.php` | Tambah blok HTML stat card ke-8 "Subscription Aktif" di dalam `.nxd-stats` setelah card "Failed Jobs". Card menampilkan jumlah juragan yang lunas subscription bulan ini; berwarna kuning peringatan (`nxd-stat-warn`) jika nilainya 0. |

**Alasan Perubahan:**
Grid `.nxd-stats` menggunakan `grid-template-columns: repeat(4, minmax(0, 1fr))` (4 kolom). Dengan hanya 7 stat card, baris kedua hanya terisi 3 dari 4 kolom — menyisakan satu sel kosong di pojok kanan bawah. Ini membuat layout terlihat "hancur" dan tidak simetris. Menambahkan card ke-8 mengisi grid menjadi 4+4 sehingga layout kembali rapi.

**Perbandingan Sebelum → Sesudah:**

| Aspek | Sebelum | Sesudah |
|---|---|---|
| Jumlah stat card | 7 (4 baris 1 + 3 baris 2 = 1 sel kosong) | 8 (4+4, grid penuh simetris) |
| Card "Subscription Aktif" | Tidak ada di blade (data PHP hilang) | Muncul sebagai card ke-8; kuning jika 0, normal jika ≥1 |
| Layout `.nxd-stats` | Baris kedua bolong di kanan | Dua baris penuh 4 kolom |

**Hasil Akhir:**
Dashboard developer kembali rapi: grid stat 4×2 terisi sempurna. Card "Subscription Aktif" menampilkan jumlah juragan yang sudah lunas subscription NexaSpace bulan ini, dengan peringatan warna kuning jika belum ada yang bayar.

---

### 10:43 WITA — Styling Tombol Header Tabel Billing

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Providers/Filament/AdminPanelProvider.php` | Tambah override CSS untuk action button di header/toolbar tabel Filament. Tombol seperti `Buat Billing` kini memakai background dark-green gradient, border hijau, teks putih bold, hover glow, dan input/search/select di toolbar tabel dipaksa background dark agar tidak muncul kotak putih di tema gelap. |

**Alasan Perubahan:**
Pengguna menunjukkan tombol create billing sudah muncul tetapi warnanya masih default/pucat dan beberapa elemen toolbar terlihat putih sehingga kurang menyatu dengan tema admin gelap.

**Hasil Akhir:**
Button create di header tabel billing kini lebih kontras dan senada dengan tema NexaSpace.

---

### 10:41 WITA — Tombol Buat Billing Dipindah ke Header Tabel

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Resources/BillingResource.php` | Tombol `Buat Billing` ditambahkan sebagai `headerActions()` pada tabel billing, dengan URL langsung ke route create resource. Ini memastikan tombol muncul di area table toolbar/header yang terlihat pada layout admin saat ini. |
| `app/Filament/Resources/BillingResource/Pages/ListBillings.php` | `CreateAction` di page header dihapus agar tidak bergantung pada header halaman yang sedang tidak tampil pada layout table dan tidak berisiko duplikat jika header halaman aktif lagi. |
| `tests/Feature/JuraganIsolationTest.php` | Test create billing juragan diperkuat: selain route `/admin/billings/create` bisa diakses, halaman `/admin/billings` juga harus menampilkan teks `Buat Billing`. |

**Alasan Perubahan:**
Pengguna melaporkan tombol create billing masih tidak ada. Permission dan route create sudah benar, tetapi action sebelumnya ditempatkan di page header, sementara layout yang tampil hanya menunjukkan toolbar/header tabel.

**Hasil Akhir:**
Tombol `Buat Billing` sekarang muncul langsung di header tabel billing untuk juragan dan developer.

---

### 10:33 WITA — Perbaikan Setup Kos Dashboard dan Tombol Buat Billing Juragan

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/filament/widgets/juragan-onboarding-widget.blade.php` | Widget `Setup Kos Anda` di dashboard juragan dirombak menjadi card dark compact. Header kini memiliki progress pill dan progress bar, 3 checklist setup ditampilkan sebagai grid card, icon diganti inline SVG ukuran eksplisit, serta daftar kamar tanpa tarif dipindahkan ke panel scrollable/grid chip dengan tombol `Buka Anak Kos`. Daftar 40 kamar tidak lagi memanjang sebagai teks satu baris. |
| `app/Filament/Resources/BillingResource.php` | Tambah `canCreate()` eksplisit untuk developer dan juragan agar izin membuat billing tidak bergantung pada default authorization Filament. |
| `app/Filament/Resources/BillingResource/Pages/ListBillings.php` | Tombol create billing diberi label jelas `Buat Billing`, icon plus circle, warna primary, dan visibility eksplisit mengikuti `BillingResource::canCreate()`. |
| `tests/Feature/JuraganIsolationTest.php` | Tambah test bahwa juragan bisa membuat billing dari admin panel: `BillingResource::canCreate()` bernilai true dan `/admin/billings/create` bisa diakses. |

**Alasan Perubahan:**
Pengguna melaporkan informasi setup kos di dashboard juragan berantakan karena daftar kamar tanpa tarif tampil panjang, serta tombol membuat billing tidak muncul untuk juragan meskipun logika billing sudah ada.

**Hasil Akhir:**
Widget setup kos juragan kini rapi, compact, dan actionable. Juragan juga mendapat akses create billing yang eksplisit melalui tombol `Buat Billing` di halaman `/admin/billings`.

---

### 10:30 WITA — Redesign Informasi Kos Juragan Menyamai Developer Profile

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/filament/pages/juragan-profile.blade.php` | Bagian read-only `Informasi Kos` pada `/admin/juragan-profile-page` diubah dari tabel dua kolom menjadi card horizontal bergaya sama dengan `Informasi Platform` developer. Card memiliki header berikon, grid data berikon, teks putih bold, border/glow gelap, dan responsive layout 4 kolom desktop, 2 kolom tablet, 1 kolom mobile. Data yang ditampilkan: `Email Login`, `Nama Kos`, `Paket`, dan `Kuota Kamar`. |

**Alasan Perubahan:**
Pengguna meminta informasi platform/kos milik juragan disamakan desain layout-nya dengan admin developer agar halaman profil juragan konsisten secara visual.

**Hasil Akhir:**
Halaman Juragan Profile sekarang memiliki card `Informasi Kos` yang lebih rapi, horizontal, dan senada dengan card informasi platform developer.

---

### 10:28 WITA — Redesign Dashboard Juragan Mengikuti Layout Developer

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Widgets/JuraganDashboardWidget.php` | Tambah widget dashboard juragan baru full-width dengan data yang discoped ke juragan login: nama kos, paket, kuota kamar, anak kos, perangkat aktif/throttled, tagihan belum lunas/throttled, pendapatan bulan ini, laporan kerusakan aktif/selesai, status langganan NexaSpace, tagihan jatuh tempo hari ini, status MikroTik, dan chart pendapatan 6 bulan. |
| `resources/views/filament/widgets/juragan-dashboard-widget.blade.php` | Tambah layout dashboard juragan memakai struktur dan visual `nxd-*` yang sama dengan dashboard developer: hero welcome, overview card, grid 8 stat cards, status router MikroTik, dan chart pendapatan. Semua icon dibuat inline SVG dengan ukuran eksplisit agar tidak membesar liar. |
| `app/Providers/Filament/AdminPanelProvider.php` | Daftarkan `JuraganDashboardWidget` di daftar widget admin panel setelah `DeveloperWelcomeWidget`, sehingga juragan mendapat dashboard baru di `/admin`. |
| `app/Filament/Widgets/StatsOverview.php` | Widget stat lama juragan disembunyikan karena data sudah dipindahkan ke dashboard juragan baru agar tidak dobel. |
| `app/Filament/Widgets/JuraganQuotaWidget.php` | Widget kuota lama disembunyikan karena kuota sudah masuk ke stat dan overview dashboard baru; ini juga menghindari masalah icon Filament yang sebelumnya bisa tampil terlalu besar. |
| `app/Filament/Widgets/RevenueChartWidget.php` | Widget chart lama disembunyikan karena chart pendapatan sudah ditanam di dashboard juragan baru dengan desain yang sama seperti developer. |
| `app/Filament/Widgets/BillingReminderWidget.php` | Widget reminder lama disembunyikan dari dashboard; ringkasannya diganti menjadi indikator `Tagihan Hari Ini` di overview dashboard juragan. |

**Alasan Perubahan:**
Pengguna meminta dashboard milik juragan ditata ulang agar desainnya sama seperti dashboard admin developer, tetapi isi informasinya disesuaikan untuk kebutuhan juragan. Tampilan lama berupa kumpulan widget terpisah dan salah satu icon tampil sangat besar karena style komponen icon lama tidak stabil.

**Hasil Akhir:**
Dashboard juragan sekarang tampil seragam dengan dashboard developer: dark-green premium layout, hero welcome, overview ringkas, stat cards, MikroTik status, dan revenue chart dalam satu widget terpadu. Informasi yang tampil berfokus pada operasional kos juragan, bukan metrik platform developer.

**Validasi:**

- `php artisan view:cache` → berhasil.
- `php artisan test --filter=PanelAccessTest` → 13 test lulus.

---

### 10:16 WITA — Checkbox Tabel Putih dan Platform Card Horizontal

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Providers/Filament/AdminPanelProvider.php` | Checkbox pada tabel admin (`.fi-ta .fi-checkbox-input` dan `input[type="checkbox"]`) dibuat putih saat belum dipilih agar terlihat di atas background tabel hitam. State checked tetap hijau dengan glow, dan focus state diberi ring putih agar keyboard focus tetap jelas. Override ini berlaku untuk semua tabel di panel admin yang dipakai developer dan juragan. |
| `resources/views/filament/pages/developer-profile.blade.php` | Card `Informasi Platform` diubah dari class utility Tailwind ke CSS scoped `.nxp-platform-*` langsung di Blade. Layout desktop dikunci menjadi horizontal 3 kolom, dengan fallback responsive 1 kolom di layar kecil. Icon, spacing, border, label, dan value dibuat eksplisit agar tidak kembali jatuh vertikal atau kehilangan style. |

**Alasan Perubahan:**
Pengguna meminta checkbox data di setiap menu admin dibuat putih supaya terlihat, dan meminta informasi platform developer ditampilkan horizontal karena tampilan sebelumnya masih jatuh vertikal.

**Hasil Akhir:**
Checkbox tabel admin sekarang lebih kontras. Card `Informasi Platform` di `/admin/developer-profile-page` tersusun horizontal stabil pada desktop.

---

### 10:13 WITA — Compact Card Informasi Platform Developer

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/filament/pages/developer-profile.blade.php` | Ukuran card `Informasi Platform` diperkecil: padding header/body dikurangi, ukuran icon wrapper diturunkan, teks label/value dibuat lebih compact, dan komponen `x-heroicon-*` diganti menjadi inline SVG dengan width/height eksplisit agar icon tidak membesar liar ketika class ukuran tidak diterapkan. |

**Alasan Perubahan:**
Pengguna melihat icon pada card informasi platform tampil terlalu besar sampai memenuhi layar. Penyebabnya adalah ukuran SVG tidak terkunci cukup aman di konteks panel.

**Hasil Akhir:**
Card `Informasi Platform` sekarang lebih compact dan icon tetap pada ukuran kecil yang konsisten.

---

### 10:08 WITA — Redesign Card Informasi Platform Developer

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/filament/pages/developer-profile.blade.php` | Card read-only `Informasi Platform` pada `/admin/developer-profile-page` diubah dari grid teks sederhana menjadi section custom bergaya seperti referensi. Header kini memakai icon server, lalu data `Email Login`, `Role`, dan `Platform` disusun sebagai tiga item horizontal berikon dengan background gelap, ring hijau, pembatas antar kolom, teks putih bold, dan layout responsive mobile. |

**Alasan Perubahan:**
Pengguna meminta data platform developer dibuat lebih rapi mengikuti contoh gambar, karena tampilan sebelumnya terlalu polos dan kurang terstruktur dibanding desain profil/rekening yang diinginkan.

**Hasil Akhir:**
Bagian `Informasi Platform` di halaman Developer Profile sekarang tampil lebih premium dan mudah dipindai: icon + label + nilai untuk setiap data utama platform.

---

### 10:03 WITA — Fitur Laporan Kerusakan Berbasis Tiket

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `database/migrations/2026_06_07_095200_create_maintenance_tickets_table.php` | Tambah tabel `maintenance_tickets` untuk sistem antrian laporan kerusakan. Kolom utama meliputi `tenant_id`, `juragan_id`, `category`, `title`, `description`, `evidence_path`, `status`, `progress_note`, `reviewed_at`, `resolved_at`, dan timestamps. Index ditambahkan untuk query juragan/status serta tenant/tanggal. |
| `app/Models/MaintenanceTicket.php` | Tambah model tiket laporan dengan konstanta status (`open`, `reviewed`, `in_progress`, `resolved`), kategori (`wifi`, `water`, `electricity`, `room_damage`, `other`), helper label status/kategori, cast timestamp review/selesai, dan relasi ke `tenant` serta `juragan`. |
| `app/Models/User.php` | Tambah relasi `maintenanceTickets()` untuk tiket milik anak kos dan `managedMaintenanceTickets()` untuk tiket yang masuk ke juragan kos. |
| `app/Filament/Tenant/Resources/MaintenanceTicketResource.php` | Tambah menu tenant `Laporan`. Anak kos bisa melihat tiket miliknya, memfilter status/kategori, membuka bukti gambar, serta membuat laporan baru dengan jenis gangguan, judul, deskripsi, dan bukti gambar wajib. Query resource dibatasi ke `tenant_id = auth()->id()` agar laporan tenant lain tidak terlihat. |
| `app/Filament/Tenant/Resources/MaintenanceTicketResource/Pages/ListMaintenanceTickets.php` | Tambah halaman daftar laporan tenant dengan tombol `Buat Laporan`. |
| `app/Filament/Tenant/Resources/MaintenanceTicketResource/Pages/CreateMaintenanceTicket.php` | Saat tenant membuat laporan, sistem otomatis mengisi `tenant_id`, `juragan_id`, status awal `open`, dan mengosongkan catatan progress/timestamp. Tenant tanpa juragan valid diblokir. |
| `app/Filament/Resources/MaintenanceTicketResource.php` | Tambah menu admin/juragan `Laporan`. Juragan bisa melihat laporan anak kos miliknya, developer bisa melihat semua laporan. Tabel menampilkan tanggal, anak kos, kamar, kos, kategori, judul, status, dan bukti. Halaman review menampilkan detail laporan read-only, preview/link bukti gambar, serta form status dan catatan progress. Bulk action tersedia untuk menandai `Sedang Dikerjakan` dan `Selesai`. |
| `app/Filament/Resources/MaintenanceTicketResource/Pages/ListMaintenanceTickets.php` | Tambah halaman daftar laporan admin tanpa tombol create, karena laporan dibuat dari akun anak kos. |
| `app/Filament/Resources/MaintenanceTicketResource/Pages/EditMaintenanceTicket.php` | Saat status disimpan, sistem otomatis mengisi `reviewed_at` untuk status yang sudah direview/dikerjakan/selesai dan `resolved_at` ketika status menjadi selesai. Jika status dikembalikan ke `Menunggu Review`, `reviewed_at` dibersihkan; jika status selesai dikembalikan ke status lain, `resolved_at` dibersihkan. |
| `database/factories/MaintenanceTicketFactory.php` | Tambah factory untuk kebutuhan test dan seed data tiket laporan. |
| `tests/Feature/MaintenanceTicketIsolationTest.php` | Tambah test isolasi laporan: tenant hanya melihat tiket sendiri, juragan hanya melihat tiket anak kos miliknya, developer bisa melihat semua, serta route `/tenant/maintenance-tickets`, `/tenant/maintenance-tickets/create`, dan `/admin/maintenance-tickets` bisa diakses role yang benar. |

**Alasan Perubahan:**
Sebelumnya laporan gangguan seperti WiFi mati, air mati, atau kerusakan kamar masih harus lewat WhatsApp. Pengguna meminta sistem pelaporan internal berbasis antrian tiket agar anak kos bisa membuat laporan dengan bukti gambar, lalu juragan dapat melakukan review, memberi progress, menandai sedang dikerjakan, dan menyelesaikan laporan.

**Hasil Akhir:**
NexaSpace sekarang memiliki fitur ticketing laporan kerusakan end-to-end. Anak kos membuat laporan dari panel tenant, laporan otomatis masuk ke akun juragan kos terkait, bukti gambar tervalidasi, status tiket bisa dipantau, dan juragan dapat mengelola progress sampai selesai. Migration sudah dijalankan di database development lokal.

**Validasi:**

- `php artisan test --filter=MaintenanceTicketIsolationTest` → 4 test lulus.
- `php artisan test --filter=PanelAccessTest` → 13 test lulus.
- `php artisan view:cache` → berhasil.
- `php artisan migrate` → migration `maintenance_tickets` berhasil dijalankan.
- `php artisan route:list --path=maintenance-tickets` → 4 route terdaftar (`admin` index/edit, `tenant` index/create).

---

### 09:51 WITA — Informasi Kos Juragan Diubah Menjadi Tabel

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/filament/pages/juragan-profile.blade.php` | Bagian read-only `Informasi Kos` pada halaman `/admin/juragan-profile-page` diubah dari grid/card ringkas menjadi tabel dua kolom. Data `Email Login`, `Nama Kos`, `Paket`, dan `Kuota Kamar` sekarang ditampilkan sebagai baris tabel dengan label di kiri dan nilai di kanan. Fallback nilai kosong disederhanakan menjadi `-`. |

**Alasan Perubahan:**
Pengguna meminta data informasi kos pada card profile juragan tidak lagi tampil sebagai kumpulan item/grid, tetapi dibuat menjadi tabel agar lebih rapi dan mudah dibaca.

**Hasil Akhir:**
Card `Informasi Kos` sekarang menampilkan data juragan dalam bentuk tabel sederhana yang tetap mengikuti tema gelap admin.

---

### 09:45 WITA — Redesign Halaman Login Tenant Portal Anak Kos

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/filament/tenant/pages/login.blade.php` | Rewrite total halaman `/tenant/login` dari layout putih/krem menjadi split dark layout seperti referensi. Sisi kiri menampilkan wordmark NexaSpace, headline besar `Welcome Home`, copy `Kelola tagihan dan pembayaran Anda dengan mudah kapan saja`, aksen garis hijau, titik dekoratif, lingkaran outline, dan wave hijau. Sisi tengah diberi vertical divider hijau dengan badge `N`. Sisi kanan berisi logo NexaSpace, subtitle `Portal Anak Kos`, form login Filament, tombol sign in hijau dengan arrow, separator `atau`, teks bantuan pengelola kos, dan copyright. CSS scoped `.ntl-*` ditambahkan untuk dark background, glow hijau, input glass dark, checkbox hijau, responsive mobile, dan dekorasi visual. |
| `app/Filament/Tenant/Pages/Login.php` | Field email tenant diberi `prefixIcon('heroicon-o-envelope')` agar form login mendekati tampilan referensi yang memakai icon email di dalam input. Flow autentikasi Filament tetap sama. |

**Alasan Perubahan:**
Pengguna meminta halaman `http://127.0.0.1:8000/tenant/login` diubah mengikuti gambar referensi: dark split screen, visual `Welcome Home`, dan form portal anak kos yang lebih modern serta senada dengan redesign admin.

**Hasil Akhir:**
Halaman login tenant kini tampil sebagai portal anak kos dark-green: kiri sebagai welcome panel, kanan sebagai form login NexaSpace, dengan input dan tombol yang sudah mengikuti tema referensi.

---

### 04:07 WITA — Revisi Warna Input Profile dan Tombol Tambah Rekening

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Providers/Filament/AdminPanelProvider.php` | Revisi CSS form admin agar input pada halaman form/profile seperti `/admin/developer-profile-page` memakai abu-abu gelap `#111827`, bukan hitam pekat, sehingga field lebih terlihat di atas panel hitam. Tombol action repeater seperti `+ Tambah Rekening` pada `.fi-fo-repeater-add` juga diberi background dark-green gradient, border hijau, teks putih bold, dan hover glow agar tidak lagi tampil putih. Filter table tetap dipertahankan hitam sesuai request sebelumnya. |

**Alasan Perubahan:**
Pengguna melihat input Developer Profile terlalu menyatu dengan background hitam dan tombol `Tambah Rekening` masih putih, sehingga perlu kontras visual yang lebih jelas tetapi tetap senada dengan tema NexaSpace.

**Hasil Akhir:**
Input Developer Profile kini tampak sebagai field abu-abu gelap yang jelas, sementara tombol tambah rekening sudah mengikuti tema dark green admin panel.

---

### 04:03 WITA — Dark Styling Form Profile Developer dan Filter Admin

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Providers/Filament/AdminPanelProvider.php` | Tambah render hook CSS global untuk form, section, modal, input, textarea, select, dropdown, dan filter Filament admin. Halaman seperti `/admin/developer-profile-page` kini memakai panel/form hitam, teks putih bold, label putih bold, helper text putih redup bold, input hitam, border hijau saat fokus, serta upload/repeater mengikuti tema gelap. Filter table di setiap menu juga dipaksa hitam dengan teks putih bold, termasuk filter dropdown, option select native, dropdown panel, dan state hover/selected. Tombol reset filter tetap merah agar fungsi reset tetap mudah dikenali. |

**Alasan Perubahan:**
Pengguna meminta halaman Developer Profile ikut disesuaikan dengan tema admin terbaru: semua teks menjadi putih bold dan input memakai background hitam. Pengguna juga menunjukkan filter table yang masih memiliki dropdown putih, sehingga styling filter perlu dibuat global untuk semua menu admin.

**Hasil Akhir:**
Developer Profile dan filter di menu admin sekarang konsisten dark mode: background form/input/filter hitam, teks putih bold, dropdown/select gelap, dan fokus input tetap memakai aksen hijau NexaSpace.

---

### 03:51 WITA — Global Dark Table Styling untuk Semua Menu Admin

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Providers/Filament/AdminPanelProvider.php` | Tambah render hook CSS global khusus tabel Filament admin. Selector aktual Filament 5 seperti `.fi-ta`, `.fi-ta-ctn`, `.fi-ta-main`, `.fi-ta-content-ctn`, `.fi-ta-table`, `.fi-ta-row`, `.fi-ta-cell`, `.fi-ta-header-toolbar`, `.fi-ta-search-field`, `.fi-pagination`, dan elemen terkait dipaksa memakai background hitam `#05070b` / `#080b11` dan teks putih. Hover row dibuat hijau transparan, search/filter/pagination/input table dibuat gelap, ikon dibuat putih, badge tetap hijau gelap, dan checkbox mengikuti tema gelap. |

**Alasan Perubahan:**
Pengguna meminta semua tabel pada setiap menu admin memiliki background hitam dan teks putih. Sebelumnya resource table seperti Users masih tampil putih karena beberapa class internal Filament 5 memakai background dan text utility light-mode yang belum tertimpa oleh override dashboard.

**Hasil Akhir:**
Semua tabel admin di menu Users, Devices, Billings, Pendaftaran, Langganan, Log Aktivitas, dan resource Filament lain sekarang konsisten dark mode: container hitam, header hitam, row hitam, teks putih, toolbar/search/pagination gelap, dan hover row hijau transparan.

---

### 03:18 WITA — Fix Warna Label Email dan Password Login Admin

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/filament/admin/pages/login.blade.php` | Selector CSS label form login diperbarui dari class lama `.fi-fo-field-wrp-label` ke class aktual Filament 5: `.fi-fo-field-label`, `.fi-fo-field-label-content`, dan `.fi-fo-field-label-ctn`. Rule yang sebelumnya membuat semua `span` di label menjadi merah dihapus; hanya `.fi-fo-field-label-required-mark` yang tetap merah. |

**Alasan Perubahan:**
Pengguna meminta title `Email address*` dan `Password*` pada halaman `/admin/login` berwarna putih. Selector sebelumnya kurang tepat untuk markup Filament 5 dan terlalu luas karena menargetkan semua `span`, sehingga label tidak konsisten putih.

**Hasil Akhir:**
Label `Email address` dan `Password` sekarang dipaksa putih, sementara tanda wajib `*` tetap merah.

---

### 03:11 WITA — Redesign Halaman Login Admin Menjadi Split Dark Admin Panel

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/filament/admin/pages/login.blade.php` | Rewrite total halaman `/admin/login` dari layout kiri putih + panel gradient hijau menjadi split dark layout seperti referensi. Sisi kiri berisi logo NexaSpace, judul "NexaSpace", subtitle "Admin Panel", form login Filament, dan copyright. Sisi kanan berisi icon boarding house, headline "Smart Boarding House", subtitle "Auto-Throttle WiFi Billing System", serta 4 feature cards: Automated throttling, Real-time MikroTik sync, Manual payments, dan Tenant self-service. CSS scoped `.nxl-*` ditambahkan langsung di view untuk background dark, green glow, field styling, tombol sign in glowing, responsive mobile, decorative circles, dan dotted wave effect. |

**Alasan Perubahan:**
Pengguna meminta halaman login admin mengikuti screenshot referensi yang menampilkan split screen gelap premium: form login di kiri dan product feature showcase di kanan. Teks fitur pembayaran disesuaikan menjadi "Manual payments" karena implementasi saat ini sudah tidak memakai Midtrans dan seluruh pembayaran menggunakan transfer bank manual + bukti bayar.

**Perbandingan Sebelum → Sesudah:**

| Aspek | Sebelum | Sesudah |
|---|---|---|
| Background kiri | Putih polos | Dark glass dengan green glow |
| Form login | Default Filament terang | Field gelap, focus green glow, tombol sign in hijau |
| Brand | Logo + teks di atas form | Logo NexaSpace besar + subtitle Admin Panel seperti referensi |
| Panel kanan | Gradient hijau sederhana | Dark product showcase dengan icon, headline, subtitle, feature cards, circle glow, dotted wave |
| Responsif | Split sederhana | Mobile fallback 1 kolom dengan feature cards stacked |

**Hasil Akhir:**
Halaman `/admin/login` sekarang tampil sebagai login admin premium dark mode yang konsisten dengan dashboard NexaSpace dan referensi visual yang diberikan, tanpa mengubah logic autentikasi Filament.

---

### 02:40 WITA — Redesign Dashboard Developer Mengikuti Referensi Layout Admin Panel

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Providers/Filament/AdminPanelProvider.php` | Tambah render hook `SIDEBAR_LOGO_AFTER`, `TOPBAR_START`, `SIDEBAR_FOOTER`, dan `USER_MENU_AFTER` untuk menampilkan brand "NexaSpace / ADMIN PANEL" di kiri atas, footer sidebar "NexaSpace Admin", serta nama user di topbar. Tambah CSS override baru berbasis prefix `nxa-*` dan `nxd-*` untuk layout admin panel seperti referensi: sidebar 292px, topbar 80px, active menu hijau gelap, dashboard full-width terpusat, hero card besar, system overview card, stat card horizontal, dan layout bawah Router MikroTik + chart. Override juga memperbaiki selector active sidebar Filament 5 dari class lama `.fi-sidebar-item-button` ke class aktual `.fi-sidebar-item-btn`. Setelah screenshot lanjutan menunjukkan dashboard masih sempit, selector container widget aktual Filament 5 (`.fi-wi` dan `.fi-wi-widget`) ditambahkan agar widget developer benar-benar span full-width 12 kolom. |
| `app/Filament/Widgets/DeveloperWelcomeWidget.php` | Query dan data `activeSubs` dihapus karena kartu `Subscription Aktif` tidak lagi ditampilkan pada dashboard developer. Import `Subscription` yang tidak terpakai juga dibersihkan. |
| `resources/views/filament/widgets/developer-welcome-widget.blade.php` | Kartu `Subscription Aktif` dihapus dari grid dashboard developer agar statistik mengikuti referensi layout: 4 kartu di baris pertama dan 3 kartu di baris kedua (Total Juragan, Total Anak Kos, Active Devices, Unpaid Bills, Revenue This Month, Pendaftaran Baru, Failed Jobs). |

**Alasan Perubahan:**
Pengguna meminta tampilan `/admin` development diubah agar mengikuti screenshot referensi NexaSpace Admin Panel: brand sidebar lengkap, active menu hijau, topbar dengan avatar + nama user, hero welcome lebih lebar, system overview di kanan, stat cards proporsional, serta area Router MikroTik dan Pendapatan 6 Bulan Terakhir di baris bawah. Perubahan dilakukan sebagai override di provider agar tetap berada di layer project tanpa mengubah file vendor Filament.

**Perbandingan Sebelum → Sesudah:**

| Aspek | Sebelum | Sesudah |
|---|---|---|
| Brand sidebar | Logo ikon saja / teks brand tidak terlihat jelas | Logo + teks "NexaSpace" + subtitle "ADMIN PANEL" |
| Active menu | Bisa muncul putih karena selector CSS lama tidak cocok dengan Filament 5 | Hijau neon dengan left border dan background gelap transparan |
| Layout dashboard | Konten berkumpul di kiri, area kanan kosong | Konten full-width terpusat dengan lebar maksimum 90rem |
| Hero card | Lebih kecil dan kurang dominan | Hero besar 19.25rem, nama user besar, sign out di kanan bawah |
| Stat cards | 8 kartu dalam 4 kolom | 7 kartu seperti referensi: 4 atas + 3 bawah |
| Bottom widgets | Router/chart lebih kecil | Router MikroTik dan chart dibuat lebih besar dan seimbang |

**Hasil Akhir:**
Dashboard developer sekarang mengikuti komposisi visual referensi: sidebar branded, topbar user lebih informatif, active menu hijau, hero dan overview proporsional, statistik lebih lapang, serta bottom section lebih menyerupai desain admin panel NexaSpace modern.

---

### 02:30 WITA — Fix UI: Full-Width, Dark Mode Global, Brand Logo, Sidebar Hover

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Providers/Filament/AdminPanelProvider.php` | (1) Tambah `->maxContentWidth(Width::Full)` menggunakan `Filament\Support\Enums\Width` agar konten panel mengisi seluruh lebar layar. (2) Update brand: `->brandLogo(asset('images/logo-nexa.png'))`, `->brandName('NEXASPACE')`, `->brandLogoHeight('3rem')`. (3) Injeksi `<script>` synchronous di HEAD_END yang menambahkan class `dark` ke `<html>` sebelum render — mengaktifkan semua Tailwind `dark:*` variants. (4) Tambah CSS override untuk utility class Tailwind light-mode (`bg-white`, `bg-gray-50`, `bg-gray-100`, `text-gray-*`, `border-gray-*`, `divide-gray-*`). (5) Tambah CSS `.fi-brand-name` untuk warna hijau + uppercase. (6) Perbaiki hover sidebar: `rgba(148,163,184,0.12)` (abu-abu semi-transparan terlihat). |

**Alasan Perubahan:**
Empat masalah UI dilaporkan: (1) Welcome card tidak mengisi seluruh lebar layar karena `max-width` Filament — diperbaiki dengan `Width::Full`. (2) Semua halaman (tabel Users, Billings, dll) menampilkan background putih karena `darkMode(false)` membuat Tailwind dark variants tidak aktif — diperbaiki dengan JS injection `dark` class. (3) Brand sidebar masih "N—" dari logo lama — diganti ke `logo-nexa.png` dengan teks "NEXASPACE" hijau. (4) Hover sidebar tidak terlihat — ditingkatkan opacity.

**Hasil Akhir:**
- Seluruh konten panel mengisi penuh lebar layar
- Background hitam konsisten di semua halaman (tabel, form, modal)
- Teks putih/abu otomatis via Tailwind dark variants
- Logo sidebar: gambar "N" hijau + teks "NEXASPACE" hijau uppercase
- Hover menu sidebar terlihat abu-abu semi-transparan

125/125 test hijau.

---

### 01:30 WITA — Redesign Total Dashboard Developer: Layout NXD (nxd-*) + Sidebar Active Fix

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Widgets/DeveloperWelcomeWidget.php` | Rewrite total: satu widget full-width menggantikan seluruh developer dashboard. Mengirim 8 stats, MikroTik status, data SVG chart (pts, line, area, yLabels) pre-computed di PHP dengan cubic bezier smooth path. `canView()` hanya untuk `isDeveloper()`. |
| `resources/views/filament/widgets/developer-welcome-widget.blade.php` | Blade baru dengan prefix class `nxd-*` (nexadash). **ROW 1** (65/35): Welcome card (radial glow, abstract wave SVG, nama besar, Alpine.js realtime clock WITA, sign out) + System Overview card (4 health indicator). **ROW 2**: 8 stat card dalam 4-column grid (Total Juragan, Anak Kos, Active Devices, Unpaid Bills, Revenue, Pendaftaran Baru, Failed Jobs, Subscription Aktif). **ROW 3** (40/60): MikroTik card (SVG router ilustrasi dengan LED animasi) + Revenue Chart (pure SVG cubic bezier, area gradient, grid lines, y-axis labels). |
| `app/Filament/Widgets/StatsOverview.php` | `canView()` → return `isJuragan()` saja (developer stats sudah masuk DeveloperWelcomeWidget). |
| `app/Filament/Widgets/RevenueChartWidget.php` | `canView()` → return `isJuragan()` saja; `columnSpan` diubah ke `'full'`. |
| `app/Filament/Widgets/MikroTikStatusWidget.php` | `canView()` → return `false` (MikroTik status sudah masuk DeveloperWelcomeWidget). |
| `app/Providers/Filament/AdminPanelProvider.php` | CSS `renderHook` diperbarui total: (1) Sidebar: semua button direset ke `background: transparent !important` + `border-left: 2px solid transparent`, active state via `[aria-current]` → green BG + green left border. (2) Layout: `.fi-main` + `.fi-page-header-container` + `.fi-dashboard-widgets-container` → `max-width: none !important` untuk full-width. (3) Hapus semua CSS `nexa-*` lama. (4) Tambah seluruh CSS `nxd-*` komprehensif: nxd-root, nxd-card, nxd-hero-row, nxd-welcome, nxd-glow, nxd-art, nxd-welcome-inner, nxd-welcome-tag, nxd-welcome-name, nxd-welcome-sub, nxd-welcome-foot, nxd-meta, nxd-meta-row, nxd-meta-ico, nxd-signout, nxd-overview, nxd-ov-head, nxd-ov-dot, nxd-ov-title, nxd-ov-sub, nxd-ov-list, nxd-ov-item, nxd-ov-label, nxd-ov-ico, nxd-ov-val, nxd-pulse-dot, nxd-green, nxd-red, nxd-amber, nxd-bold, nxd-stats, nxd-stat (+ warn/info/danger variants), nxd-stat-top, nxd-stat-ico (+ warn/info/danger), nxd-stat-label, nxd-stat-val, nxd-stat-val-sm, nxd-stat-desc, nxd-bottom, nxd-mk, nxd-mk-head, nxd-mk-icon, nxd-mk-title, nxd-mk-body, nxd-mk-badge (+ variants), nxd-mk-dot (+ variants), nxd-mk-hint, nxd-mk-table, nxd-mk-row, nxd-mk-key, nxd-mk-val, nxd-mk-router, nxd-code, nxd-chart, nxd-chart-head, nxd-chart-title, nxd-chart-sub, nxd-chart-body. |

**Alasan Perubahan:**
Dashboard developer dari sesi sebelumnya masih memiliki 10 masalah layout: konten terlalu kecil, sisi kanan terlalu kosong, welcome card terlalu kecil, spacing tidak konsisten, sidebar active item masih menampilkan background putih alih-alih hijau gelap, dan ukuran/proporsi baris ketiga tidak ideal. Redesign total dilakukan dengan satu full-width widget dan CSS `nxd-*` yang dikelola di satu tempat.

**Perbandingan Sebelum → Sesudah:**

| Aspek | Sebelum | Sesudah |
|---|---|---|
| Prefix CSS | `nexa-*` (tidak terstruktur) | `nxd-*` (nexadash, konsisten) |
| Sidebar active item | Background putih (override gagal) | `rgba(34,197,94,0.08)` + `border-left: 2px solid #22c55e` |
| Layout konten | `max-width` terbatas Filament | `max-width: none !important` (full-width) |
| ROW 1 split | 2/3 + 1/3 | 65fr + 35fr (lebih proporsional) |
| ROW 2 stats | 7 cards, tidak terstruktur | 8 cards dalam 4-column grid |
| ROW 3 split | MikroTik + Chart seadanya | 40fr + 60fr |
| SVG chart | — | Pure SVG cubic bezier, area gradient, grid, y-axis labels |
| Welcome card | Min-height 220px | Min-height 240px + padding 1.875rem |

**Hasil Akhir:**
Dashboard developer premium dark mode dengan tiga baris yang seimbang: Hero/Overview (65/35), 8 stat cards (4-kolom), MikroTik/Chart (40/60). Sidebar active item sekarang ditandai dengan latar hijau semi-transparan dan garis kiri hijau neon, tanpa background putih. Konten mengisi seluruh lebar panel.

---

## [Sesi Kerja] — 12 Juni 2026

---

### 12:00 WITA — Redesign Dashboard Admin: Dark Premium Theme + Hero Widget

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Widgets/DeveloperWelcomeWidget.php` | Widget baru khusus developer (sort=-10, columnSpan=full). Mengirim data ke view: nama user, `failedJobs`, `dbHealthy`. `canView()` hanya untuk `isDeveloper()`. |
| `resources/views/filament/widgets/developer-welcome-widget.blade.php` | View berisi 2-kolom grid: (1) Hero Welcome Card — "WELCOME BACK", nama besar, tanggal, jam realtime Alpine.js (WITA), dekorasi SVG gelombang abstrak + cincin konsentrik + partikel, tombol Sign out; (2) System Overview Card — indikator System Status, Server Uptime, Database, Backup Status dengan dot hijau berdenyut. |
| `resources/views/filament/widgets/mikrotik-status-widget.blade.php` | Redesign total: badge status dark glass (dark pill), tabel koneksi bertema gelap, tambah ilustrasi SVG router MikroTik — antena, LED berkedip (animasi `<animate>`), port slot, sinyal WiFi arc (tampil saat terhubung). |
| `app/Providers/Filament/AdminPanelProvider.php` | (1) Hapus `AccountWidget` dan `FilamentInfoWidget` dari dashboard. (2) Tambah `DeveloperWelcomeWidget`. (3) Injeksi CSS ~300 baris via `renderHook(HEAD_END)` mencakup: dark background (#07090D), sidebar (#0B0F14), topbar, glassmorphism card, tabel, form, modal, dropdown, notifikasi, scrollbar, dan semua komponen `nexa-*` untuk widget baru. |
| `app/Filament/Widgets/StatsOverview.php` | Tambah `sort = 5` agar urutan developer dashboard: DeveloperWelcome → Stats → MikroTik → RevenueChart. |
| `app/Filament/Widgets/MikroTikStatusWidget.php` | Tambah `columnSpan = 1` (1/3 lebar dashboard 3-kolom). |
| `app/Filament/Widgets/RevenueChartWidget.php` | Tambah `columnSpan = 2` (2/3 lebar dashboard). Update warna garis chart: `#22c55e` (neon green), area fill `rgba(34,197,94,0.10)`, grid line abu transparan, ticks berwarna abu. |

**Alasan Perubahan:**
Dashboard default Filament terlalu generik. Developer meminta redesign dengan konsep premium dark SaaS: background hitam (#07090D), glassmorphism card, aksen hijau neon (#22c55e), hero welcome card dengan abstract art SVG, dan System Overview card menggantikan AccountWidget/FilamentInfoWidget bawaan Filament.

**Hasil Akhir:**
Dashboard developer menampilkan:
- **Baris 1:** Hero card (2/3) dengan nama besar, jam realtime WITA, seni SVG abstrak, tombol sign out + System Overview card (1/3) dengan 4 indikator kesehatan platform
- **Baris 2:** 7 stat card (Total Juragan, Anak Kos, Active Devices, Unpaid Bills, Revenue, Pendaftaran, Failed Jobs) dengan glass dark style
- **Baris 3:** Status Router MikroTik (1/3, dengan SVG router animasi) + Grafik Pendapatan 6 bulan (2/3, garis hijau neon)
- Seluruh panel: background hitam, sidebar gelap, glassmorphism, scrollbar minimal

125/125 test hijau.

---

## [Sesi Kerja] — 8 Juni 2026

---

### 11:00 WITA — Fix: Teks Modal "Bayar" Langganan Diperbarui ke Alur Auto-Lunas

**Apa yang Diubah:**
`app/Filament/Resources/SubscriptionResource.php` — `modalDescription` aksi "Bayar" diubah dari *"Tim kami akan mengkonfirmasi dalam 1×24 jam"* menjadi *"Status langganan akan langsung berubah menjadi Lunas"* agar konsisten dengan implementasi aktual (auto-lunas setelah upload).

**Alasan Perubahan:**
Teks deskripsi modal menyebutkan proses konfirmasi manual (1×24 jam), padahal implementasi aktual langsung mengubah status ke `paid` saat juragan submit bukti. Informasi yang salah bisa menyebabkan kebingungan pengguna.

**Hasil Akhir:**
Modal pembayaran juragan menampilkan deskripsi yang akurat sesuai alur sebenarnya.

---

### 10:30 WITA — Halaman Profil & Rekening untuk Developer

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Pages/DeveloperProfilePage.php` | Halaman baru khusus developer di admin panel: form profil (nama, HP, email kontak), Repeater rekening bank NexaSpace, upload QRIS, ganti password; hanya tampil untuk `role = developer` (`canAccess` + `shouldRegisterNavigation`); auto-discover via `discoverPages` |
| `resources/views/filament/pages/developer-profile.blade.php` | View halaman: blok read-only info platform (email login, role, nama platform) di atas, form profil + rekening + QRIS, form ganti password |

**Alasan Perubahan:**
Developer tidak memiliki halaman untuk mengisi biodata, rekening bank NexaSpace, dan QRIS. Rekening bank developer penting karena dipakai di modal "Bayar" saat juragan melunasi tagihan langganan bulanan. Sebelumnya developer harus mengedit record user lewat `UserResource` yang tidak user-friendly.

**Hasil Akhir:**
Developer memiliki halaman **"Profil & Rekening NexaSpace"** di navigasi panel admin. Dari sini mereka bisa:
- Mengubah nama, nomor HP/WA, email kontak
- Menambah/mengubah/menghapus rekening bank NexaSpace (yang muncul di modal bayar juragan)
- Upload QRIS statis NexaSpace
- Ganti password
125/125 test hijau.

---

### 10:00 WITA — Fitur Bayar Langganan: Juragan Upload Bukti + Auto-Lunas ke NexaSpace

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `database/migrations/2026_06_07_005201_add_payment_receipt_to_subscriptions_table.php` | Kolom baru `payment_receipt` (string, nullable) pada tabel `subscriptions` |
| `app/Models/Subscription.php` | `payment_receipt` ditambahkan ke `#[Fillable]` |
| `app/Filament/Resources/SubscriptionResource.php` | (1) Aksi **"Bayar"** — muncul untuk juragan saat status `unpaid`/`overdue` dan belum ada bukti; modal berisi dropdown rekening bank developer (NexaSpace) + upload bukti transfer; setelah submit, bukti disimpan ke `payment_receipt` dan status langsung diubah ke `paid` (auto-lunas). `SubscriptionObserver` otomatis menghapus `suspended_at` jika juragan sedang disuspend. (2) Aksi **"Lihat Bukti"** — muncul untuk juragan setelah upload, membuka file di tab baru. (3) Kolom `IconColumn payment_receipt` — ikon hijau jika ada bukti, abu jika belum; dapat diklik developer untuk melihat file. |

**Alasan Perubahan:**
Juragan belum memiliki cara untuk menyampaikan bukti pembayaran langganan NexaSpace langsung dari panel. Alur auto-lunas dipilih agar juragan langsung mendapat akses kembali tanpa menunggu konfirmasi manual developer — developer tetap bisa melihat bukti melalui ikon di kolom tabel.

**Alur lengkap:**
1. Juragan membuka `/admin/subscriptions`
2. Klik tombol **"Bayar"** pada baris subscription `unpaid` atau `overdue`
3. Modal muncul: pilih rekening bank NexaSpace (dari `bank_accounts` developer) + upload bukti transfer (JPG/PNG/WebP/PDF, maks 3MB)
4. Submit → bukti tersimpan, status langsung `paid`, `SubscriptionObserver` menghapus `suspended_at`
5. Tombol "Bayar" berubah menjadi **"Lihat Bukti"** (buka file di tab baru)
6. Developer melihat ikon hijau di kolom Bukti → dapat membuka file untuk verifikasi

**Catatan:** Rekening bank NexaSpace diambil dari field `bank_accounts` milik user developer. Jika belum diisi, hanya field upload yang muncul.

**Hasil Akhir:**
Juragan memiliki alur pembayaran self-service yang jelas di panel admin. Upload bukti → status langsung lunas → akses panel dipulihkan otomatis. 125/125 test hijau.

---

### 09:30 WITA — Bugfix: MySQL ONLY_FULL_GROUP_BY Error pada Filter Bulan Subscriptions

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Resources/SubscriptionResource.php` | Pada query opsi filter bulan: ubah `groupByRaw('DATE_FORMAT(subscription_month, "%Y-%m")')` → `groupByRaw('DATE_FORMAT(..., "%Y-%m-01")')` dan `orderByDesc('subscription_month')` → `orderByRaw('DATE_FORMAT(..., "%Y-%m-01") DESC')` agar ekspresi di SELECT, GROUP BY, dan ORDER BY identik. |

**Alasan Perubahan:**
Membuka halaman `/admin/subscriptions` langsung memunculkan `SQLSTATE[42000]: Syntax error or access violation: 1055 Expression #1 of SELECT list is not in GROUP BY clause`. MySQL mode `ONLY_FULL_GROUP_BY` (aktif secara default di MySQL 8) menolak query karena `SELECT DATE_FORMAT(..., "%Y-%m-01")` dan `ORDER BY subscription_month` menggunakan ekspresi berbeda dari `GROUP BY DATE_FORMAT(..., "%Y-%m")`. MySQL tidak bisa membuktikan dependensi fungsional secara otomatis dalam kasus ini.

**Hasil Akhir:**
Halaman `/admin/subscriptions` terbuka normal. Filter Bulan menampilkan dropdown nama bulan Bahasa Indonesia dari data yang ada. 125/125 test hijau.

---

### 09:00 WITA — Filter Paket & Bulan di Halaman Subscriptions

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Resources/SubscriptionResource.php` | Tambah 2 filter baru: (1) **Filter Paket** — developer-only, dropdown LITE/PRO/CUSTOM, memfilter via relasi `juragan.plan`. (2) **Filter Bulan** — semua role, dropdown dinamis berisi bulan-bulan yang tersedia di database (nama bulan Bahasa Indonesia via Carbon locale `id`), memfilter dengan `whereYear` + `whereMonth`. |

**Alasan Perubahan:**
Developer perlu bisa menyaring subscription berdasarkan paket (misal: lihat semua juragan PRO yang belum bayar) dan berdasarkan bulan tertentu (misal: lihat semua tagihan Januari 2026). Sebelumnya hanya ada filter status saja.

**Detail implementasi:**
- Filter Paket hanya muncul untuk developer (conditional via `array_filter` + `$isDeveloper`)
- Filter Bulan mengambil opsi dari data yang sudah ada di tabel `subscriptions` — jika juragan yang login, opsi dibatasi hanya bulan miliknya sendiri
- Nama bulan ditampilkan dalam Bahasa Indonesia menggunakan `Carbon::locale('id')->isoFormat('MMMM Y')`

**Hasil Akhir:**
Halaman `/admin/subscriptions` kini memiliki 3 filter: Status, Paket (developer-only), dan Bulan. 125/125 test hijau.

---

### 08:30 WITA — Tambah Hapus Tagihan per Baris & Bulk Delete di Halaman Billings

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Resources/BillingResource.php` | (1) Tambah `DeleteAction` di row actions tabel — tombol hapus per baris dengan konfirmasi modal. (2) Tambah `DeleteBulkAction` di bulk actions — hapus banyak tagihan sekaligus dengan konfirmasi. Data isolation tetap berlaku: juragan hanya bisa melihat/menghapus tagihan anak kosnya sendiri via `getEloquentQuery()`. |

**Alasan Perubahan:**
Juragan perlu bisa menghapus tagihan yang salah input (misal: tagihan duplikat, nominal keliru yang tidak bisa di-edit ulang, atau tagihan untuk anak kos yang sudah pindah). Sebelumnya hanya ada aksi edit dan download invoice, tanpa opsi hapus.

**Hasil Akhir:**
- Setiap baris di `/admin/billings` kini memiliki tombol "Hapus" dengan konfirmasi
- Centang beberapa tagihan → bulk action "Hapus" → konfirmasi → semua terhapus
- Juragan hanya bisa menghapus tagihan anak kosnya sendiri (isolasi data)
- 125/125 test hijau

---

### 08:00 WITA — Bugfix: Class "Filament\Notifications\Actions\Action" Not Found pada Invoice Gabungan

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Resources/BillingResource.php` | Hapus import `use Filament\Notifications\Actions\Action as NotificationAction;` (class tidak ada di Filament 5.6). Ganti `NotificationAction::make('download')` → `Action::make('download')` menggunakan `Filament\Actions\Action` yang sudah diimpor. |

**Alasan Perubahan:**
Klik bulk action "Invoice Gabungan (PDF)" di halaman `/admin/billings` memunculkan error fatal:
```
Class "Filament\Notifications\Actions\Action" not found
```
Investigasi ke source `vendor/filament/notifications/src/Concerns/HasActions.php` membuktikan bahwa `HasActions` di package notifications menggunakan `Filament\Actions\Action` (bukan sub-namespace `Notifications\Actions\Action`). Class tersebut memang tidak pernah ada di Filament 5.

**Hasil Akhir:**
Bulk action "Invoice Gabungan (PDF)" berjalan tanpa error. Setelah memilih billing dari satu anak kos dan mengkonfirmasi, notifikasi persistent muncul dengan tombol "Download PDF →" yang membuka invoice gabungan di tab baru. 125/125 test hijau.

---

### 07:30 WITA — Lihat Bukti Bayar & Invoice Gabungan

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Resources/BillingResource.php` | (1) Kolom `IconColumn payment_receipt` — ikon foto hijau jika ada bukti, abu-abu jika belum; klik ikon buka receipt di tab baru. (2) Filter "Bukti Bayar" (ada/belum). (3) `BulkAction::download_merged_invoice` — pilih beberapa billing, klik "Invoice Gabungan (PDF)", muncul notifikasi persistent dengan tombol "Download PDF →" yang buka invoice gabungan di tab baru. Validasi: semua tagihan harus milik satu anak kos. |
| `app/Http/Controllers/InvoiceController.php` | Tambah `downloadMerged(Request)`: ambil `ids` dari query param (maks 36), validasi auth per billing, validasi semua milik 1 tenant, generate PDF dari template `invoices.billing-merged`, download sebagai `invoice-gabungan-YYYY-MM-sd-YYYY-MM.pdf` |
| `resources/views/invoices/billing-merged.blade.php` | Template PDF baru: header + pihak yang terlibat + tabel multi-baris (satu baris per billing: periode, jatuh tempo, status badge, nominal) + total di footer tabel + summary box (lunas/belum lunas/total) + QRIS jika ada tagihan belum lunas + footer NexaSpace |
| `routes/web.php` | Route baru `GET /invoice/billing-merged` → `InvoiceController::downloadMerged` bernama `invoice.billing.merged` |

**Alasan Perubahan:**
Juragan perlu melihat bukti transfer yang diupload anak kos langsung dari halaman admin billings tanpa harus masuk ke panel anak kos. Dan sering kali juragan perlu mencetak/mengirimkan rekap tagihan beberapa bulan sekaligus ke anak kos atau keperluan audit.

**Hasil Akhir:**
- Kolom "Bukti" di `/admin/billings` menampilkan ikon hijau jika receipt sudah diupload; klik langsung buka file-nya di tab baru
- Filter "Bukti Bayar" untuk cepat melihat siapa yang sudah/belum upload
- Centang 2-12 billing dari bulan berbeda (milik satu anak kos) → klik "Invoice Gabungan (PDF)" → konfirmasi → notifikasi muncul dengan tombol "Download PDF →" → download 1 file PDF dengan semua bulan dalam satu tabel
- 125/125 test hijau

---

### 06:30 WITA — Bugfix: Backfill Tagihan Bulan Sebelumnya saat move_in_date di Masa Lalu

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Resources/BillingResource/Pages/CreateBilling.php` | Tambah `afterCreate()`: setelah bill manual dibuat, cek apakah `move_in_date` ada di bulan yang sudah lewat. Jika ya, backfill semua bulan yang belum punya bill dari `move_in_date + 1 bulan` hingga bulan ini. Tampilkan notifikasi jumlah bill yang dibuat. |

**Alasan Perubahan:**
Saat juragan memasukkan `move_in_date = 1 Maret` tetapi saat ini sudah Juni, sistem hanya membuat 1 tagihan (Juni). Seharusnya sistem mendeteksi gap dan otomatis membuat tagihan April, Mei, Juni — yaitu semua bulan dari bulan setelah masuk hingga bulan ini yang belum ada tagihannya.

**Aturan backfill:**
- Penagihan dimulai dari **bulan setelah bulan masuk** (bukan bulan masuk itu sendiri)
- Contoh: `move_in_date = 1 Maret` → tagihan pertama = April → backfill April, Mei; + Juni dari form = total 3 tagihan
- Idempotent: bulan yang sudah ada tagihan dilewati
- Notifikasi sukses muncul jika ada tagihan backfill yang dibuat

**Hasil Akhir:**
Juragan mengisi `move_in_date = 1 Maret`, klik simpan → sistem otomatis membuat tagihan April, Mei, Juni sekaligus. Muncul notifikasi "2 tagihan bulan sebelumnya otomatis dibuat". 125/125 test hijau.

---

### 06:00 WITA — Tanggal Masuk Anak Kos & Auto-Tagihan Bulanan

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `database/migrations/2026_06_06_213828_add_move_in_date_to_users_table.php` | Kolom baru `move_in_date` (date, nullable) pada tabel `users`; diisi juragan untuk menandai kapan anak kos pertama masuk |
| `app/Models/User.php` | `move_in_date` ditambahkan ke `#[Fillable]` dan di-cast sebagai `date` |
| `app/Filament/Resources/UserResource.php` | `DatePicker::make('move_in_date')` ditambahkan pada form anak kos (visible ketika role = tenant); helper text `monthly_rate` diperbarui |
| `app/Filament/Resources/BillingResource.php` | `user_id` menjadi `->live()` dengan `afterStateUpdated`: saat anak kos dipilih, otomatis isi `move_in_date`, `amount` (dari monthly_rate), `billing_month` (bulan ini), dan `due_date` (7 hari setelah tanggal masuk). Tambah `move_in_date` DatePicker dengan `afterStateHydrated` (untuk mode edit). Label form diindonesiakan. |
| `app/Filament/Resources/BillingResource/Pages/CreateBilling.php` | `mutateFormDataBeforeCreate`: ekstrak `move_in_date` dari data form → simpan ke `users`, hapus dari data billing sebelum `Billing::create()` |
| `app/Filament/Resources/BillingResource/Pages/EditBilling.php` | `mutateFormDataBeforeSave`: idem untuk mode edit; tambah `DeleteAction` di header |
| `app/Services/BillingService.php` | (1) `generateMonthlyBills()` kini exclude tenant dengan `move_in_date` (mereka punya siklus sendiri); (2) Tambah `generateBillsForMoveInDay(int $day)`: generate tagihan untuk tenant yang `DAY(move_in_date) == $day`; (3) Tambah `createBillForTenant(User $tenant): bool`: buat tagihan satu tenant untuk bulan ini, return false jika sudah ada |
| `app/Jobs/GenerateBillsByMoveInJob.php` | Job baru: panggil `BillingService::generateBillsForMoveInDay(Carbon::today()->day)` |
| `routes/console.php` | Tambah schedule: `GenerateBillsByMoveInJob` harian 00:02 WITA |
| `app/Filament/Widgets/BillingReminderWidget.php` | Widget baru juragan-only: tampilkan anak kos yang tanggal masuknya hari ini & belum punya tagihan bulan ini; aksi per-baris "Buat Tagihan" + tombol "Buat Semua"; "upcoming" preview 3 hari ke depan |
| `resources/views/filament/widgets/billing-reminder.blade.php` | Blade view widget: tabel anak kos pending hari ini + upcoming pills |
| `app/Providers/Filament/AdminPanelProvider.php` | Daftarkan `BillingReminderWidget` setelah `JuraganOnboardingWidget` |

**Alasan Perubahan:**
Juragan membutuhkan cara untuk melacak kapan anak kos mulai menghuni kamar, agar tagihan bulanan dapat digenerate secara otomatis sesuai "ulang tahun" masuk masing-masing (bukan selalu tanggal 1 untuk semua). Ini juga memudahkan pengelolaan pergantian penghuni.

**Hasil Akhir:**
- Form `admin/billings/create` memiliki field **"Tanggal Masuk Anak Kos"**; saat anak kos dipilih, semua field (nominal, bulan, jatuh tempo, tanggal masuk) terisi otomatis dari data anak kos
- Menyimpan tagihan juga memperbarui `move_in_date` di record user anak kos
- **Auto-billing harian**: setiap tengah malam (00:02 WITA), sistem memeriksa anak kos dengan `move_in_date` yang hari-nya cocok dengan hari ini → tagihan dibuat otomatis
- **Widget dashboard juragan** "Auto-Tagihan Hari Ini": tampil daftar anak kos yang perlu dibuat tagihannya hari ini + tombol "Buat Tagihan" per baris + "Buat Semua"; juga preview anak kos yang akan tagih dalam 3 hari ke depan
- Tenant tanpa `move_in_date` tetap ditagih tanggal 1 (backward compatible)
- 125/125 test tetap hijau

---

### 04:30 WITA — Full CRUD pada RegistrationResource

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Resources/RegistrationResource.php` | Diubah total: `canCreate()` diaktifkan (developer only); `form()` diisi lengkap dengan Section "Informasi Pendaftar" (name, kos_name, email, phone), Section "Detail Paket" (plan Select live + room_count dengan maxValue berbasis plan, status, payment_status), Section "Pesan / Catatan" (message Textarea collapsible); row actions: "Setujui & Buat Akun" (provision individual), "Edit" (ke edit page), "Hapus" (delete dengan konfirmasi); bulk action "Hapus" ditambahkan; `getPages()` kini mendaftarkan `create` dan `edit`; `getNavigationBadge()` menampilkan jumlah registrasi pending |
| `app/Filament/Resources/RegistrationResource/Pages/CreateRegistration.php` | File baru — standard `CreateRecord`, redirect ke index setelah simpan |
| `app/Filament/Resources/RegistrationResource/Pages/EditRegistration.php` | File baru — standard `EditRecord` dengan `DeleteAction` di header, redirect ke index setelah simpan |
| `app/Filament/Resources/RegistrationResource/Pages/ListRegistrations.php` | Ditambahkan `getHeaderActions()` dengan `CreateAction` ("Tambah Pendaftaran") |

**Alasan Perubahan:**
Sebelumnya `/admin/registrations` hanya berupa list read-only dengan bulk actions. Developer tidak bisa membuat registrasi manual, mengedit data yang salah, atau menghapus record. Permintaan user: tambahkan full CRUD.

**Hasil Akhir:**
- Tombol **"Tambah Pendaftaran"** muncul di header list untuk developer
- Setiap baris punya tiga row actions: **Setujui & Buat Akun** (provisioning langsung), **Edit** (buka halaman edit), **Hapus** (delete dengan konfirmasi modal)
- Form create/edit lengkap: informasi pendaftar, paket (room_count dengan batas sesuai plan), status, payment_status, pesan
- Badge merah di navigasi menampilkan jumlah registrasi berstatus `pending`
- 125/125 test tetap hijau

---

### 03:00 WITA — Halaman Profil & Rekening Bank untuk Juragan

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Filament/Pages/JuraganProfilePage.php` | Halaman baru khusus juragan di admin panel: form profil (nama, HP, email notifikasi), Repeater rekening bank (nama bank, nomor rekening, atas nama), upload QRIS, ganti password; hanya tampil untuk `role = juragan` |
| `resources/views/filament/pages/juragan-profile.blade.php` | View halaman profil juragan: info kos read-only di atas (email login, nama kos, paket, kuota), form profil+rekening+QRIS, form ganti password |

**Alasan Perubahan:**
`UserResource::getEloquentQuery()` memfilter juragan hanya melihat anak kos mereka (`role=tenant`), sehingga juragan tidak dapat menemukan record dirinya sendiri dan tidak bisa mengedit rekening bank. Halaman khusus ini bypass pembatasan tersebut dan memberi juragan akses langsung ke datanya sendiri tanpa bisa menyentuh data user lain.

**Hasil Akhir:**
Juragan kini memiliki halaman **"Profil & Rekening"** di navigasi admin panel. Dari sini mereka bisa menambah, mengubah, atau menghapus rekening bank. Data rekening yang disimpan langsung tersedia sebagai pilihan dropdown saat anak kos membayar tagihan di `/tenant/billings`.

---

### 02:30 WITA — Revisi: Simulasi Pembayaran Anak Kos + Format Amount + Fix Nama Juragan

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `database/migrations/2026_06_06_203015_add_bank_accounts_to_users_table.php` | Kolom `bank_accounts` (JSON nullable) ditambahkan ke tabel `users` |
| `app/Models/User.php` | `bank_accounts` masuk `$fillable` + cast sebagai `array` |
| `app/Filament/Resources/UserResource.php` | Tambah seksi **"Rekening Bank"** (Repeater) untuk juragan: field `bank_name`, `account_number`, `account_name`; tampil/collapsible saat `role === juragan` |
| `app/Filament/Tenant/Resources/BillingResource.php` | Ganti action `Upload Receipt` dengan action **"Bayar"** — modal berisi dropdown rekening bank juragan + upload bukti transfer; setelah submit, status tagihan langsung menjadi `paid` dan notifikasi sukses muncul |
| `app/Filament/Resources/BillingResource.php` | Field `amount` di form create/edit sekarang auto-format dengan titik ribuan (Alpine.js `x-on:input`); `dehydrateStateUsing` membersihkan titik sebelum simpan ke DB |
| DB (tinker) | Nama `owner@kos-reb.com` diubah dari "haikal ariadma" → **"Pemilik Kos Reb"**; 3 rekening bank ditambahkan (BCA, BRI, GoPay/OVO) untuk simulasi pembayaran |

**Alasan Perubahan:**
1. Anak kos tidak bisa melakukan pembayaran langsung — hanya ada upload receipt tanpa alur bayar yang jelas.
2. Input `amount` di form admin tidak ada auto-format sehingga angka besar sulit dibaca saat input.
3. Akun juragan `owner@kos-reb.com` terdaftar dengan nama "haikal ariadma" (nama developer) karena di-provision manual menggunakan data developer.

**Hasil Akhir:**
- Anak kos kini melihat tombol **"Bayar"** di tabel tagihan; klik → modal muncul dengan dropdown bank juragan dan upload bukti → submit → status langsung `paid`.
- Input nominal di form admin admin auto-menambahkan titik saat mengetik (1200000 → 1.200.000).
- Juragan `owner@kos-reb.com` kini tampil sebagai "Pemilik Kos Reb" di panel.
- 3 rekening bank tersedia untuk kos-reb sebagai data simulasi.

---

### 01:30 WITA — Opsi B: Manajemen Router MikroTik dari Panel Admin

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `app/Services/MikroTikService.php` | Tambah method `getLeases(): array` — mengambil seluruh DHCP lease dari router via RouterOS API; error di-log dan mengembalikan array kosong |
| `app/Filament/Pages/RouterManagementPage.php` | Halaman Filament baru; developer dapat memilih juragan dari dropdown; juragan hanya melihat router miliknya sendiri; menampilkan status koneksi (host/port/user/badge Terhubung–Tidak Terhubung) dan tabel DHCP lease |
| `resources/views/filament/pages/router-management.blade.php` | Template Blade halaman router: dropdown juragan (developer only), seksi status koneksi, tabel lease dengan kolom MAC, IP, hostname, status, rate-limit, expires |

**Alasan Perubahan:**
Developer dan juragan sebelumnya tidak dapat melihat data DHCP lease langsung dari panel — harus login ke RouterOS secara manual. Halaman ini memberi visibilitas ke status perangkat di jaringan tanpa meninggalkan panel admin.

**Hasil Akhir:**
Halaman `/admin/router-management` kini tersedia di navigasi panel admin. Developer memilih juragan untuk melihat leases router mereka; juragan langsung melihat data router sendiri. Lease yang di-throttle ditandai dengan badge merah `256k/256k`.

---

### 01:15 WITA — Opsi D: Activity Log / Audit Trail

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `database/migrations/2026_06_06_200335_create_activity_logs_table.php` | Tabel `activity_logs` baru: `causer_id` (nullable FK → users), `causer_name` (snapshot nama), `subject_type`, `subject_id`, `event`, `description`, `properties` (JSON), `created_at` (tanpa `updated_at`) |
| `app/Models/ActivityLog.php` | Model dengan `UPDATED_AT = null`, cast `properties` → array, relasi `causer()`, static helper `record(event, description, subject?, properties?, causer?)` |
| `app/Observers/BillingObserver.php` | Mencatat `billing.status_changed` setiap kali status tagihan berubah; properties menyertakan `old_status`, `new_status`, `amount` |
| `app/Observers/SubscriptionObserver.php` | Mencatat `subscription.status_changed`; mencatat tambahan `juragan.unsuspended` saat status → `paid` |
| `app/Jobs/SuspendOverdueJuraganJob.php` | Mencatat `juragan.suspended` setiap kali juragan disuspend |
| `app/Jobs/ProvisionTenantJob.php` | Import `ActivityLog`; mencatat `juragan.provisioned` setelah provisioning berhasil di dalam transaksi DB |
| `app/Filament/Resources/ActivityLogResource.php` | Resource Filament developer-only (read-only: `canCreate/canEdit/canDelete` → false); tabel dengan kolom Waktu, Event (badge berwarna), Oleh, Keterangan; filter per event; default sort terbaru di atas |
| `app/Filament/Resources/ActivityLogResource/Pages/ListActivityLogs.php` | Halaman daftar log aktivitas |

**Alasan Perubahan:**
Tidak ada jejak audit sebelumnya — perubahan status tagihan, suspend/unsuspend juragan, dan provisioning tidak terekam. Audit trail membantu developer mendiagnosis masalah dan melihat riwayat aktivitas platform.

**Hasil Akhir:**
Setiap perubahan status tagihan, langganan, suspend/unsuspend juragan, dan provisioning kini tercatat otomatis di tabel `activity_logs`. Developer dapat melihat seluruh riwayat di `/admin/activity-logs` dengan filter per jenis event. Tidak ada package eksternal tambahan.

---

### 01:00 WITA — Opsi C: QRIS Statis per Juragan

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `database/migrations/2026_06_06_200118_add_qris_image_to_users_table.php` | Kolom `qris_image` (nullable string) ditambahkan ke tabel `users` setelah `mikrotik_pass` |
| `app/Models/User.php` | `qris_image` ditambahkan ke `$fillable` |
| `app/Filament/Resources/UserResource.php` | Seksi "QRIS Pembayaran" dengan `FileUpload` untuk kolom `qris_image`; tampil ketika juragan yang diedit adalah juragan (developer lihat semua, juragan edit milik sendiri) |
| `app/Filament/Tenant/Widgets/QrisWidget.php` | Widget tenant baru; tampil hanya jika juragan memiliki `qris_image`; menampilkan gambar QR, nama kos, dan link WhatsApp konfirmasi |
| `resources/views/filament/tenant/widgets/qris-widget.blade.php` | Template blade widget QRIS: gambar 44x44, instruksi pembayaran, nomor WA opsional |
| `app/Providers/Filament/TenantPanelProvider.php` | Daftarkan `QrisWidget` di panel tenant |
| `resources/views/invoices/billing.blade.php` | Blok QRIS ditambahkan di bagian bawah invoice PDF; tampil hanya jika `juragan->qris_image` ada dan tagihan belum `paid`; menggunakan `public_path()` bukan `asset()` karena DomPDF butuh path filesystem |

**Alasan Perubahan:**
Sebelumnya tidak ada cara bagi anak kos untuk mengetahui cara bayar via QRIS. Juragan biasanya memiliki QRIS statis dari bank atau dompet digital. Fitur ini memungkinkan juragan upload QR sekali; semua anak kos langsung bisa scan dari portal mereka.

**Hasil Akhir:**
Juragan dapat upload gambar QRIS di halaman edit profil mereka di panel admin. Anak kos melihat widget QRIS di dashboard tenant panel. Invoice PDF juga menyertakan gambar QRIS untuk tagihan yang belum dibayar.

---

## [Sesi Kerja] — 7 Juni 2026

---

### 23:45 WITA — Perbarui README.md agar sesuai kondisi platform saat ini

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `README.md` | Tulis ulang penuh: badge Laravel 13.12 (bukan 12), hapus badge Midtrans, tambah bagian "Live" dengan URL produksi, tambah tabel three-tier role model, perbarui fitur (SaaS multi-tenant, subscription billing, provisioning, email notifications, CSV export, PDF invoice, revenue chart, onboarding widget), perbarui tech stack (hapus Midtrans, tambah dompdf), perbarui service config (hapus Midtrans env, tambah MAIL_*), perbarui seeded accounts (developer + 2 juragan + anak kos), perbarui billing flow (tanpa Midtrans webhook), perbarui project structure, perbarui testing status (125/125 hijau). |

**Alasan Perubahan:**
README masih mencerminkan versi awal (single-kos, Midtrans, Laravel 12) dan tidak lagi akurat setelah transformasi ke SaaS multi-tenant Phase 1–7.

**Hasil Akhir:**
README kini selaras penuh dengan CLAUDE.md dan kondisi aktual platform.

---

### 23:30 WITA — Fix: Badge "Paling Laris" terpotong di tabel perbandingan paket

**Apa yang Diubah:**

| File | Perubahan |
|---|---|
| `resources/views/landing.blade.php` | (1) Tambah `pt-6` pada grid pricing cards agar badge `absolute -top-4` pada card PRO tidak ter-clip. (2) Ubah badge "Paling Laris" di `<th>` kolom PRO pada tabel perbandingan dari `absolute -top-3` (absolute positioning) menjadi `inline-block` dalam flow normal cell — tidak ter-clip oleh `overflow-x-auto` wrapper tabel. |

**Alasan Perubahan:**
Badge bertipe `absolute -top-N` yang keluar dari batas elemen induknya akan di-clip ketika salah satu ancestor memiliki `overflow: auto` atau `overflow: hidden`. Wrapper tabel menggunakan `overflow-x-auto` untuk responsive scroll horizontal, yang juga meng-clip overflow vertikal.

**Hasil Akhir:**
Badge "⭐ Paling Laris" tampil penuh di kedua tempat: kartu PRO (grid) maupun kolom PRO di tabel perbandingan fitur.

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
