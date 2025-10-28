# Test Cases - Futsal Neo S Booking System

## 📋 Testing Checklist

Gunakan checklist ini untuk memastikan semua fitur berfungsi dengan baik.

---

## 🔐 1. AUTHENTICATION & AUTHORIZATION

### 1.1 Login
- [ ] Login dengan email admin1@example.com + password "password"
- [ ] Login dengan email user1@example.com + password "password"
- [ ] Login dengan email/password salah → Harus error
- [ ] Login form memiliki validation error messages
- [ ] Setelah login redirect ke halaman yang sesuai (admin → dashboard admin, user → dashboard user)

### 1.2 Logout
- [ ] Click logout dari navbar/sidebar
- [ ] Session cleared & redirect ke home/login
- [ ] Tidak bisa akses halaman protected setelah logout

### 1.3 Authorization
- [ ] User biasa tidak bisa akses `/admin/*` → redirect atau 403
- [ ] Admin bisa akses semua halaman admin
- [ ] Guest redirect ke login saat akses protected pages

---

## 👤 2. USER DASHBOARD (http://localhost:8000/dashboard)

### 2.1 Statistics Cards
- [ ] Total Booking menampilkan angka yang benar
- [ ] Booking Mendatang menampilkan angka yang benar
- [ ] Booking Selesai menampilkan angka yang benar
- [ ] Total Pengeluaran menampilkan format rupiah yang benar

### 2.2 Next Booking Card
**Jika ada booking mendatang:**
- [ ] Card "Booking Mendatang" muncul dengan info lengkap
- [ ] Nama lapangan ditampilkan
- [ ] Harga ditampilkan dengan format rupiah
- [ ] Tanggal booking ditampilkan (format: dd MMM yyyy)
- [ ] Jam booking ditampilkan (format: HH:mm - HH:mm)
- [ ] Button "Lihat Detail" → redirect ke `/my-bookings`
- [ ] Button "Buat Reminder" → copy text & show notification
- [ ] Notification "✅ Reminder berhasil disalin!" muncul
- [ ] Text reminder ter-copy ke clipboard
- [ ] Paste reminder text → format rapi dengan emoji

**Jika tidak ada booking mendatang:**
- [ ] Card empty state muncul
- [ ] Button "Pesan Sekarang" muncul
- [ ] Button redirect ke halaman schedule/booking

### 2.3 Quick Actions
- [ ] Card "Pesan Lapangan" → redirect ke booking page
- [ ] Card "Booking Saya" → redirect ke `/my-bookings`
- [ ] Card "Profile" → redirect ke profile page
- [ ] Card "Hubungi Kami" → redirect ke contact page

### 2.4 Recent Bookings Sidebar
- [ ] Menampilkan maksimal 5 booking terakhir
- [ ] Setiap booking menampilkan: nama lapangan, tanggal, status, harga
- [ ] Status badge dengan warna yang benar (green=confirmed, yellow=pending, red=cancelled)
- [ ] Click pada booking card → redirect ke `/my-bookings`
- [ ] Link "Lihat Semua Booking" → redirect ke `/my-bookings`

---

## 📅 3. MY BOOKINGS (http://localhost:8000/my-bookings)

### 3.1 Filter Section
- [ ] Filter Status dropdown menampilkan: Semua Status, Pending, Confirmed, Cancelled
- [ ] Filter Dari Tanggal (date picker berfungsi)
- [ ] Filter Sampai Tanggal (date picker berfungsi)
- [ ] Button "Terapkan Filter" submit form
- [ ] Link "Reset" → clear semua filter

### 3.2 Filter by Status
- [ ] Pilih "Pending" → hanya booking pending yang muncul
- [ ] Pilih "Confirmed" → hanya booking confirmed yang muncul
- [ ] Pilih "Cancelled" → hanya booking cancelled yang muncul
- [ ] Pilih "Semua Status" → semua booking muncul
- [ ] URL update dengan parameter `?status=pending`

