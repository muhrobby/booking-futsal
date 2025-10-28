# 📋 Rencana Refactor UI/UX Booking Futsal

**Tanggal**: 28 Oktober 2025  
**Tujuan**: Meningkatkan pengalaman pengguna dengan memisahkan dashboard admin dan member, serta memperbaiki halaman utama  
**Stack**: Laravel 11, Tailwind CSS, Livewire, Blade Templates

---

## 📑 Daftar Isi

1. [Ringkasan Eksekutif](#ringkasan-eksekutif)
2. [Analisis Saat Ini](#analisis-saat-ini)
3. [Visi & Tujuan](#visi--tujuan)
4. [Rencana Implementasi](#rencana-implementasi)
5. [Timeline & Milestone](#timeline--milestone)
6. [Struktur File Baru](#struktur-file-baru)

---

## 🎯 Ringkasan Eksekutif

Aplikasi Booking Futsal saat ini memiliki desain yang sederhana tetapi belum optimal untuk membedakan pengalaman antara pengguna admin dan member. Refactor ini akan:

-   ✅ Memisahkan dashboard admin dan member dengan UI yang berbeda
-   ✅ Meningkatkan desain halaman utama (landing page)
-   ✅ Membuat navigasi yang lebih intuitif berdasarkan role
-   ✅ Mengoptimalkan responsive design untuk mobile
-   ✅ Menambahkan visual hierarchy yang lebih baik
-   ✅ Meningkatkan aksesibilitas dan user experience

---

## 🔍 Analisis Saat Ini

### Halaman Existings:

-   ✓ **Home**: Hero banner + cta booking
-   ✓ **Dashboard**: Simple welcome message
-   ✓ **Profile**: Profile management
-   ✓ **Admin Routes**: Field management, booking management
-   ✓ **Fields**: List lapangan
-   ✓ **Schedule**: Booking schedule

### Masalah Identifikasi:

| Aspek              | Masalah                                              | Dampak                                  |
| ------------------ | ---------------------------------------------------- | --------------------------------------- |
| **Dashboard**      | Hanya menampilkan welcome message                    | User tidak tahu apa yang bisa dilakukan |
| **Pemisahan Role** | Tidak ada visual distinction antara admin dan member | Confusing user experience               |
| **Landing Page**   | Hero banner sederhana, kurang engaging               | Bounce rate tinggi                      |
| **Navigation**     | Sidebar/navbar kurang responsif untuk mobile         | Poor mobile experience                  |
| **Data Display**   | Belum ada insights/statistics untuk admin            | Admin kesulitan monitoring              |
| **User Actions**   | Member dashboard tidak menampilkan bookings          | User harus navigasi ke halaman lain     |

---

## 🎨 Visi & Tujuan

### Visi:

Menciptakan pengalaman pengguna yang intuitif, menarik, dan role-aware di mana admin dan member memiliki interface dan workflows yang sesuai dengan kebutuhan mereka.

### Tujuan Spesifik:

#### Member Dashboard:

-   Menampilkan list bookings dengan status real-time
-   Quick actions untuk booking baru
-   Riwayat booking dan rating lapangan
-   Notifikasi penting

#### Admin Dashboard:

-   Statistik lapangan (occupancy, revenue, etc.)
-   Management interface untuk fields
-   Management interface untuk bookings
-   Reporting tools
-   User management

#### Landing Page:

-   Hero section yang lebih engaging
-   Feature showcase
-   Social proof (testimonial)
-   Call-to-action yang jelas

---

## 🛠️ Rencana Implementasi

### PHASE 1: Persiapan & Struktur (Week 1)

#### 1.1 Setup Struktur Views

-   [ ] Buat layout terpisah untuk member dan admin
-   [ ] Buat reusable components yang comprehensive
-   [ ] Setup color system dan typography variables

**Files to create:**

```
resources/views/layouts/
  ├── app.blade.php (member layout - default)
  ├── admin.blade.php (admin layout)
  └── guest.blade.php (public layout)

resources/views/components/
  ├── navbar.blade.php
  ├── sidebar.blade.php (admin-specific)
  ├── header.blade.php
  ├── card.blade.php
  ├── button.blade.php
  └── stats-card.blade.php
```

#### 1.2 Create Tailwind Configuration

-   [ ] Extend tailwind.config.js dengan custom colors dan spacing
-   [ ] Define typography scale
-   [ ] Setup animation utilities

**File to update:**

```
tailwind.config.js
```

---

### PHASE 2: Landing Page Redesign (Week 1-2)

#### 2.1 Hero Section Enhancement

-   [ ] Buat hero banner dengan gradient dan illustration
-   [ ] Tambahkan animated elements
-   [ ] Optimize CTA buttons

**Features:**

-   Background gradient atau hero image
-   Hero text dengan animation
-   Multiple CTA buttons (untuk guest vs authenticated)
-   Breadcrumb navigation

**File to create:**

```
resources/views/home.blade.php (refactor)
```

#### 2.2 Landing Page Sections

-   [ ] Fitur section dengan cards
-   [ ] Pricing/lapangan showcase
-   [ ] Testimonial section
-   [ ] FAQ section
-   [ ] Footer enhancement

**Components:**

-   Feature cards dengan icons
-   Lapangan showcase grid
-   Testimonial carousel
-   FAQ accordion

---

### PHASE 3: Member Dashboard (Week 2-3)

#### 3.1 Dashboard Overview

-   [ ] Buat dashboard.blade.php yang menampilkan welcome + quick stats
-   [ ] Tambahkan quick actions
-   [ ] Responsive layout untuk mobile

**Content:**

-   Welcome greeting dengan nama user
-   Quick stats: Total bookings, Last booking, Next booking
-   Recent bookings list
-   Quick action buttons (Book now, View profile)

**File to create/update:**

```
resources/views/dashboard.blade.php (refactor)
```

#### 3.2 My Bookings Page

-   [ ] Buat halaman dedicated untuk bookings
-   [ ] Filter & search functionality
-   [ ] Booking card dengan action buttons
-   [ ] Status badge untuk each booking

**Fitur:**

-   List bookings dengan pagination
-   Filter by status (upcoming, completed, cancelled)
-   Search by field name atau date
-   Action buttons (cancel, reschedule, rate)
-   Booking detail modal

**Files to create:**

```
resources/views/bookings/
  ├── index.blade.php (my bookings)
  ├── show.blade.php (detail)
  └── components/
      └── booking-card.blade.php
```

#### 3.3 Profile & Settings

-   [ ] Enhance profile page dengan more details
-   [ ] Add settings tab
-   [ ] Add notification preferences

**File to update:**

```
resources/views/profile.blade.php (refactor)
```

---

### PHASE 4: Admin Dashboard (Week 3-4)

#### 4.1 Admin Dashboard Overview

-   [ ] Buat admin dashboard dengan statistics
-   [ ] Revenue chart
-   [ ] Occupancy chart
-   [ ] Recent activities

**Features:**

-   Key metrics: Total revenue, occupancy rate, total bookings
-   Charts: Revenue over time, occupancy by field
-   Recent bookings/activities
-   Quick access to management sections

**File to create:**

```
resources/views/admin/dashboard.blade.php
```

#### 4.2 Admin Layout & Navigation

-   [ ] Buat admin.blade.php layout dengan sidebar
-   [ ] Top navigation bar
-   [ ] Breadcrumb
-   [ ] User profile dropdown

**Components:**

-   Admin sidebar dengan menu items
-   Top navbar dengan search dan user menu
-   Breadcrumb navigation
-   Responsive mobile menu

**Files to create:**

```
resources/views/layouts/admin.blade.php
resources/views/components/admin/
  ├── sidebar.blade.php
  ├── navbar.blade.php
  └── breadcrumb.blade.php
```

#### 4.3 Field Management Page

-   [ ] Redesign field management interface
-   [ ] Table dengan edit/delete actions
-   [ ] Add new field modal/form
-   [ ] Field card view & table view toggle

**Features:**

-   Data table dengan sorting dan pagination
-   Bulk actions
-   Quick edit inline
-   Field image display
-   Status badge

**File to update:**

```
resources/views/admin/fields/
  ├── index.blade.php (refactor)
  ├── create.blade.php (create)
  ├── edit.blade.php (create)
  └── components/
      └── field-table.blade.php
```

#### 4.4 Booking Management Page

-   [ ] Redesign booking management interface
-   [ ] Advanced filtering
-   [ ] Status update actions
-   [ ] Booking details modal

**Features:**

-   Interactive data table
-   Filter by field, status, date range
-   Bulk status update
-   Booking detail modal
-   Export functionality (future)

**File to update:**

```
resources/views/admin/bookings/
  ├── index.blade.php (refactor)
  └── components/
      └── booking-table.blade.php
```

#### 4.5 User Management Page (Future)

-   [ ] Buat halaman untuk manage users
-   [ ] View user details
-   [ ] Change user role
-   [ ] Block/unblock users

**File to create:**

```
resources/views/admin/users/
  ├── index.blade.php
  └── show.blade.php
```

---

### PHASE 5: Component Library & Shared Components (Week 4)

#### 5.1 Button Component

```blade
<x-button variant="primary|secondary|danger" size="sm|md|lg">
  {{ $slot }}
</x-button>
```

#### 5.2 Card Component

```blade
<x-card class="...">
  <x-slot:header>Title</x-slot:header>
  <x-slot:body>Content</x-slot:body>
  <x-slot:footer>Footer</x-slot:footer>
</x-card>
```

#### 5.3 Modal Component

```blade
<x-modal :show="$show" @close="...">
  <x-slot:header>Title</x-slot:header>
  <x-slot:body>Content</x-slot:body>
  <x-slot:footer>Actions</x-slot:footer>
</x-modal>
```

#### 5.4 Data Table Component

```blade
<x-data-table :items="$items" :columns="$columns" :pagination="true">
</x-data-table>
```

**Files to create:**

```
resources/views/components/
  ├── button.blade.php
  ├── card.blade.php
  ├── modal.blade.php
  ├── data-table.blade.php
  ├── badge.blade.php
  ├── alert.blade.php
  ├── form/
  │   ├── input.blade.php
  │   ├── select.blade.php
  │   ├── textarea.blade.php
  │   └── checkbox.blade.php
  └── icons/
      ├── check.blade.php
      ├── close.blade.php
      ├── menu.blade.php
      └── ...
```

---

### PHASE 6: Routing & Controllers Update (Week 4-5)

#### 6.1 Routing Structure

```php
// Member routes
Route::middleware('auth', 'role:member')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'member'])->name('dashboard');
    Route::resource('/bookings', BookingController::class);
    // ...
});

// Admin routes
Route::middleware('auth', 'role:admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('/fields', AdminFieldController::class);
    Route::resource('/bookings', AdminBookingController::class);
    Route::resource('/users', AdminUserController::class);
});
```

#### 6.2 Middleware Creation

-   [ ] Create role middleware untuk filter based on role
-   [ ] Create admin middleware
-   [ ] Create member middleware

**File to create:**

```
app/Http/Middleware/
  ├── CheckRole.php
  └── CheckAdmin.php
```

#### 6.3 Controller Enhancement

-   [ ] Add AdminDashboardController
-   [ ] Refactor BookingController
-   [ ] Refactor FieldController
-   [ ] Add more methods untuk data display

**Files to create/update:**

```
app/Http/Controllers/
  ├── DashboardController.php (create)
  ├── Admin/
  │   ├── DashboardController.php (create)
  │   └── ... (refactor existing)
  └── ... (refactor existing)
```

---

### PHASE 7: Testing & Refinement (Week 5)

#### 7.1 UI Testing

-   [ ] Test responsive design di berbagai devices
-   [ ] Test browser compatibility
-   [ ] Test accessibility (WCAG 2.1 AA)

#### 7.2 UX Testing

-   [ ] User flow testing
-   [ ] Navigation testing
-   [ ] Performance testing

#### 7.3 Bug Fixes & Refinement

-   [ ] Fix responsive issues
-   [ ] Optimize images
-   [ ] Improve load times

---

## 📅 Timeline & Milestone

| Week   | Phase     | Milestone                                     | Status |
| ------ | --------- | --------------------------------------------- | ------ |
| Week 1 | 1 + 2     | Setup complete, Landing page ready            | ⏳     |
| Week 2 | 2 + 3     | Landing page finalized, Member dashboard MVP  | ⏳     |
| Week 3 | 3 + 4     | My bookings complete, Admin dashboard started | ⏳     |
| Week 4 | 4 + 5 + 6 | Admin features done, Components library ready | ⏳     |
| Week 5 | 7         | Testing complete, Launch ready                | ⏳     |

---

## 📁 Struktur File Baru

```
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php              [NEW] Member default layout
│   │   ├── admin.blade.php            [NEW] Admin layout
│   │   ├── guest.blade.php            [UPDATE] Refactor
│   │   └── ...
│   │
│   ├── components/
│   │   ├── navbar.blade.php           [NEW]
│   │   ├── sidebar.blade.php          [NEW]
│   │   ├── button.blade.php           [NEW]
│   │   ├── card.blade.php             [NEW]
│   │   ├── modal.blade.php            [NEW]
│   │   ├── data-table.blade.php       [NEW]
│   │   ├── badge.blade.php            [NEW]
│   │   ├── alert.blade.php            [NEW]
│   │   ├── stats-card.blade.php       [NEW]
│   │   ├── form/
│   │   │   ├── input.blade.php        [NEW]
│   │   │   ├── select.blade.php       [NEW]
│   │   │   ├── textarea.blade.php     [NEW]
│   │   │   └── checkbox.blade.php     [NEW]
│   │   ├── admin/
│   │   │   ├── sidebar.blade.php      [NEW]
│   │   │   ├── navbar.blade.php       [NEW]
│   │   │   └── breadcrumb.blade.php   [NEW]
│   │   └── icons/
│   │       ├── check.blade.php        [NEW]
│   │       ├── close.blade.php        [NEW]
│   │       └── ...
│   │
│   ├── home.blade.php                 [UPDATE] Redesign
│   ├── dashboard.blade.php            [UPDATE] Member dashboard refactor
│   ├── profile.blade.php              [UPDATE] Enhance
│   │
│   ├── bookings/
│   │   ├── index.blade.php            [NEW] My bookings list
│   │   ├── show.blade.php             [NEW] Booking detail
│   │   ├── create.blade.php           [UPDATE] Enhance
│   │   ├── edit.blade.php             [UPDATE] Enhance
│   │   └── components/
│   │       └── booking-card.blade.php [NEW]
│   │
│   ├── admin/
│   │   ├── dashboard.blade.php        [NEW] Admin dashboard
│   │   ├── fields/
│   │   │   ├── index.blade.php        [UPDATE]
│   │   │   ├── create.blade.php       [UPDATE]
│   │   │   ├── edit.blade.php         [UPDATE]
│   │   │   └── components/
│   │   │       └── field-table.blade.php [NEW]
│   │   ├── bookings/
│   │   │   ├── index.blade.php        [UPDATE]
│   │   │   └── components/
│   │   │       └── booking-table.blade.php [NEW]
│   │   └── users/
│   │       ├── index.blade.php        [NEW]
│   │       └── show.blade.php         [NEW]
│   │
│   └── ... (existing)
│
├── css/
│   └── app.css                        [UPDATE] Add custom styles
│
└── js/
    └── app.js                         [UPDATE] Add interactivity

app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php    [NEW]
│   │   ├── Admin/
│   │   │   ├── DashboardController.php [NEW]
│   │   │   └── ... (enhance existing)
│   │   └── ... (refactor existing)
│   │
│   └── Middleware/
│       ├── CheckRole.php              [NEW]
│       └── CheckAdmin.php             [NEW]
│
├── Models/
│   └── ... (may need enhancements)
│
└── ... (existing)

tailwind.config.js                     [UPDATE] Enhance config
```

---

## 🎯 Design System

### Color Palette

```css
/* Primary Colors */
--primary: #3B82F6      /* Blue */
--primary-dark: #1E40AF
--primary-light: #DBEAFE

/* Secondary Colors */
--secondary: #10B981    /* Emerald/Green */
--secondary-dark: #047857
--secondary-light: #D1FAE5

/* Status Colors */
--success: #10B981
--warning: #F59E0B
--danger: #EF4444
--info: #3B82F6

/* Neutral Colors */
--gray-50: #F9FAFB
--gray-100: #F3F4F6
--gray-200: #E5E7EB
--gray-300: #D1D5DB
--gray-400: #9CA3AF
--gray-500: #6B7280
--gray-600: #4B5563
--gray-700: #374151
--gray-800: #1F2937
--gray-900: #111827
```

### Typography

```css
/* Headings */
h1: 2rem (32px) - Bold
h2: 1.75rem (28px) - Bold
h3: 1.5rem (24px) - Semibold
h4: 1.25rem (20px) - Semibold

/* Body Text */
Body: 1rem (16px) - Regular
Small: 0.875rem (14px) - Regular
XSmall: 0.75rem (12px) - Regular

Font Family: Inter / System Font Stack
```

### Spacing Scale

```css
0: 0
1: 0.25rem (4px)
2: 0.5rem (8px)
3: 0.75rem (12px)
4: 1rem (16px)
6: 1.5rem (24px)
8: 2rem (32px)
12: 3rem (48px)
16: 4rem (64px)
```

---

## 🔐 Security Considerations

-   [ ] Implement role-based access control (RBAC) middleware
-   [ ] Validate user permissions on every action
-   [ ] Sanitize user inputs
-   [ ] Protect against CSRF attacks
-   [ ] Implement rate limiting for sensitive actions
-   [ ] Use HTTPS for all communications

---

## ♿ Accessibility Standards

-   [ ] WCAG 2.1 Level AA compliance
-   [ ] Semantic HTML structure
-   [ ] Proper color contrast ratios
-   [ ] Keyboard navigation support
-   [ ] ARIA labels untuk interactive elements
-   [ ] Alt text untuk images
-   [ ] Form labels dan error messages yang jelas

---

## 📊 Performance Targets

-   [ ] First Contentful Paint (FCP): < 1.5s
-   [ ] Largest Contentful Paint (LCP): < 2.5s
-   [ ] Cumulative Layout Shift (CLS): < 0.1
-   [ ] Time to Interactive (TTI): < 3.5s
-   [ ] Lighthouse Score: 90+

---

## 🚀 Future Enhancements (Phase 2)

-   [ ] Dark mode support
-   [ ] Multi-language support (i18n)
-   [ ] Advanced analytics & reporting
-   [ ] Mobile app (React Native)
-   [ ] API optimization & caching
-   [ ] Real-time notifications (Pusher/WebSockets)
-   [ ] Payment integration
-   [ ] Rating & review system
-   [ ] Email notifications
-   [ ] SMS notifications

---

## 📞 Notes & References

### Tech Stack Used:

-   Laravel 11
-   Tailwind CSS 3.1
-   Livewire (optional enhancements)
-   Blade Templates
-   Vite (build tool)

### Best Practices:

-   Use Tailwind utility classes untuk styling
-   Implement reusable Blade components
-   Follow Laravel naming conventions
-   Keep controllers lean, move logic to services
-   Write tests for critical features

### Recommended Tools:

-   Figma (untuk mockups)
-   Tailwind CSS IntelliSense (VS Code extension)
-   Laravel Debugbar
-   Lighthouse (Chrome DevTools)

---

**Document Version**: 1.0  
**Last Updated**: 28 Oktober 2025  
**Status**: ✅ Ready for Review
