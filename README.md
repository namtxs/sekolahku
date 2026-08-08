# Sekolahku v2

CMS (Content Management System) sekolah modern berbasis **Laravel 13** dan **Filament 4** — pengganti CMS Sekolahku lama. Terdiri dari website publik yang siap pakai untuk sekolah beserta panel admin lengkap untuk mengelola seluruh konten.

## Fitur

### Website Publik

- **Berita/artikel** dengan kategori, tag, dan komentar pengunjung (dengan moderasi).
- **Halaman statis** (profil sekolah, visi-misi, dsb.).
- **Galeri foto** berbasis album.
- **Pusat unduhan** (formulir, kalender pendidikan, dokumen sekolah).
- **Formulir kontak** yang masuk ke panel admin.
- **Banner** untuk pengumuman/promosi.
- **Menu builder** — struktur navigasi bebas diatur dari admin.
- **Sistem widget sidebar** (widget dapat disusun dan dikonfigurasi dari admin).
- **SEO**: sitemap XML, RSS feed, dan meta Open Graph.

### Panel Admin (Filament 4)

- 12 modul pengelolaan: Berita, Halaman, Kategori, Tag, Komentar, Album Galeri, Unduhan, Banner, Menu, Widget, Pesan Kontak, dan Pengguna.
- Manajemen peran & izin (Spatie Permission): **Super Admin**, **Admin**, **Operator**, **Penulis**.
- Pengaturan sekolah (nama, logo, kontak, dsb.) via Spatie Settings.
- Dashboard dengan statistik, grafik posting, komentar tertunda, dan aksi cepat.

### Sistem Multi-Tema

Tampilan website publik menggunakan sistem tema — setiap tema berada di `resources/views/themes/{nama-tema}` sehingga mudah dikustomisasi atau ditambah tema baru.

## Kebutuhan Sistem

| Komponen | Versi |
|----------|-------|
| PHP | >= 8.3 (ekstensi: `zip`, `gd`, `mbstring`, `intl`, `pdo_mysql`, dll.) |
| Composer | 2.x |
| Node.js | >= 20 |
| MySQL | 8.x |

## Instalasi

```bash
# 1. Clone repositori
git clone <url-repo> sekolahku-v2
cd sekolahku-v2

# 2. Install dependensi PHP
composer install

# 3. Siapkan environment
cp .env.example .env
php artisan key:generate

# 4. Buat database MySQL (mis. `sekolahku_v2`), lalu sesuaikan
#    DB_DATABASE, DB_USERNAME, DB_PASSWORD di .env

# 5. Migrasi + seeder (role & permission + konten demo + widget)
php artisan migrate
php artisan db:seed

# 6. Build aset frontend
npm install
npm run build

# 7. Symlink storage untuk media upload
php artisan storage:link
```

### Akun Default (hasil seeder)

| Peran | Email | Password |
|-------|-------|----------|
| Super Admin | `admin@sekolahku-v2.test` | `password` |
| Operator | `operator@sekolahku-v2.test` | `password` |
| Penulis | `penulis@sekolahku-v2.test` | `password` |

> **PERINGATAN:** Segera ganti semua password default di atas sebelum aplikasi dipakai di produksi. Jangan menjalankan seeder konten demo di lingkungan produksi.

Panel admin dapat diakses di `/admin`.

## Catatan Laragon / Windows

- Laragon otomatis membuat virtual host: **http://sekolahku-v2.test**.
- **PENTING — versi PHP CLI:** proyek ini membutuhkan PHP **8.3**, sementara `php` default di PATH lingkungan dev ini adalah 8.1. Sebelum menjalankan perintah `artisan`/`composer` di PowerShell, arahkan PATH ke PHP 8.3 terlebih dahulu:

```powershell
$env:Path = "C:\laragon\bin\php\php-8.3.31-Win32-vs16-x64;" + $env:Path
php -v   # pastikan 8.3.x
```

## Testing

Test berjalan di atas database MySQL yang sudah di-seed (SmokeTest memverifikasi halaman-halaman utama):

```bash
php artisan test
```

Pastikan `.env` (atau variabel environment saat menjalankan test) memakai:

```
DB_CONNECTION=mysql
DB_DATABASE=sekolahku_v2
```

## Konfigurasi Email (Produksi)

Aplikasi mengirim notifikasi email (komentar baru, pesan kontak, pendaftar PPDB, dan hasil keputusan PPDB) melalui **antrean** (`QUEUE_CONNECTION=database`). Alamat email tujuan admin diambil dari **Pengaturan Sekolah → Email** di panel admin (bila kosong, notifikasi ke admin otomatis dilewati).

Di lingkungan dev, `MAIL_MAILER=log` (email hanya ditulis ke log). Untuk produksi, gunakan SMTP:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.penyedia-anda.com
MAIL_PORT=587
MAIL_USERNAME=user@sekolah.sch.id
MAIL_PASSWORD=rahasia
MAIL_SCHEME=null            # atau tls sesuai penyedia
MAIL_FROM_ADDRESS="noreply@sekolah.sch.id"
MAIL_FROM_NAME="${APP_NAME}"
```

Karena email dikirim via antrean database, jalankan worker antrean di produksi (mis. via Supervisor/systemd):

```bash
php artisan queue:work --tries=3
```

## Struktur Proyek (Ringkas)

| Lokasi | Isi |
|--------|-----|
| `resources/views/themes/{tema}` | Template tema website publik (bawaan: `default`) |
| `resources/css/filament/admin/theme.css` | Tema kustom panel admin Filament |
| `app/Filament/Resources` | Modul-modul CRUD panel admin |
| `app/Filament/Widgets` | Widget dashboard admin |
| `app/Settings` | Kelas pengaturan sekolah (Spatie Settings) |
| `database/seeders` | `RolePermissionSeeder`, `DemoContentSeeder`, `WidgetSeeder` |

## Lisensi & Kredit

Kode proyek dirilis di bawah [lisensi MIT](https://opensource.org/licenses/MIT). Dibangun dengan [Laravel](https://laravel.com), [Filament](https://filamentphp.com), [Tailwind CSS](https://tailwindcss.com), [Alpine.js](https://alpinejs.dev), dan paket-paket [Spatie](https://spatie.be/open-source) (Media Library, Permission, Settings, Sitemap).