### 3.3 Filter by Date
- [ ] Set tanggal 2025-10-28 s/d 2025-10-28 → hanya booking tanggal tersebut
- [ ] Set date range 1 minggu → booking dalam 1 minggu muncul
- [ ] Set hanya "Dari Tanggal" → booking dari tanggal tersebut ke depan
- [ ] Set hanya "Sampai Tanggal" → booking sampai tanggal tersebut
- [ ] URL update dengan parameter `?date_from=...&date_to=...`

### 3.4 Filter Kombinasi
- [ ] Status "Pending" + Date range → hasil sesuai kombinasi
- [ ] Semua filter → hasil sesuai kombinasi
- [ ] URL mencakup semua parameter filter

### 3.5 Booking Table (Desktop View)
- [ ] Table menampilkan kolom: Lapangan, Tanggal, Waktu, Harga, Status, Aksi
- [ ] Data booking ditampilkan dengan benar
- [ ] Status badge dengan warna sesuai
- [ ] Harga format rupiah
- [ ] Button "Lihat Detail" pada setiap row

### 3.6 Booking Cards (Mobile View)
- [ ] Resize browser < 768px → table berubah ke cards
- [ ] Setiap card menampilkan info lengkap
- [ ] Status badge terlihat
- [ ] Button "Lihat Detail" di setiap card

### 3.7 Booking Detail Modal
- [ ] Click "Lihat Detail" → modal muncul
- [ ] Modal menampilkan: Booking ID, Lapangan, Lokasi, Status, Tanggal, Waktu, Nama Pemesan, Telepon, Harga, Catatan (jika ada), Dibuat pada
- [ ] Status badge dengan warna yang benar
- [ ] Harga format rupiah
- [ ] Tanggal format Indonesia (dd MMMM yyyy)
- [ ] Click X button → modal close
- [ ] Click area gelap di luar modal → modal close
- [ ] Press ESC → modal close
- [ ] Click button "Tutup" → modal close

### 3.8 Pagination
- [ ] Jika > 10 booking, pagination muncul
- [ ] Click page 2 → data page 2 muncul
- [ ] Filter tetap aktif saat pindah halaman
- [ ] URL include `&page=2`

### 3.9 Empty State
- [ ] Jika tidak ada booking → empty state muncul
- [ ] Message "Belum ada booking ditemukan"
- [ ] Button "Cari Lapangan" muncul

---

## 👨‍💼 4. ADMIN DASHBOARD (http://localhost:8000/admin/dashboard)

### 4.1 Date Range Filter
- [ ] Default: 30 hari terakhir
- [ ] Input "Tanggal Mulai" berfungsi
- [ ] Input "Tanggal Akhir" berfungsi
- [ ] Button "Filter" submit form
- [ ] Button "Reset" → kembali ke 30 hari terakhir
- [ ] URL update dengan parameter

### 4.2 Key Metrics (Filtered by Date Range)
- [ ] Total Pengguna menampilkan angka yang benar
- [ ] Total Lapangan menampilkan angka yang benar
- [ ] Total Booking menampilkan angka yang benar (dalam range)
- [ ] Total Pendapatan menampilkan format rupiah (dalam range)

### 4.3 Quick Stats Cards
- [ ] Menunggu Konfirmasi (pending bookings)
- [ ] Booking Hari Ini
- [ ] Tingkat Okupansi (%)
- [ ] Semua card menampilkan data yang akurat

### 4.4 Revenue Trend Chart
- [ ] Chart muncul menggunakan Chart.js
- [ ] Line chart dengan warna biru
- [ ] Data sesuai dengan date range filter
- [ ] Hover tooltip menampilkan nilai rupiah
- [ ] X-axis: tanggal, Y-axis: rupiah
- [ ] Chart responsive

### 4.5 Booking Trend Chart
- [ ] Chart muncul menggunakan Chart.js
- [ ] Line chart dengan warna hijau
- [ ] Data sesuai dengan date range filter
- [ ] Hover tooltip menampilkan jumlah booking
- [ ] X-axis: tanggal, Y-axis: jumlah
- [ ] Chart responsive

### 4.6 Recent Bookings
- [ ] Menampilkan 10 booking terbaru (dalam range)
- [ ] Info: Lapangan, User, Tanggal, Waktu, Status
- [ ] Status badge dengan warna yang benar
- [ ] Link "Lihat Semua Booking" → `/admin/bookings`

