# 📋 Project Structure & Checklist

## Struktur Folder Hasil Refactor

```
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php                          ✨ BARU - Member default layout
│   │   ├── admin.blade.php                        ✨ BARU - Admin layout dengan sidebar
│   │   └── guest.blade.php                        🔄 UPDATE - Refactor jika ada
│   │
│   ├── components/
│   │   ├── navbar.blade.php                       ✨ BARU - Member/guest navbar
│   │   ├── button.blade.php                       ✨ BARU - Reusable button component
│   │   ├── card.blade.php                         ✨ BARU - Card wrapper component
│   │   ├── modal.blade.php                        ✨ BARU - Modal dialog component
│   │   ├── data-table.blade.php                   ✨ BARU - Reusable data table
│   │   ├── badge.blade.php                        ✨ BARU - Status/tag badges
│   │   ├── alert.blade.php                        ✨ BARU - Alert messages
│   │   ├── stats-card.blade.php                   ✨ BARU - Statistics card
│   │   ├── pagination.blade.php                   ✨ BARU - Custom pagination
│   │   │
│   │   ├── form/
│   │   │   ├── input.blade.php                    ✨ BARU
│   │   │   ├── select.blade.php                   ✨ BARU
│   │   │   ├── textarea.blade.php                 ✨ BARU
│   │   │   ├── checkbox.blade.php                 ✨ BARU
│   │   │   └── file-input.blade.php               ✨ BARU
│   │   │
│   │   ├── admin/
│   │   │   ├── sidebar.blade.php                  ✨ BARU - Admin sidebar navigation
│   │   │   ├── navbar.blade.php                   ✨ BARU - Admin top navbar
│   │   │   ├── breadcrumb.blade.php               ✨ BARU - Breadcrumb navigation
│   │   │   └── user-menu.blade.php                ✨ BARU - Admin user dropdown menu
│   │   │
│   │   └── icons/
│   │       ├── check.blade.php                    ✨ BARU
│   │       ├── close.blade.php                    ✨ BARU
│   │       ├── menu.blade.php                     ✨ BARU
│   │       ├── chevron-down.blade.php             ✨ BARU
│   │       └── ... (other icons)
│   │
│   ├── home.blade.php                             🔄 UPDATE - Redesign hero section & features
│   ├── dashboard.blade.php                        🔄 UPDATE - Member dashboard dengan stats & quick actions
│   ├── profile.blade.php                          🔄 UPDATE - Enhance dengan more sections
│   │
│   ├── bookings/
│   │   ├── index.blade.php                        ✨ BARU - List my bookings page
│   │   ├── show.blade.php                         ✨ BARU - Booking detail page
│   │   ├── create.blade.php                       🔄 UPDATE - Enhance form UI
│   │   ├── edit.blade.php                         🔄 UPDATE - Enhance form UI
│   │   └── components/
│   │       ├── booking-card.blade.php             ✨ BARU - Booking card component
│   │       ├── booking-filters.blade.php          ✨ BARU - Filter component
│   │       └── status-badge.blade.php             ✨ BARU - Status display
│   │
│   ├── admin/
│   │   ├── dashboard.blade.php                    ✨ BARU - Admin dashboard dengan charts & stats
│   │   │
│   │   ├── fields/
│   │   │   ├── index.blade.php                    🔄 UPDATE - Redesign dengan table & cards toggle
│   │   │   ├── create.blade.php                   🔄 UPDATE - Enhance form
│   │   │   ├── edit.blade.php                     🔄 UPDATE - Enhance form
│   │   │   └── components/
│   │   │       └── field-table.blade.php          ✨ BARU - Fields management table
│   │   │
│   │   ├── bookings/
│   │   │   ├── index.blade.php                    🔄 UPDATE - Interactive table dengan filters
│   │   │   └── components/
│   │   │       ├── booking-table.blade.php        ✨ BARU - Bookings management table
│   │   │       └── filters.blade.php              ✨ BARU - Advanced filters
│   │   │
│   │   └── users/
│   │       ├── index.blade.php                    ✨ BARU - Users management list
│   │       └── show.blade.php                     ✨ BARU - User detail/edit
│   │
│   ├── fields/
│   │   ├── index.blade.php                        🔄 UPDATE - Better layout
│   │   └── show.blade.php                         ✨ BARU - Jika belum ada
│   │
│   ├── schedule/
│   │   ├── index.blade.php                        🔄 UPDATE - Enhance UI
│   │   └── components/
│   │       ├── calendar.blade.php                 ✨ BARU (optional)
│   │       └── time-slots.blade.php               ✨ BARU
│   │
│   ├── contact.blade.php                          (Keep existing)
│   ├── welcome.blade.php                          (Legacy - bisa dihapus)
│   └── livewire/
│       └── ... (Existing livewire components)
│
├── css/
│   ├── app.css                                    🔄 UPDATE - Add custom classes
│   └── admin.css                                  ✨ BARU (optional) - Admin-specific styles
│
└── js/
    ├── app.js                                     🔄 UPDATE - Add interactivity
    └── components/
        ├── modal.js                               ✨ BARU (optional)
        ├── sidebar.js                             ✨ BARU (optional)
        └── table.js                               ✨ BARU (optional)

app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php                ✨ BARU - Handle member dashboard
│   │   ├── BookingController.php                  🔄 UPDATE - Add more methods
│   │   ├── FieldController.php                    🔄 UPDATE - Enhance
│   │   ├── ScheduleController.php                 🔄 UPDATE - Enhance
│   │   │
│   │   └── Admin/
│   │       ├── DashboardController.php            ✨ BARU - Admin dashboard with stats
│   │       ├── FieldController.php                🔄 UPDATE - Enhance admin field management
│   │       ├── BookingController.php              🔄 UPDATE - Enhance admin booking management
│   │       ├── UserController.php                 ✨ BARU - User management (future)
│   │       └── ReportController.php               ✨ BARU (optional) - Reporting
│   │
│   ├── Middleware/
│   │   ├── CheckRole.php                          ✨ BARU - Check user role
│   │   ├── CheckAdmin.php                         ✨ BARU - Check admin role
│   │   └── ... (existing middlewares)
│   │
│   └── Requests/
│       ├── StoreBookingRequest.php                🔄 UPDATE (if exists)
│       ├── StoreFieldRequest.php                  🔄 UPDATE (if exists)
│       └── ... (existing requests)
│
├── Models/
│   ├── User.php                                   (Keep - might add methods)
│   ├── Booking.php                                (Keep - might add scopes)
│   ├── Field.php                                  (Keep)
│   └── TimeSlot.php                               (Keep)
│
├── Services/ (optional)
│   ├── BookingService.php                         ✨ BARU (recommended)
│   ├── FieldService.php                           ✨ BARU (recommended)
│   └── DashboardService.php                       ✨ BARU (recommended)
│
└── View/
    └── Components/
        └── ... (existing components)

routes/
├── web.php                                        🔄 UPDATE - Add new routes & organize
├── auth.php                                       (Keep)
└── admin.php                                      ✨ BARU (optional) - Separate admin routes

tailwind.config.js                                 🔄 UPDATE - Add custom colors & animations

```

