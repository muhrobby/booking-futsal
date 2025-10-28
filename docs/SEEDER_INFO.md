# Seeder dan User Management - Dokumentasi

## Perubahan yang Dibuat

### 1. Database Seeders

#### UserSeeder (Updated)
- **File**: `database/seeders/UserSeeder.php`
- **Fungsi**: Membuat 10 admin dan 50 user
- **Detail**:
  - 10 Admin: email `admin1@example.com` - `admin10@example.com`
  - 50 User: email `user1@example.com` - `user50@example.com`
  - Password default: `password`
  - Phone format: Auto-generated

#### BookingSeeder (New)
- **File**: `database/seeders/BookingSeeder.php`
- **Fungsi**: Membuat booking untuk setiap user dengan jumlah berbeda per hari
- **Detail**:
  - Periode: 30 hari ke depan dari hari ini
  - Jumlah booking per user per hari: Random 0-2
  - Total booking yang dibuat: ~478 bookings
  - Status: Random (pending/confirmed untuk masa depan, confirmed/canceled untuk masa lalu)

### 2. User Management Interface

#### Controller
- **File**: `app/Http/Controllers/Admin/UserController.php`
- **Fitur**:
  - Index dengan filter (search, role) dan pagination
  - Create user baru
  - Edit user (termasuk ubah password)
  - Delete user (tidak bisa hapus akun sendiri)

#### Views
- **Index**: `resources/views/admin/users/index.blade.php`
  - Tabel user dengan kolom: Nama, Email, Telepon, Role, Total Booking, Terdaftar
  - Filter: Search (nama/email/telepon) dan Role (admin/user)
  - Pagination
  - Button: Edit dan Hapus

- **Create**: `resources/views/admin/users/create.blade.php`
  - Form tambah user baru
  - Field: Nama, Email, Telepon, Role, Password, Konfirmasi Password

- **Edit**: `resources/views/admin/users/edit.blade.php`
  - Form edit user
  - Password opsional (kosongkan jika tidak ingin diubah)

#### Navigation
- **File**: `resources/views/components/admin/sidebar.blade.php`
- Menu "User" ditambahkan di sidebar admin

### 3. Routes
- **File**: `routes/web.php`
- Routes yang ditambahkan:
  ```
  GET    /admin/users          - List users
  GET    /admin/users/create   - Form tambah user
  POST   /admin/users          - Store user baru
  GET    /admin/users/{id}/edit - Form edit user
  PUT    /admin/users/{id}     - Update user
  DELETE /admin/users/{id}     - Hapus user
  ```

### 4. Migration Fix
- **File**: `database/migrations/2025_09_22_161159_add_role_and_phone_to_us_phone_to_users_table.php`
- Role enum diubah dari `['admin','member']` menjadi `['admin','user']`

## Cara Menjalankan

### 1. Reset Database dan Jalankan Seeder
```bash
php artisan migrate:fresh --seed
```

### 2. Login Credentials

**Admin** (pilih salah satu):
- Email: admin1@example.com - admin10@example.com
- Password: password

**User** (pilih salah satu):
- Email: user1@example.com - user50@example.com
- Password: password

### 3. Akses User Management
- Login sebagai admin
- Navigasi: Admin Panel → User (di sidebar)
- URL: `http://localhost/admin/users`

## Fitur User Management

### Filter & Search
- **Search**: Cari berdasarkan nama, email, atau nomor telepon
- **Filter Role**: Tampilkan hanya admin atau user
- **Reset Button**: Kembalikan ke tampilan semua user

### Actions
- **Tambah User**: Buat user atau admin baru
- **Edit User**: Ubah informasi user (nama, email, telepon, role, password)
- **Hapus User**: Hapus user (tidak bisa hapus akun sendiri)

### Pagination
- 10 user per halaman
- Navigasi halaman di bagian bawah tabel

## Data yang Dibuat

- ✅ 10 Admin
- ✅ 50 User
- ✅ ~478 Bookings (distribusi random untuk 30 hari ke depan)
- ✅ Setiap user memiliki 0-2 booking per hari dengan jumlah berbeda-beda

## Catatan
- Password untuk semua user: `password`
- Booking dibuat dengan status random sesuai tanggal
- Slot booking yang sama tidak akan double (protected by unique constraint)

---

## Update Dashboard dengan Date Range dan Charts (2025-10-28)

### Perubahan yang Dibuat

#### 1. Dashboard Controller
- **File**: `app/Http/Controllers/Admin/DashboardController.php`
- **Fitur Baru**:
  - Date range filter (start_date & end_date parameters)
  - Default range: 30 hari terakhir
  - Chart data generation untuk revenue & booking trends
  - Filtering semua statistik berdasarkan date range