### 4.7 Top Fields
- [ ] Menampilkan 5 lapangan teratas by booking count
- [ ] Progress bar relatif
- [ ] Link "Kelola Lapangan" → `/admin/fields`

### 4.8 Booking Status Distribution
- [ ] Bar chart untuk confirmed, pending, cancelled
- [ ] Persentase ditampilkan
- [ ] Warna berbeda untuk setiap status

### 4.9 Quick Actions
- [ ] Link "Kelola Booking" → `/admin/bookings`
- [ ] Link "Kelola Lapangan" → `/admin/fields`
- [ ] Link "Tambah Lapangan Baru" → `/admin/fields/create`

---

## 📋 5. ADMIN BOOKINGS (http://localhost:8000/admin/bookings)

### 5.1 Filter Section
- [ ] Dropdown Lapangan menampilkan semua lapangan
- [ ] Dropdown Status: Semua, Pending, Confirmed, Cancelled
- [ ] Input Tanggal (date picker)
- [ ] Button "Filter" submit form

### 5.2 Filter Functionality
- [ ] Filter by lapangan → hasil sesuai
- [ ] Filter by status → hasil sesuai
- [ ] Filter by tanggal → hasil sesuai
- [ ] Kombinasi filter → hasil sesuai
- [ ] Reset → semua filter clear

### 5.3 Bookings Table
- [ ] Menampilkan kolom: Tanggal, Lapangan, Waktu, Pemesan, Status, Aksi
- [ ] Data akurat dan lengkap
- [ ] 15 booking per halaman

### 5.4 Status Update (Inline)
- [ ] Dropdown status pada setiap row
- [ ] Change status → auto submit
- [ ] Page refresh → status terupdate
- [ ] Success message muncul

### 5.5 Pagination
- [ ] Pagination muncul jika > 15 bookings
- [ ] Filter preserved saat pindah halaman
- [ ] URL include filter params + page number

---

## 👥 6. ADMIN USERS (http://localhost:8000/admin/users)

### 6.1 Filter Section
- [ ] Input Search (nama/email/telepon)
- [ ] Dropdown Role: Semua, Admin, User
- [ ] Button "Filter" submit
- [ ] Button "Reset" clear filter

### 6.2 Filter by Search
- [ ] Search "admin" → hasil muncul admin users
- [ ] Search "user1" → user1 muncul
- [ ] Search by email → hasil sesuai
- [ ] Search by phone → hasil sesuai

### 6.3 Filter by Role
- [ ] Role "Admin" → hanya admin
- [ ] Role "User" → hanya user
- [ ] Kombinasi search + role → hasil sesuai

### 6.4 Users Table
- [ ] Kolom: Nama, Email, Telepon, Role, Total Booking, Terdaftar
- [ ] Role badge (Admin=blue, User=gray)
- [ ] Total booking ditampilkan
- [ ] Tanggal terdaftar format Indonesia
- [ ] 10 users per halaman

### 6.5 User Actions
- [ ] Button "Tambah User" → `/admin/users/create`
- [ ] Button "Edit" pada setiap row → `/admin/users/{id}/edit`
- [ ] Button "Hapus" → confirmation dialog
- [ ] Tidak bisa hapus akun sendiri

### 6.6 Create User
- [ ] Form dengan field: Nama, Email, Telepon, Role, Password, Konfirmasi Password
- [ ] Validation bekerja (required, email format, phone, password min 8, password match)
- [ ] Submit → redirect ke `/admin/users` dengan success message
- [ ] User baru muncul di list

### 6.7 Edit User
- [ ] Form pre-filled dengan data user
- [ ] Bisa ubah: Nama, Email, Telepon, Role
- [ ] Password optional (kosongkan jika tidak ingin ubah)
- [ ] Submit → update berhasil dengan success message

### 6.8 Delete User
- [ ] Click hapus → confirmation dialog muncul
- [ ] Confirm → user terhapus
- [ ] Success message muncul
- [ ] Tidak bisa hapus akun sendiri (error message)

### 6.9 Pagination
- [ ] Pagination preserved filter
- [ ] URL include filter + page

---

## 🏟️ 7. ADMIN FIELDS (http://localhost:8000/admin/fields)

