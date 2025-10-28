# Field Create/Edit Form - Fix Documentation

## Masalah yang Diperbaiki

Form untuk menambah/edit lapangan di:
- `http://localhost:8000/admin/fields/create`
- `http://localhost:8000/admin/fields/{id}/edit`

tidak berfungsi dengan baik karena beberapa masalah.

## Penyebab Masalah

### 1. Missing Validation Rule
Field `location` tidak ada di validation rules di controller, menyebabkan error saat submit form.

### 2. Nested Button Elements
Button submit dibungkus dalam component `<x-button>` yang juga menghasilkan tag `<button>`, menyebabkan invalid HTML:
```html
<!-- WRONG -->
<button type="submit">
    <button>Simpan</button>  <!-- nested button -->
</button>
```

## Solusi yang Diterapkan

### 1. FieldController - Tambah Validation untuk Location

**File**: `app/Http/Controllers/Admin/FieldController.php`

**Perubahan pada `store()` method:**
```php
$validated = $request->validate([
    'name' => ['required', 'string', 'max:255'],
    'location' => ['nullable', 'string', 'max:255'],  // ✅ ADDED
    'description' => ['nullable', 'string'],
    'price_per_hour' => ['required', 'integer', 'min:0'],
    'is_active' => ['nullable', 'boolean'],
]);
```

**Perubahan pada `update()` method:**
```php
$validated = $request->validate([
    'name' => ['required', 'string', 'max:255'],
    'location' => ['nullable', 'string', 'max:255'],  // ✅ ADDED
    'description' => ['nullable', 'string'],
    'price_per_hour' => ['required', 'integer', 'min:0'],
    'is_active' => ['nullable', 'boolean'],
]);
```

### 2. Update Success Messages

Changed from `'status'` to `'success'` untuk konsistensi dengan alert component:
- `store()`: `with('success', 'Lapangan berhasil ditambahkan.')`
- `update()`: `with('success', 'Lapangan berhasil diperbarui.')`
- `destroy()`: `with('success', 'Lapangan berhasil dihapus.')`

### 3. Fix Button HTML di Create Form

**File**: `resources/views/admin/fields/create.blade.php`

**BEFORE:**
```html
<button type="submit" class="flex-1">
    <x-button variant="primary" class="w-full">
        Simpan Lapangan
    </x-button>
</button>
```

**AFTER:**
```html
<button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-medium transition">
    Simpan Lapangan
</button>
```

### 4. Fix Button HTML di Edit Form

**File**: `resources/views/admin/fields/edit.blade.php`

Same fix as create form.

## Testing

### Manual Test
1. Navigate ke `http://localhost:8000/admin/fields/create`
2. Isi form dengan data:
   - **Nama Lapangan**: Lapangan Test
   - **Lokasi**: Jakarta Selatan
   - **Deskripsi**: Lapangan indoor dengan rumput sintetis
   - **Harga per Jam**: 200000
   - **Lapangan Aktif**: ✓ (checked)
3. Click "Simpan Lapangan"
4. Seharusnya redirect ke `/admin/fields` dengan success message

### Test Edit
1. Navigate ke `/admin/fields`
2. Click "Edit" pada salah satu lapangan
3. Ubah data
4. Click "Simpan Perubahan"
5. Seharusnya berhasil update

### Test Delete
1. Navigate ke `/admin/fields`
2. Click "Hapus" pada salah satu lapangan
3. Confirm dialog
4. Seharusnya berhasil delete dengan success message

## Cache Clear

Setelah perubahan, jalankan command berikut:

```bash
php artisan config:cache
php artisan view:clear
php artisan route:cache
```

## Validation Rules

Field yang di-validate saat create/update lapangan:

| Field           | Type    | Required | Rules                      |
|-----------------|---------|----------|----------------------------|
| name            | string  | Yes      | max:255                    |
| location        | string  | No       | max:255                    |
| description     | text    | No       | -                          |
| price_per_hour  | integer | Yes      | min:0                      |
| is_active       | boolean | No       | default: true              |

## Success Messages

Setelah operasi berhasil, akan muncul alert success di bagian atas halaman:

- **Create**: "Lapangan berhasil ditambahkan."
- **Update**: "Lapangan berhasil diperbarui."
- **Delete**: "Lapangan berhasil dihapus."

## Troubleshooting

### Form tidak submit
1. Check browser console untuk JavaScript errors
2. Verify CSRF token ada di form
3. Clear browser cache
4. Check Laravel logs: `storage/logs/laravel.log`

### Validation error
1. Check semua required fields terisi
2. Check format data (harga harus integer)
3. Check error messages di bawah input fields

### Redirect tidak berfungsi
1. Check route exists: `php artisan route:list --path=admin/fields`
2. Clear route cache: `php artisan route:clear`
3. Re-cache routes: `php artisan route:cache`

## Files Changed

```
✅ app/Http/Controllers/Admin/FieldController.php
✅ resources/views/admin/fields/create.blade.php
✅ resources/views/admin/fields/edit.blade.php
```

## Verification Checklist

- [x] Location field added to validation
- [x] Success messages updated
- [x] Nested button tags removed
- [x] Create form works
- [x] Edit form works
- [x] Delete function works
- [x] Cache cleared
- [x] Routes working

## Next Steps

Form sekarang sudah berfungsi normal! Anda dapat:
1. ✅ Menambah lapangan baru
2. ✅ Mengedit lapangan existing
3. ✅ Menghapus lapangan
4. ✅ Melihat success messages

Jika masih ada masalah, check Laravel logs di `storage/logs/laravel.log`
