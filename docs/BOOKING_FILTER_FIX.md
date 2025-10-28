# Booking Filter - Fix Documentation

## Masalah yang Diperbaiki

Filter pada halaman Kelola Booking di:
- `http://localhost:8000/admin/bookings`

tidak berfungsi dengan baik.

## Penyebab Masalah

### 1. Request Method Issue
Controller menggunakan method yang tidak kompatibel dengan beberapa versi Laravel:
```php
// PROBLEMATIC
$request->integer('field_id')
$request->string('status')
$request->date('date')
```

### 2. Nested Button Element
Filter button dibungkus dalam component `<x-button>` yang menghasilkan nested button tag, menyebabkan form tidak submit dengan benar.

### 3. Success Message Key
Menggunakan `'status'` sebagai session key yang tidak ditangkap oleh alert component.

## Solusi yang Diterapkan

### 1. BookingController - Fix Request Methods

**File**: `app/Http/Controllers/Admin/BookingController.php`

**BEFORE:**
```php
->when($request->filled('field_id'), fn ($query) => $query->where('field_id', $request->integer('field_id')))
->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
->when($request->filled('date'), fn ($query) => $query->whereDate('booking_date', $request->date('date')))
```

**AFTER:**
```php
->when($request->filled('field_id'), fn ($query) => $query->where('field_id', $request->field_id))
->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
->when($request->filled('date'), fn ($query) => $query->whereDate('booking_date', $request->date))
```

### 2. Update Method - Simplify Redirect

**BEFORE:**
```php
$filters = array_filter([
    'field_id' => $request->input('filter_field_id'),
    'status' => $request->input('filter_status'),
    'date' => $request->input('filter_date'),
], fn ($value) => filled($value));

return redirect()->route('admin.bookings.index', $filters)
    ->with('status', 'Status booking diperbarui.');
```

**AFTER:**
```php
return redirect()->route('admin.bookings.index')
    ->with('success', 'Status booking berhasil diperbarui.');
```

### 3. View - Fix Filter Button

**File**: `resources/views/admin/bookings/index.blade.php`

**BEFORE:**
```html
<div class="flex items-end gap-2">
    <x-button variant="primary" class="flex-1">
        Filter
    </x-button>
</div>
```

**AFTER:**
```html
<div class="flex items-end gap-2">
    <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-medium transition">
        Filter
    </button>
</div>
```

## Filter Options

### 1. Filter by Field (Lapangan)
- Dropdown menampilkan semua lapangan
- Select lapangan tertentu untuk lihat booking-nya saja
- Option "Semua Lapangan" untuk reset filter ini

### 2. Filter by Status
- **Pending**: Booking yang menunggu konfirmasi
- **Confirmed**: Booking yang sudah dikonfirmasi
- **Cancelled**: Booking yang dibatalkan
- Option "Semua Status" untuk reset filter ini

### 3. Filter by Date (Tanggal)
- Date picker untuk pilih tanggal tertentu
- Hanya menampilkan booking di tanggal tersebut
- Kosongkan untuk reset filter ini

## Testing

### Test Filter by Field
1. Navigate ke: `http://localhost:8000/admin/bookings`
2. Pilih lapangan dari dropdown "Lapangan"
3. Click "Filter"
4. ✅ Hanya booking untuk lapangan tersebut yang ditampilkan

### Test Filter by Status
1. Pilih status dari dropdown "Status" (misal: Pending)
2. Click "Filter"
3. ✅ Hanya booking dengan status tersebut yang ditampilkan

### Test Filter by Date
1. Pilih tanggal dari date picker
2. Click "Filter"
3. ✅ Hanya booking di tanggal tersebut yang ditampilkan

### Test Multiple Filters
1. Pilih lapangan, status, DAN tanggal
2. Click "Filter"
3. ✅ Hasil ditampilkan berdasarkan kombinasi filter

### Test Reset
1. Click link "Reset" di sebelah judul "Filter Booking"
2. ✅ Semua filter direset dan menampilkan semua booking

## URL Examples

### Filter by Field
```
GET /admin/bookings?field_id=1
```

### Filter by Status
```
GET /admin/bookings?status=pending
```

### Filter by Date
```
GET /admin/bookings?date=2025-10-28
```

### Multiple Filters
```
GET /admin/bookings?field_id=1&status=confirmed&date=2025-10-28
```

## Pagination

Filter tetap aktif saat pindah halaman karena menggunakan `->withQueryString()` di controller.

Contoh:
```
/admin/bookings?field_id=1&status=pending&page=2
```

## Update Status Feature

Pada setiap row booking, admin dapat langsung mengubah status dengan:
1. Click dropdown status di kolom "Status"
2. Pilih status baru
3. ✅ Auto-submit dan status langsung terupdate

Status yang tersedia:
- **Pending** → Menunggu konfirmasi
- **Confirmed** → Sudah dikonfirmasi
- **Cancelled** → Dibatalkan

## Clear Cache

Setelah perubahan, jalankan:

```bash
php artisan config:cache
php artisan view:clear
php artisan route:cache
```

## Files Changed

```
✅ app/Http/Controllers/Admin/BookingController.php
✅ resources/views/admin/bookings/index.blade.php
```

## Verification Checklist

- [x] Filter by field works
- [x] Filter by status works
- [x] Filter by date works
- [x] Multiple filters work together
- [x] Reset filter works
- [x] Pagination preserves filters
- [x] Update status works
- [x] Success message displays
- [x] Cache cleared

## Troubleshooting

### Filter tidak bekerja
1. Check URL parameters di browser address bar
2. Check network tab di browser DevTools
3. Clear browser cache
4. Check Laravel logs: `storage/logs/laravel.log`

### Pagination kehilangan filter
Pastikan controller menggunakan `->withQueryString()`:
```php
->paginate(15)->withQueryString();
```

### Status update tidak bekerja
1. Check form method PATCH tersedia
2. Check validation rules di controller
3. Check dropdown onchange="this.form.submit()"

## Next Steps

Filter sekarang sudah berfungsi normal! Anda dapat:
1. ✅ Filter booking berdasarkan lapangan
2. ✅ Filter booking berdasarkan status
3. ✅ Filter booking berdasarkan tanggal
4. ✅ Kombinasi multiple filters
5. ✅ Update status booking langsung dari tabel

Jika masih ada masalah, check Laravel logs atau browser console.