#### 2. Dashboard View
- **File**: `resources/views/admin/dashboard.blade.php`
- **Fitur Baru**:
  
  **Date Range Filter:**
  - Input tanggal mulai dan akhir
  - Button Filter untuk apply filter
  - Button Reset untuk kembali ke default (30 hari terakhir)
  
  **Chart Pendapatan (Revenue Trend):**
  - Line chart menggunakan Chart.js
  - Menampilkan pendapatan harian dalam rentang tanggal
  - Format currency Indonesia (Rp)
  - Smooth curve dengan fill area
  - Interactive tooltip dengan format rupiah
  
  **Chart Booking (Booking Trend):**
  - Line chart menggunakan Chart.js
  - Menampilkan jumlah booking harian dalam rentang tanggal
  - Integer values untuk jumlah booking
  - Interactive tooltip

#### 3. Admin Layout
- **File**: `resources/views/layouts/admin.blade.php`
- **Perubahan**: Menambahkan `@stack('scripts')` sebelum closing body tag
- **Fungsi**: Support untuk Chart.js scripts dari dashboard

### Library Eksternal
- **Chart.js v4.4.0** - Loaded dari CDN
- URL: `https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js`

### Fitur Dashboard yang Tersedia

#### Filter Berdasarkan Tanggal
```
GET /admin/dashboard?start_date=2025-10-01&end_date=2025-10-28
```

#### Data yang Difilter
- Total Pengguna (user baru dalam range)
- Total Booking (dalam range)
- Total Pendapatan (booking confirmed dalam range)
- Recent Bookings (dalam range)
- Booking by Status (dalam range)
- Top Fields (berdasarkan booking dalam range)
- Occupancy Rate (dalam range)

#### Charts
1. **Revenue Trend Chart**
   - Type: Line chart
   - Data: Pendapatan harian
   - Color: Blue (#3B82F6)
   - Format: Rupiah Indonesia
   - Features: Smooth curve, filled area, tooltips

2. **Booking Trend Chart**
   - Type: Line chart
   - Data: Jumlah booking harian
   - Color: Green (#10B981)
   - Format: Integer
   - Features: Smooth curve, filled area, tooltips

### Cara Menggunakan

1. **Akses Dashboard**
   - Login sebagai admin
   - Navigate ke `/admin/dashboard`

2. **Gunakan Date Range Filter**
   - Pilih tanggal mulai
   - Pilih tanggal akhir
   - Klik "Filter"
   - Semua statistik dan chart akan diupdate

3. **Reset Filter**
   - Klik "Reset" untuk kembali ke 30 hari terakhir

### Contoh Penggunaan

**Filter 1 Minggu Terakhir:**
- Start Date: 2025-10-21
- End Date: 2025-10-28
- Klik Filter

**Filter 1 Bulan Terakhir:**
- Start Date: 2025-09-28
- End Date: 2025-10-28
- Klik Filter

**Filter Custom Range:**
- Start Date: [Pilih tanggal]
- End Date: [Pilih tanggal]
- Klik Filter

### Technical Details

#### Revenue Calculation
```php
Pendapatan = Σ (Durasi Slot dalam Jam × Price per Hour)
- Hanya booking dengan status 'confirmed'
- Filtered by date range
```

#### Chart Data Generation
- Loop through each day in the date range
- Calculate daily revenue and booking count
- Format data for Chart.js
- Return as JSON to frontend

### Performance Notes
- Chart data generated on-demand based on filter
- Efficient SQL queries with date filtering
- Cached data dapat ditambahkan untuk improve performance
- Limit maksimal range disarankan untuk performa optimal


---

## Fix: User Filter Pagination (2025-10-28)

### Masalah
Ketika melakukan filter di halaman `/admin/users`, kemudian klik halaman selanjutnya (pagination), **filter hilang/reset otomatis**.

### Penyebab
Pagination tidak membawa query string (parameter filter) ke halaman berikutnya.

**Code sebelumnya:**
```php
$users = $query->orderBy('created_at', 'desc')->paginate(10);
```

### Solusi
Tambahkan `->withQueryString()` pada pagination agar parameter filter tetap terbawa.

**Code setelah diperbaiki:**
```php
$users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
```

### Testing
1. Navigate ke: `http://localhost:8000/admin/users`
2. Isi filter search dengan "admin"
3. Pilih role "admin"
4. Click "Filter"
5. Click halaman 2 (jika ada)
6. ✅ Filter tetap aktif, URL: `/admin/users?search=admin&role=admin&page=2`

### File Changed
- ✅ `app/Http/Controllers/Admin/UserController.php`

Ini **bukan bug**, tapi **improvement** agar UX lebih baik! 🎉
