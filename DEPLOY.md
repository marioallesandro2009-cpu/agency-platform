# Deploy Checklist

Panduan singkat deploy ke production (shared hosting cPanel/Rumahweb-style). Ikuti berurutan — jangan lompat.

## 1. Sebelum build: pastikan kode final

- [ ] Semua perubahan (mobile layout responsif + security audit 2026-08-15) sudah di-commit ke git. Cek dengan `git status` — working tree harus bersih.
- [ ] Jangan deploy dari working tree yang masih ada perubahan uncommitted — kalau ada masalah di production, Anda butuh commit yang jelas untuk rollback.

## 2. Build asset production (di lokal)

```bash
npm run build
```

- [ ] Verifikasi `public/build/manifest.json` ter-update — cek timestamp-nya harus lebih baru dari commit terakhir:
  ```bash
  ls -la public/build/manifest.json
  ```
- [ ] Build harus selesai tanpa error (warning ukuran chunk besar dari `RichTextEditor` — CKEditor — itu normal, bukan error, aman diabaikan).

## 3. Simulasikan install dependency production (di lokal, dry-run)

```bash
composer install --optimize-autoloader --no-dev --dry-run
```

- [ ] Pastikan tidak ada error/conflict di output.
- **Jangan** jalankan tanpa `--dry-run` di folder lokal ini — itu akan menghapus PHPUnit/Faker/dev-tools yang masih dipakai untuk testing lokal. Command sungguhannya (`composer install --optimize-autoloader --no-dev`, tanpa `--dry-run`) baru dijalankan **di server**, setelah upload, pada Langkah 5.

## 4. File yang WAJIB diupload ke server

- `app/`, `bootstrap/`, `config/`, `database/`, `resources/`, `routes/`, `public/` (termasuk `public/build/` hasil Langkah 2)
- `composer.json`, `composer.lock`
- `.htaccess` (di `public/`)
- `artisan`

## 5. File/folder yang TIDAK BOLEH diupload

- `node_modules/` — tidak dibutuhkan di server, tidak ada proses Node yang jalan di production (hanya asset hasil build yang dipakai)
- `.env` **lokal** — jangan pernah upload file `.env` dari mesin dev. Buat `.env` baru langsung di server dari `.env.production.example` (lihat bagian bawah)
- `vendor/` versi lokal (yang masih include dev-dependencies) — install ulang di server dengan `composer install --no-dev` (lihat Langkah 5 di bawah), atau kalau upload manual, install ulang tanpa `--no-dev` dulu baru dijalankan `--no-dev` di server
- Isi lama `storage/logs/*.log` — tidak perlu ikut ter-upload, biarkan server mulai dengan log kosong
- `tests/`, `.git/` — opsional tidak wajib ada di server (kalau ikut ter-upload pun tidak masalah selama document root benar, lihat checklist keamanan di bawah)

## 6. Setelah upload, di server