### 7.1 Fields List
- [ ] Table menampilkan: Nama, Lokasi, Harga/Jam, Status, Aksi
- [ ] Harga format rupiah
- [ ] Status badge (Aktif=green, Tidak Aktif=red)
- [ ] Button "Tambah Lapangan" → create page

### 7.2 Create Field
- [ ] Navigate to `/admin/fields/create`
- [ ] Form fields: Nama Lapangan, Lokasi, Deskripsi, Harga per Jam, Lapangan Aktif (checkbox)
- [ ] Validation: Nama required, Harga required & numeric
- [ ] Submit → redirect dengan success message
- [ ] Lapangan baru muncul di list

### 7.3 Edit Field
- [ ] Click "Edit" → navigate ke edit page
- [ ] Form pre-filled
- [ ] Bisa ubah semua field
- [ ] Checkbox "Lapangan Aktif" berfungsi
- [ ] Submit → update berhasil
- [ ] Success message muncul

### 7.4 Delete Field
- [ ] Click "Hapus" → confirmation dialog
- [ ] Confirm → field terhapus
- [ ] Success message muncul

### 7.5 Button Fix (Create & Edit)
- [ ] Button "Simpan Lapangan" berfungsi (no nested button)
- [ ] Button "Simpan Perubahan" berfungsi
- [ ] Button "Batal" → redirect ke index

---

## 🏠 8. PUBLIC PAGES

### 8.1 Homepage (http://localhost:8000/)
- [ ] Hero section muncul dengan branding "Futsal Neo S"
- [ ] Button "Mulai Booking Sekarang" → schedule page
- [ ] Button "Pelajari Lebih Lanjut" → scroll ke features
- [ ] Features section menampilkan 3+ features
- [ ] Featured fields section (jika ada)
- [ ] Footer dengan copyright "© 2025 Futsal Neo S"

### 8.2 Navbar
- [ ] Logo "Futsal Neo S" → home
- [ ] Menu: Home, Lapangan, Jadwal, Kontak
- [ ] Menu "Booking Saya" (jika login)
- [ ] User dropdown (jika login): Dashboard, Profile, Logout
- [ ] Login/Register button (jika guest)

### 8.3 Footer
- [ ] Links berfungsi
- [ ] Social media links (jika ada)
- [ ] Copyright text "© 2025 Futsal Neo S"

---

## 🎨 9. BRANDING

### 9.1 Check All "Futsal Neo S" References
- [ ] Admin sidebar: "Futsal Neo S Admin"
- [ ] Admin dashboard subtitle: "platform Futsal Neo S"
- [ ] User navbar: "Futsal Neo S"
- [ ] Homepage hero: "Selamat Datang di Futsal Neo S"
- [ ] Features: "Mengapa Pilih Futsal Neo S?"
- [ ] Footer: "© 2025 Futsal Neo S"
- [ ] Browser title: "Futsal Neo S - ..."

### 9.2 No "FutsalGO" Remains
- [ ] Check semua halaman, tidak ada "FutsalGO" lagi
- [ ] View source code → tidak ada "FutsalGO"

---

## 📱 10. RESPONSIVE DESIGN

### 10.1 Desktop (> 1024px)
- [ ] Dashboard layout rapi
- [ ] Tables full width
- [ ] Charts terlihat jelas
- [ ] No horizontal scroll

### 10.2 Tablet (768px - 1024px)
- [ ] Layout menyesuaikan
- [ ] Sidebar collapse (admin)
- [ ] Readable content

### 10.3 Mobile (< 768px)
- [ ] Tables berubah ke cards
- [ ] Filter form stack vertical
- [ ] Buttons full width
- [ ] Charts responsive
- [ ] Modals fit screen
- [ ] Navbar mobile menu berfungsi

---

## 🔔 11. NOTIFICATIONS & ALERTS

### 11.1 Success Messages
- [ ] User created → "User created successfully"
- [ ] User updated → "User updated successfully"
- [ ] User deleted → "User deleted successfully"
- [ ] Field created → "Lapangan berhasil ditambahkan"
- [ ] Field updated → "Lapangan berhasil diperbarui"
- [ ] Field deleted → "Lapangan berhasil dihapus"
- [ ] Booking status updated → "Status booking berhasil diperbarui"

