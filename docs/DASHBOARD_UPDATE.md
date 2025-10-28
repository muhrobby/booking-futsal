# Dashboard Admin - Update dengan Date Range & Charts

## 📊 Fitur Baru yang Ditambahkan

### 1. Date Range Filter
Filterkan data dashboard berdasarkan rentang tanggal custom:
- Input tanggal mulai (start date)
- Input tanggal akhir (end date)
- Button "Filter" untuk apply
- Button "Reset" untuk kembali ke default (30 hari terakhir)

**Lokasi**: Di bagian atas dashboard, sebelah kanan judul

### 2. Chart Trend Pendapatan (Revenue Trend)
Line chart yang menampilkan trend pendapatan harian:
- **Warna**: Biru (#3B82F6)
- **Data**: Pendapatan per hari dalam rupiah
- **Format**: Rp 123.456
- **Features**: 
  - Smooth curve line
  - Filled area
  - Interactive tooltips
  - Responsive design

### 3. Chart Trend Booking (Booking Trend)
Line chart yang menampilkan trend jumlah booking harian:
- **Warna**: Hijau (#10B981)
- **Data**: Jumlah booking per hari
- **Format**: Integer (1, 2, 3, dst)
- **Features**:
  - Smooth curve line
  - Filled area
  - Interactive tooltips
  - Responsive design

## 📁 File yang Diubah

### 1. DashboardController.php
```
app/Http/Controllers/Admin/DashboardController.php
```
**Perubahan**:
- Added date range parameters (start_date, end_date)
- Added getChartData() method untuk generate data chart
- Filter semua query berdasarkan date range
- Return chartData ke view

### 2. dashboard.blade.php
```
resources/views/admin/dashboard.blade.php
```
**Perubahan**:
- Added date range filter form di header
- Added 2 chart sections (revenue & booking)
- Added Chart.js scripts
- Added chart configurations

### 3. admin.blade.php (Layout)
```
resources/views/layouts/admin.blade.php
```
**Perubahan**:
- Added @stack('scripts') before closing body tag

## 🎯 Cara Menggunakan

### Akses Dashboard
1. Login sebagai admin (admin1@example.com - password)
2. Navigate ke: `http://localhost:8000/admin/dashboard`

### Gunakan Date Range Filter

**Contoh 1: Filter 7 Hari Terakhir**
```
Start Date: 2025-10-21
End Date: 2025-10-28
[Klik Filter]
```

**Contoh 2: Filter Bulan Oktober**
```
Start Date: 2025-10-01
End Date: 2025-10-31
[Klik Filter]
```

**Reset ke Default**
```
[Klik Reset]
→ Kembali ke 30 hari terakhir
```

## 📈 Data yang Ditampilkan di Charts

### Revenue Chart
- X-Axis: Tanggal (format: dd MMM)
- Y-Axis: Pendapatan dalam Rupiah
- Tooltip: Hover untuk lihat detail per hari
- Calculation: Σ (Durasi × Price per Hour) untuk booking confirmed

### Booking Chart
- X-Axis: Tanggal (format: dd MMM)
- Y-Axis: Jumlah booking
- Tooltip: Hover untuk lihat jumlah booking per hari
- Calculation: COUNT(*) booking per hari

## 🔧 Technical Stack

### Frontend
- **Chart.js v4.4.0** - JavaScript charting library
- **Tailwind CSS** - Styling
- **Vanilla JavaScript** - Chart initialization

### Backend
- **Laravel Controller** - Data processing
- **Eloquent ORM** - Database queries
- **Carbon** - Date manipulation

## 📊 Statistik yang Difilter

Ketika date range diubah, data berikut akan difilter:

✅ Total Pengguna (user baru dalam range)
✅ Total Booking (dalam range)
✅ Total Pendapatan (dalam range)
✅ Recent Bookings (dalam range)
✅ Booking by Status (dalam range)
✅ Top Fields (dalam range)
✅ Occupancy Rate (dalam range)
✅ Revenue Chart (data dalam range)
✅ Booking Chart (data dalam range)

## 🚀 Quick Start

```bash
# 1. Pastikan seeder sudah dijalankan
php artisan migrate:fresh --seed

# 2. Start development server
php artisan serve

# 3. Login sebagai admin
Email: admin1@example.com
Password: password

# 4. Navigate ke dashboard
http://localhost:8000/admin/dashboard

# 5. Gunakan date range filter
Pilih tanggal → Filter → Lihat charts!
```

## 💡 Tips

1. **Best Practice untuk Range**:
   - Untuk performa optimal, gunakan range maksimal 90 hari
   - Range pendek (7-30 hari) memberikan visualisasi terbaik

2. **Interaksi Chart**:
   - Hover pada line untuk lihat detail
   - Charts otomatis responsive di mobile

3. **Data Accuracy**:
   - Revenue hanya dari booking 'confirmed'
   - Booking count termasuk semua status

## 🎨 Chart Customization

Untuk mengubah warna atau style chart, edit file:
```
resources/views/admin/dashboard.blade.php
```

Cari section `@push('scripts')` dan ubah:
- `borderColor` - Warna garis
- `backgroundColor` - Warna area fill
- `tension` - Kelengkungan garis (0-1)
- `fill` - Show/hide area fill

## 📝 Changelog

**Version 1.1.0** (2025-10-28)
- ✅ Added date range filter
- ✅ Added revenue trend chart
- ✅ Added booking trend chart
- ✅ Integrated Chart.js
- ✅ Responsive design for charts
- ✅ Indonesian currency formatting