```bash
composer install --optimize-autoloader --no-dev
php artisan key:generate         # HANYA jika APP_KEY masih kosong di .env server
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

- [ ] `.env` server sudah diisi lengkap dari `.env.production.example` (lihat file itu untuk daftar lengkap + penjelasan tiap key).
- [ ] Buat akun admin pertama:
  ```bash
  php artisan admin:create --email=you@yourdomain.com --name="Nama Anda"
  ```
  (akan minta password interaktif, tidak lewat argumen — lihat komentar di `app/Console/Commands/AdminCreateCommand.php`)

## 6a. Performa server: OPcache (sekali saja, di cPanel)

Tanpa OPcache, PHP membaca ulang ratusan file setiap ada pengunjung. Di server produksi (Rumahweb, cPanel) ini menaikkan waktu PHP dari sekitar 9 ms menjadi sekitar 150 ms per halaman.

1. cPanel, buka **Select PHP Version**, pilih PHP **8.3**, lalu di tab **Extensions** centang `opcache`.
2. Di tab **Per Domain Settings**, pastikan domain ini memakai **CloudLinux PHP Selector** (klik **Use PHP Selector**). Kalau domain masih "handled by cPanel MultiPHP Manager", centang ekstensi di PHP Selector **tidak berlaku** untuk domain itu.
3. Hentikan proses PHP lama supaya pengaturan baru terpakai:
   ```bash
   pkill -u $(whoami) lsphp
   ```
4. Cek waktu PHP lewat header `Server-Timing` (jalankan beberapa kali, kunjungan pertama setelah restart memang lebih lambat):
   ```bash
   for i in 1 2 3 4 5; do curl -s -I https://domainanda.com/robots.txt | grep -i server-timing; done
   ```
   Normalnya `app;dur=` di bawah sekitar 50. Kalau sekitar 150 atau lebih, OPcache belum aktif untuk domain ini, jadi ulangi langkah 1–3.

## 6b. Update rutin setelah deploy pertama

Jalankan dari folder project di server:

```bash
git pull origin main
composer install --optimize-autoloader --no-dev   # hanya jika composer.lock berubah
php artisan migrate --force
php artisan optimize
php artisan view:cache
```

- Ada artikel baru di `database/seeders/data/blog-artikel.php`: `php artisan db:seed --class=ArticleSeeder --force` (hanya artikel, tidak menimpa setting).
- `CorporateContentSeeder` **menimpa** tagline, deskripsi website, dan judul beranda; jalankan hanya bila konten itu memang ikut berubah.
- Setelah halaman atau artikel baru tayang: `php artisan indexnow:submit` untuk memberi tahu Bing.

## 7. Checklist keamanan manual (cek langsung di browser setelah deploy)

- [ ] **Document root mengarah ke folder `public/`**, bukan root project. Test: buka `https://domainanda.com/composer.json` dan `https://domainanda.com/.env` — keduanya HARUS 404/403, bukan menampilkan isi file.
- [ ] **`.git` tidak bisa diakses**: buka `https://domainanda.com/.git/config` — harus 404/403.
- [ ] **Permission file/folder di server**: file `644`, folder `755` (khususnya `storage/` dan `bootstrap/cache/` harus writable oleh web server user, tapi tetap `755` bukan `777`).
- [ ] **`APP_DEBUG=false` benar-benar aktif**: buka URL yang sengaja tidak ada (`https://domainanda.com/halaman-tidak-ada-xyz`) — harus muncul halaman 404 biasa, BUKAN stack trace Laravel dengan nama file/path server. Kalau masih muncul detail error, `APP_DEBUG` masih `true` di server — perbaiki sebelum lanjut, ini kebocoran informasi serius.
- [ ] **HTTPS aktif dan dipaksa**: buka `http://domainanda.com` (bukan https) — harus otomatis redirect ke `https://`. Ini sudah ditangani otomatis oleh `ForceHttps` middleware selama `APP_ENV=production`, tapi tetap pastikan sertifikat SSL sudah terpasang di hosting sebelum test ini (kalau belum ada SSL, redirect ke https akan gagal total — pasang SSL dulu, baru set `APP_ENV=production`).
- [ ] Login ke `/login`, masuk ke `/admin`, pastikan panel admin bisa diakses dan berfungsi normal dengan akun yang dibuat di Langkah 6.
- [ ] Test kirim form lead/konsultasi di halaman publik — pastikan tidak error 500 (indikasi `MAIL_MAILER` atau DB config salah).

## 8. Belum wajib sebelum deploy, tapi dijadwalkan

- **CKEditor**: `npm audit` melaporkan 68 kerentanan (1 high, 64 moderate — dari `@ckeditor/ckeditor5-clipboard` XSS, 3 low tidak terkait) di komponen editor admin (`RichTextEditor.vue`). Tidak ada exposure publik — komponen ini 100% di balik login admin. Aman ditunda, tapi jadwalkan upgrade major CKEditor (`npm audit fix --force`) + tes manual editor artikel sebagai maintenance terpisah.