---

## 📊 Implementation Checklist

### PHASE 1: Setup & Configuration (Week 1)

-   [ ] Update `tailwind.config.js` dengan custom colors
-   [ ] Create `resources/views/layouts/app.blade.php`
-   [ ] Create `resources/views/layouts/admin.blade.php`
-   [ ] Create base components:
    -   [ ] `navbar.blade.php`
    -   [ ] `button.blade.php`
    -   [ ] `card.blade.php`
    -   [ ] `stats-card.blade.php`
-   [ ] Create admin components:
    -   [ ] `admin/sidebar.blade.php`
    -   [ ] `admin/navbar.blade.php`
    -   [ ] `admin/breadcrumb.blade.php`
-   [ ] Create form components
-   [ ] Test layouts and components

**Subtotal**: 13 tasks

---

### PHASE 2: Landing Page (Week 1-2)

-   [ ] Redesign `home.blade.php`
    -   [ ] Hero section with gradient
    -   [ ] Features section
    -   [ ] Popular fields section
    -   [ ] CTA section
-   [ ] Update `HomeController`
-   [ ] Test responsive design on mobile
-   [ ] Optimize hero images/illustrations
-   [ ] Add smooth scrolling

**Subtotal**: 8 tasks

---

### PHASE 3: Member Dashboard & Bookings (Week 2-3)

