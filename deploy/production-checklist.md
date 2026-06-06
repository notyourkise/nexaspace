# NexaSpace — Production Deployment Checklist

Domain: **nexaspace.site**
Stack: Laravel 13 + PHP 8.4 + MySQL + Filament 5

---

## 1. Environment

- [ ] Copy `.env.example` → `.env` dan isi semua nilai
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL=https://nexaspace.site`
- [ ] `APP_KEY` di-generate: `php artisan key:generate`
- [ ] `DB_*` terisi dengan kredensial MySQL production
- [ ] `MAIL_*` terisi dengan kredensial SMTP (untuk email kredensial provisioning)
- [ ] `MIKROTIK_*` terisi dengan IP dan kredensial router

## 2. Dependency & Build

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

## 3. Database

```bash
php artisan migrate --force
php artisan db:seed   # hanya untuk setup awal / fresh deploy
```

## 4. Storage

```bash
php artisan storage:link
```

Pastikan direktori `storage/` dan `bootstrap/cache/` writable oleh web server.

## 5. Cache & Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## 6. Queue Worker (Supervisor)

Salin `deploy/supervisor.conf` ke `/etc/supervisor/conf.d/nexaspace.conf`.
Ganti path dan username sesuai server.

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start nexaspace-queue:*
sudo supervisorctl status
```

Jika hosting tidak mendukung Supervisor (shared hosting):
- Gunakan cron job sebagai fallback: `* * * * * php artisan queue:work --stop-when-empty`
- Atau aktifkan queue lewat panel hosting jika tersedia.

## 7. Scheduler (Cron)

Tambahkan via cPanel > Cron Jobs (setiap menit):

```
* * * * * cd /home/u123456789/domains/nexaspace.site/public_html && php artisan schedule:run >> /dev/null 2>&1
```

Verifikasi jadwal aktif:
```bash
php artisan schedule:list
```

## 8. Verifikasi

```bash
# Cek semua environment & driver
php artisan about --only=environment,drivers

# Cek status migrasi
php artisan migrate:status

# Cek scheduler
php artisan schedule:list

# Cek routes
php artisan route:list

# Health check endpoint
curl https://nexaspace.site/health
```

Response `/health` yang sehat:
```json
{
  "status": "ok",
  "checks": {
    "database": "ok",
    "failed_jobs": 0
  }
}
```

## 9. Setelah Deploy (Verifikasi Manual)

- [ ] Login developer berhasil di `https://nexaspace.site/admin`
- [ ] Login juragan berhasil (coba `owner@mutiara.com` jika seed dijalankan)
- [ ] Login anak kos berhasil di `https://nexaspace.site/tenant`
- [ ] Halaman landing `https://nexaspace.site` tampil normal
- [ ] Form pendaftaran di `/daftar/pro` bisa disubmit
- [ ] Halaman pembayaran `/daftar/pembayaran/...` tampil (butuh signed URL)
- [ ] Email provisioning terkirim saat "Setujui & Buat Akun" (cek log jika MAIL_MAILER=log)
- [ ] Queue worker jalan: buat billing test, bayar manual, cek device restore

## 10. Monitoring

- UptimeRobot atau monitoring eksternal: ping `https://nexaspace.site/health` setiap 5 menit
- Pantau `storage/logs/laravel.log` dan `storage/logs/worker.log`
- Dashboard admin: widget "Failed Jobs" akan merah jika ada job gagal

---

## Catatan Penting

- **Payment**: Semua pembayaran (anak kos & langganan juragan) melalui transfer bank manual.
  Developer memverifikasi via WhatsApp/bukti transfer dan menandai status paid secara manual
  di panel `/admin`.
- **MikroTik**: Pastikan server production bisa koneksi ke router MikroTik pada port 8728.
  Test koneksi: `php artisan tinker` → `app(App\Services\MikroTikService::class)->findLeaseByMac('AA:BB:CC:DD:EE:FF')`
- **Email provisioning**: Set `MAIL_MAILER=smtp` di production agar juragan baru dapat email
  kredensial. Di local dev, gunakan `MAIL_MAILER=log`.
