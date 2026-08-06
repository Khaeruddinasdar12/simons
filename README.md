# SIMONS

**Sistem Manajemen Operasional Non Srikandi**

Aplikasi web untuk pengelolaan alur permohonan **SK Pembimbing Proposal Skripsi** (dan fondasi tahap berikutnya: SK Penguji & Undangan Munaqasyah) di Fakultas Syariah dan Hukum Islam (FSHI) IAIN Bone.

---

## Fitur utama

- Form publik pengajuan usul pembimbing (NIM sebagai kunci mahasiswa)
- Tracking status permohonan berdasarkan NIM
- Panel admin (Filament) dengan role: Akademik, Kabag, Wakil Dekan 1, Dekan, Superadmin
- Alur verifikasi paralel Kabag & Wadek 1, lalu penerbitan SK oleh Dekan
- Generate PDF SK resmi (DomPDF) + QR verifikasi TTD & tracking
- Preview SK di browser (tanpa membebani server DomPDF)
- Notifikasi email saat SK terbit (SMTP Brevo / penyedia lain)
- Struktur data mahasiswa terpusat (`mahasiswas.nim`) siap dilanjutkan ke tahap berikutnya

---

## Requirement

### Server / runtime

| Komponen | Versi / catatan |
|----------|-----------------|
| **PHP** | **8.1+** (disarankan 8.1.x / 8.2.x) |
| Extensi PHP | `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `gd` atau `imagick` (untuk QR / gambar) |
| **Composer** | 2.x |
| **Node.js** | 18+ (disarankan LTS) |
| **NPM** | ikut instalasi Node |
| **MySQL** | 5.7+ / **8.x** (atau MariaDB setara) |
| Web server | Laragon / XAMPP / Nginx / Apache, atau `php artisan serve` |

### Stack aplikasi

| Paket | Keterangan |
|-------|------------|
| Laravel | 10.x |
| Filament | 3.2 |
| Livewire | 3.x |
| Jetstream | 4.x (auth / session) |
| DomPDF (`barryvdh/laravel-dompdf`) | PDF SK resmi |
| Endroid QR Code | QR pada SK |
| Vite + Tailwind CSS | aset front-end |

### Lingkungan yang teruji

- Windows + **Laragon** (PHP 8.1.10)
- MySQL lokal
- `APP_URL=http://localhost:8000`

---

## Instalasi

### 1. Clone / salin project

```bash
cd D:\app
# atau lokasi project Anda
cd sk
```

### 2. Install dependensi PHP

```bash
composer install
```

### 3. Install dependensi front-end

```bash
npm install
```

### 4. Environment

```bash
copy .env.example .env
# Linux/macOS: cp .env.example .env

php artisan key:generate
```

Sesuaikan file `.env` (lihat bagian **Konfigurasi `.env`** di bawah).

### 5. Database

Buat database kosong, contoh:

```sql
CREATE DATABASE sk_fshi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Lalu:

```bash
php artisan migrate --seed
```

Perintah di atas menjalankan migrasi dan seeder akun admin.

### 6. Storage (symlink unggahan & file SK)

```bash
php artisan storage:link
```

### 7. Build aset

**Development** (hot reload):

```bash
npm run dev
```

**Production**:

```bash
npm run build
```

### 8. Jalankan aplikasi

Terminal 1 (jika belum `npm run build`):

```bash
npm run dev
```

Terminal 2:

```bash
php artisan serve
```

Akses:

| Halaman | URL |
|---------|-----|
| Form publik | http://localhost:8000/ |
| Tracking | http://localhost:8000/tracking |
| Admin login | http://localhost:8000/admin/login |

> Pastikan `APP_URL` di `.env` sama dengan URL yang dipakai (termasuk port `:8000` jika memakai `artisan serve`).

---

## Konfigurasi `.env`

### Wajib

```env
APP_NAME=SIMONS
APP_FULL_NAME="Sistem Manajemen Operasional Non Srikandi"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sk_fshi
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=43200
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
```

### Email (notifikasi SK terbit)

Gunakan SMTP yang sudah diverifikasi (contoh Brevo). **`MAIL_FROM_ADDRESS` harus alamat pengirim yang verified**, bukan hanya login SMTP.

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=akun-smtp-anda@smtp-brevo.com
MAIL_PASSWORD=smtp-key-anda
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=pengirim-verified@domain-anda.com
MAIL_FROM_NAME="FSHI IAIN Bone"
```