-   [ ] Redesign `dashboard.blade.php`
    -   [ ] Welcome section
    -   [ ] Quick stats
    -   [ ] Quick actions cards
    -   [ ] Recent bookings section
-   [ ] Create `DashboardController`
-   [ ] Create bookings section:
    -   [ ] `bookings/index.blade.php` (My bookings)
    -   [ ] `bookings/show.blade.php` (Booking detail)
    -   [ ] `bookings/components/booking-card.blade.php`
    -   [ ] `bookings/components/booking-filters.blade.php`
-   [ ] Create `BookingController` methods if needed
-   [ ] Add filtering functionality to bookings list
-   [ ] Add pagination to bookings
-   [ ] Test member workflows

**Subtotal**: 11 tasks

---

### PHASE 4: Admin Dashboard (Week 3-4)

-   [ ] Create `Admin/DashboardController`
-   [ ] Create `admin/dashboard.blade.php`
    -   [ ] Key metrics cards
    -   [ ] Revenue chart
    -   [ ] Occupancy chart
    -   [ ] Recent activities
-   [ ] Redesign field management:
    -   [ ] Update `admin/fields/index.blade.php`
    -   [ ] Update `admin/fields/create.blade.php`
    -   [ ] Update `admin/fields/edit.blade.php`
    -   [ ] Create field table component
-   [ ] Redesign booking management:
    -   [ ] Update `admin/bookings/index.blade.php`
    -   [ ] Create advanced filters
    -   [ ] Add bulk actions
-   [ ] Create user management (future):
    -   [ ] Create `admin/users/index.blade.php`
    -   [ ] Create `admin/users/show.blade.php`
    -   [ ] Create `Admin/UserController`
-   [ ] Add admin middleware for authorization
-   [ ] Test admin workflows

**Subtotal**: 16 tasks

---

### PHASE 5: Components Library (Week 4)

-   [ ] Create modal component
-   [ ] Create data-table component
-   [ ] Create badge component
-   [ ] Create alert component
-   [ ] Create pagination component
-   [ ] Create icon components (check, close, etc)
-   [ ] Create additional form components
-   [ ] Document component usage

**Subtotal**: 8 tasks

---

### PHASE 6: Routing & Middleware (Week 4)

-   [ ] Update `routes/web.php` with new routes
-   [ ] Create `CheckRole` middleware
-   [ ] Create `CheckAdmin` middleware
-   [ ] Organize routes by role
-   [ ] Test route access controls

**Subtotal**: 5 tasks

---

### PHASE 7: Enhancement & Polish (Week 4-5)

-   [ ] Add animations and transitions
-   [ ] Optimize images and assets
-   [ ] Add loading states
-   [ ] Add success/error messages
-   [ ] Add empty states
-   [ ] Improve form validation messages
-   [ ] Add tooltips where needed
-   [ ] Test all interactive elements

**Subtotal**: 8 tasks

---

### PHASE 8: Testing & QA (Week 5)

-   [ ] Responsive design testing (mobile, tablet, desktop)
-   [ ] Browser compatibility testing (Chrome, Firefox, Safari, Edge)
-   [ ] Accessibility testing (WCAG 2.1 AA)
-   [ ] Performance testing (Lighthouse)
-   [ ] User flow testing
-   [ ] Navigation testing
-   [ ] Cross-device testing
-   [ ] Bug fixes and refinements

**Subtotal**: 8 tasks

---

## 📈 Total Summary

| Phase               | Tasks  | Est. Days    | Status |
| ------------------- | ------ | ------------ | ------ |
| 1. Setup            | 13     | 3            | ⏳     |
| 2. Landing Page     | 8      | 3            | ⏳     |
| 3. Member Dashboard | 11     | 4            | ⏳     |
| 4. Admin Dashboard  | 16     | 5            | ⏳     |
| 5. Components       | 8      | 2            | ⏳     |
| 6. Routing          | 5      | 2            | ⏳     |
| 7. Enhancement      | 8      | 3            | ⏳     |
| 8. Testing          | 8      | 3            | ⏳     |
| **TOTAL**           | **77** | **~25 days** | ⏳     |