### 11.2 Reminder Notification
- [ ] Dashboard → Click "Buat Reminder" → notification muncul
- [ ] Toast notification "✅ Reminder berhasil disalin!"
- [ ] Auto-hide setelah 3 detik
- [ ] Smooth animation

### 11.3 Error Messages
- [ ] Form validation errors ditampilkan
- [ ] Login error → "Invalid credentials"
- [ ] Cannot delete own account → error message

---

## 🗄️ 12. DATABASE & SEEDER

### 12.1 Data Seeding
- [ ] Run `php artisan migrate:fresh --seed`
- [ ] 10 admin accounts created
- [ ] 50 user accounts created
- [ ] ~478 bookings created
- [ ] 2 fields created
- [ ] Time slots created
- [ ] No errors during seeding

### 12.2 Data Integrity
- [ ] All users have valid email format
- [ ] All users have phone numbers
- [ ] Bookings have relationships (user, field, time_slot)
- [ ] No duplicate bookings for same slot
- [ ] Booking dates spread over 30 days

---

## 🔒 13. SECURITY

### 13.1 Authentication
- [ ] Cannot access protected routes without login
- [ ] Redirect to login page
- [ ] After login → redirect back to intended page

### 13.2 Authorization
- [ ] Users cannot access admin pages
- [ ] Admins can access all pages
- [ ] CSRF protection on forms

### 13.3 Data Security
- [ ] Passwords hashed (not plain text)
- [ ] SQL injection protected (Eloquent ORM)
- [ ] XSS protection (Blade escaping)

---

## ⚡ 14. PERFORMANCE

### 14.1 Page Load Speed
- [ ] Homepage loads < 2 seconds
- [ ] Dashboard loads < 3 seconds
- [ ] No long loading times

### 14.2 Database Queries
- [ ] No N+1 query problems
- [ ] Relationships eager loaded (with())
- [ ] Pagination works efficiently

### 14.3 Charts
- [ ] Charts render smoothly
- [ ] No lag when interacting
- [ ] Tooltips work fast

---

## 🐛 15. ERROR HANDLING

### 15.1 404 Page
- [ ] Access non-existent route → 404 page
- [ ] 404 page styled properly

### 15.2 Validation Errors
- [ ] Form validation shows error messages
- [ ] Error messages clear and helpful
- [ ] Old input preserved

### 15.3 Database Errors
- [ ] Graceful error handling
- [ ] No raw Laravel error pages in production

---

## 📝 16. FINAL CHECKS

### 16.1 Browser Compatibility
- [ ] Chrome: All features work
- [ ] Firefox: All features work
- [ ] Safari: All features work
- [ ] Edge: All features work

### 16.2 Cache Clear
- [ ] `php artisan config:cache`
- [ ] `php artisan view:clear`
- [ ] `php artisan route:cache`
- [ ] All caches cleared

### 16.3 Console Errors
- [ ] Open browser DevTools
- [ ] Check Console tab → No JavaScript errors
- [ ] Check Network tab → No 404/500 errors

### 16.4 All Documentation Files Present
- [ ] SEEDER_INFO.md
- [ ] DASHBOARD_UPDATE.md
- [ ] FIELD_CREATE_FIX.md
- [ ] BOOKING_FILTER_FIX.md
- [ ] MY_BOOKING_DETAIL_FIX.md
- [ ] BRANDING_UPDATE.md
- [ ] DASHBOARD_REMINDER_FIX.md
- [ ] TEST_CASES.md (this file)

---

## 📊 TESTING SUMMARY

**Total Test Cases:** ~200+

**Testing Priority:**
1. 🔴 Critical: Authentication, Filters, CRUD operations
2. 🟡 High: Charts, Modals, Notifications
3. 🟢 Medium: Responsive, Branding, Performance

**Testing Time Estimate:** 2-3 hours for complete testing

---

## ✅ SIGN-OFF

**Tester:** _______________  
**Date:** _______________  
**Result:** Pass / Fail / Needs Fix  
**Notes:**

---

**Happy Testing! 🎉**