Setelah mengubah `.env`:

```bash
php artisan config:clear
```

### Logo

Pastikan file berikut ada di folder `public/`:

- `public/logoiainbone.png`
- `public/logokemenag.png`

---

## Akun admin (seeder)

Password default semua akun: **`password`**

| Role | Email |
|------|-------|
| Superadmin | `superadmin@fshi.local` |
| Akademik | `akademik@fshi.local` |
| Kabag | `kabag@fshi.local` |
| Wakil Dekan 1 | `wadek1@fshi.local` |
| Dekan | `dekan@fshi.local` |

**Penting:** ganti password segera di lingkungan production / shared.

Jalankan ulang seeder saja (opsional):

```bash
php artisan db:seed --class=AdminUserSeeder
```

---

## Alur bisnis singkat

1. Mahasiswa mengisi form di beranda (data tersimpan di `mahasiswas` + `permohonan_pembimbing`).
2. **Akademik** meninjau, dapat mengedit (termasuk NIM), mengirim ke pimpinan, atau menolak permanen.
3. **Kabag** dan **Wadek 1** menyetujui secara paralel (bebas urutan).
4. Setelah keduanya setuju, **Dekan** dapat menerbitkan SK (PDF + email) atau mengembalikan ke akademik.
5. Jika dikembalikan / ditolak pimpinan → status `dikembalikan_akademik`; akademik dapat memperbaiki lalu kirim ulang (persetujuan pimpinan di-reset).
6. Mahasiswa memantau via **/tracking?nim=...**.
7. Pengajuan ulang diizinkan jika tidak ada permohonan pembimbing yang masih aktif.

Struktur tahap berikutnya (skeleton DB sudah ada):

`SK Pembimbing` → `SK Penguji` → `Undangan Munaqasyah`

---

## Perintah berguna

```bash
# Migrasi + seeder
php artisan migrate --seed

# Bersihkan cache
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear

# Link storage
php artisan storage:link

# Build front-end production
npm run build
```

---

## Struktur penting

```
app/
  Filament/Resources/     # Panel admin permohonan
  Http/Controllers/       # Form publik, tracking, SK
  Mail/                   # Email SK terbit
  Models/                 # Mahasiswa, PermohonanPembimbing, …
  Services/               # Generator SK, kirim email
database/migrations/      # Skema DB
resources/views/
  permohonan/             # Form & tracking publik
  sk/                     # Template SK (PDF & preview browser)
  emails/                 # Template email
public/
  logoiainbone.png
  logokemenag.png
```

---

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Logo / file usul 404 | Jalankan `php artisan storage:link` |
| CSS/JS tidak tampil | Jalankan `npm run dev` atau `npm run build` |
| Link di email salah host/port | Samakan `APP_URL` dengan URL aktual, lalu `config:clear` |
| Email tidak masuk | Cek SMTP, `MAIL_FROM` verified, folder Spam, Brevo Transactional Logs |
| Error DomPDF / QR | Pastikan ekstensi `gd` (atau imagick) aktif di PHP |
| Session hilang cepat | `SESSION_DRIVER=database` + migrasi `sessions` sudah jalan |
| Preview SK error Blade | `php artisan view:clear` |

---

## Production checklist

- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `APP_URL` memakai HTTPS domain publik
- [ ] Password admin diganti
- [ ] Kredensial mail production & pengirim verified
- [ ] `npm run build`
- [ ] `php artisan config:cache` / `route:cache` (opsional)
- [ ] Backup database rutin
- [ ] Permission folder `storage/` dan `bootstrap/cache/` writable

---

## Lisensi

Project berbasis [Laravel](https://laravel.com) (MIT). Sesuaikan kebijakan lisensi institusi untuk kode aplikasi ini.
