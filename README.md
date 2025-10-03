# Booking Futsal

Aplikasi Booking Futsal membantu pengelola lapangan menerima reservasi secara daring, melacak ketersediaan jadwal, serta memudahkan pengguna melihat informasi lapangan populer. Proyek ini dibangun dengan Laravel 12, Livewire, dan Tailwind CSS.

## Fitur Utama

- Manajemen jadwal lapangan dengan tampilan kartu vertikal yang responsif.
- Pencarian jadwal berdasarkan lapangan dan tanggal.
- Halaman beranda dengan hero banner promosi serta daftar lapangan populer.
- Halaman kontak berisi form dan informasi operasional.
- Autentikasi pengguna, halaman booking saya, dan panel admin (kelola lapangan & booking).

## Teknologi

- **Backend**: PHP 8.3, Laravel 12, Livewire 3.
- **Frontend**: Tailwind CSS, Vite.
- **Database**: SQLite (default) atau MySQL bila diperlukan.
- **Container**: Docker + Docker Compose dengan dukungan Traefik reverse proxy.

## Persiapan Lingkungan

Pastikan telah memasang perangkat lunak berikut:

- PHP 8.3 dan Composer 2.
- Node.js 20 dan npm 10.
- SQLite 3 (gunakan bawaan OS atau paket resmi).
- (Opsional) Docker 24+ dan Docker Compose 2+.

Salin berkas contoh `.env` bila belum tersedia:

```bash
cp .env.example .env
php artisan key:generate
```

### Instalasi Lokal

1. Pasang dependensi backend dan frontend:
   ```bash
   composer install
   npm install
   ```
2. Jalankan migrasi dan seed data awal:
   ```bash
   php artisan migrate --seed
   ```
   Secara bawaan aplikasi memakai SQLite di `database/database.sqlite`. Sesuaikan `.env` bila ingin menggunakan database lain.
3. Jalankan server pengembangan dan Vite:
   ```bash
   php artisan serve
   npm run dev
   ```
4. Buka aplikasi di `http://localhost:8000`.

### Menjalankan dengan Docker

Konfigurasi container tersedia di `docker-compose.yml` dan membutuhkan jaringan eksternal `traefik-proxy` jika ingin memanfaatkan Traefik. Langkah cepat:

```bash
# optional: buat jaringan traefik bila belum ada
# docker network create traefik-proxy

# build image
docker compose build

# jalankan container
docker compose up -d

# setup aplikasi (generate key, migrasi, seeding)
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Secara default aplikasi akan tersedia pada port `8002`. Ubah label Traefik atau variabel lingkungan pada `docker-compose.yml` agar sesuai domain produksi Anda.

### Perintah Artisan dan npm yang Berguna

- `php artisan migrate:fresh --seed` – reset database dan seed ulang.
- `php artisan optimize` – optimisasi konfigurasi.
- `npm run build` – kompilasi aset produksi.

## Struktur Direktori Penting

- `app/` – kode backend Laravel (controller, model, policy, dst.).
- `resources/views/` – tampilan Blade termasuk halaman home, jadwal, dan kontak.
- `database/migrations/` – migrasi tabel lapangan, jadwal, booking, dan user.
- `database/seeders/` – data awal lapangan, slot waktu, dan user admin.

## Kontribusi

Gunakan Git flow standar: buat branch fitur, lakukan commit deskriptif (bisa berbahasa Indonesia), lalu ajukan pull request. Pastikan menjalankan uji migrasi serta format kode sebelum mengirimkan perubahan.

## Lisensi

Proyek ini dirilis di bawah lisensi [MIT](LICENSE).
