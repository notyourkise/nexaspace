# Changelog — NexaSpace

Semua perubahan dicatat secara kronologis.
Zona waktu: **WITA (UTC+8) — Balikpapan, Kalimantan Timur**

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

## Ringkasan File yang Dimodifikasi

| File | Jumlah Perubahan |
|---|---|
| `resources/views/landing.blade.php` | 12 perubahan terpisah |

## File yang Dibuat

| File | Keterangan |
|---|---|
| `CHANGELOG.md` | File ini |