---

## 🎯 Key Milestones

### ✅ Milestone 1: Foundation Complete

**Timeline**: End of Week 1

-   [ ] All layouts created and tested
-   [ ] Base components working
-   [ ] Tailwind configured

### ✅ Milestone 2: Member Experience Complete

**Timeline**: End of Week 2

-   [ ] Landing page redesigned
-   [ ] Member dashboard functional
-   [ ] Bookings list working

### ✅ Milestone 3: Admin Experience Complete

**Timeline**: End of Week 4

-   [ ] Admin dashboard functional
-   [ ] Field management enhanced
-   [ ] Booking management enhanced

### ✅ Milestone 4: Production Ready

**Timeline**: End of Week 5

-   [ ] All tests passing
-   [ ] No critical bugs
-   [ ] Performance optimized
-   [ ] Accessibility compliant

---

## 🚀 Deployment Checklist

Before deploying to production:

### Code Quality

-   [ ] No console errors/warnings
-   [ ] No TypeScript/ESLint errors
-   [ ] Code follows naming conventions
-   [ ] Components are properly documented
-   [ ] No unused code/imports

### Performance

-   [ ] Lighthouse score 90+
-   [ ] First Contentful Paint < 1.5s
-   [ ] Largest Contentful Paint < 2.5s
-   [ ] Cumulative Layout Shift < 0.1
-   [ ] Images are optimized

### Security

-   [ ] CSRF protection in forms
-   [ ] Input validation on all forms
-   [ ] SQL injection prevention
-   [ ] XSS prevention
-   [ ] Authorization checks on routes

### Testing

-   [ ] All critical features tested
-   [ ] No broken links
-   [ ] Forms submit correctly
-   [ ] Redirects work properly
-   [ ] Error handling works

### Browser/Device Support

-   [ ] Chrome/Chromium ✅
-   [ ] Firefox ✅
-   [ ] Safari ✅
-   [ ] Edge ✅
-   [ ] Mobile (iOS/Android) ✅
-   [ ] Tablet ✅
-   [ ] Desktop ✅

### Accessibility

-   [ ] WCAG 2.1 AA compliant
-   [ ] Keyboard navigation works
-   [ ] Screen readers compatible
-   [ ] Color contrast adequate
-   [ ] Alt text on images

### Documentation

-   [ ] README updated
-   [ ] Component documentation complete
-   [ ] Deployment instructions clear
-   [ ] Known issues documented
-   [ ] Changelog updated

---

## 📝 Notes & Best Practices

### Blade Component Tips

```blade
<!-- Use slots for maximum flexibility -->
<x-card>
    <x-slot:header>Title</x-slot:header>
    <x-slot:body>Content</x-slot:body>
    <x-slot:footer>Footer</x-slot:footer>
</x-card>

<!-- Use attributes for simple values -->
<x-button variant="primary" size="lg">Click Me</x-button>

<!-- Use computed properties for logic -->
<x-stats-card
    :title="$stat->title"
    :value="$stat->value"
    :color="$stat->trend > 0 ? 'green' : 'red'"
/>
```

### Tailwind Best Practices

```css
/* Use @apply for repeated patterns */
.btn-base {
    @apply inline-flex items-center justify-center px-4 py-2 rounded-lg font-medium transition duration-200;
}

/* Use extend for custom utilities */
.card-hover {
    @apply hover:shadow-lg active:shadow-md;
}

/* Use responsive prefixes */
<div class="w-full md:w-1/2 lg:w-1/3">

/* Use dark mode if needed */
<div class="dark:bg-gray-900 dark:text-white">
```

### Component Organization

```
Always group related components:
- Form components in form/ folder
- Admin components in admin/ folder
- Icon components in icons/ folder
- Booking components in bookings/components/
```

---

**Version**: 1.0  
**Status**: Ready for Implementation  
**Last Updated**: 28 Oktober 2025
