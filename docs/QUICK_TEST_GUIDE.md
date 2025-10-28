# Quick Test Guide - Futsal Neo S

## 🚀 Quick Start Testing

### Pre-requisites
```bash
# 1. Clear all cache
php artisan config:cache
php artisan view:clear
php artisan route:cache

# 2. Reset database with seeder
php artisan migrate:fresh --seed

# 3. Start development server
php artisan serve
```

---

## 🔑 Test Accounts

### Admin Accounts
```
Email: admin1@example.com - admin10@example.com
Password: password
```

### User Accounts
```
Email: user1@example.com - user50@example.com
Password: password
```

---

## 🎯 Critical Test Scenarios (30 minutes)

### Scenario 1: Admin Flow (10 min)
1. Login as **admin1@example.com**
2. Check Dashboard → Date filter → Charts working
3. Go to Bookings → Apply filters → Update status
4. Go to Users → Search → Create new user → Edit → Delete
5. Go to Fields → Create field → Edit → Check validation
6. Logout

### Scenario 2: User Flow (10 min)
1. Login as **user1@example.com**
2. Check Dashboard → Statistics → Next booking → Reminder button
3. Go to My Bookings → Apply filters (status + date)
4. Click "Lihat Detail" → Modal shows → Close modal
5. Test pagination (if > 10 bookings)
6. Logout

### Scenario 3: Public Pages (5 min)
1. Browse as Guest
2. Check Homepage → Branding "Futsal Neo S"
3. Check Navbar → Links work
4. Check Footer → Copyright correct
5. Try to access `/admin` → Redirect to login

### Scenario 4: Responsive (5 min)
1. Resize browser to mobile (< 768px)
2. Check My Bookings → Table becomes cards
3. Check Admin Dashboard → Charts responsive
4. Check modals → Fit screen
5. Check filters → Stack vertically

---

## 🔴 Critical Bugs to Check

### Must Work ✅
- [ ] Login/Logout
- [ ] All filters (date, status, search)
- [ ] Pagination preserves filters
- [ ] CRUD operations (Create, Edit, Delete)
- [ ] Charts load and display data
- [ ] Modals open and close
- [ ] Buttons submit forms
- [ ] Success/error messages appear

### Common Issues ❌
- [ ] Button not working (nested button issue)
- [ ] Filter not working (has() vs filled())
- [ ] Date filter not working (where() vs whereDate())
- [ ] Charts not loading (check Chart.js CDN)
- [ ] Modal not closing (check onclick handlers)
- [ ] Old branding "FutsalGO" remains

---

## 📱 Test URLs

### User Pages
```
Homepage:        http://localhost:8000/
Dashboard:       http://localhost:8000/dashboard
My Bookings:     http://localhost:8000/my-bookings
```

### Admin Pages
```
Dashboard:       http://localhost:8000/admin/dashboard
Bookings:        http://localhost:8000/admin/bookings
Users:           http://localhost:8000/admin/users
Fields:          http://localhost:8000/admin/fields
```

---

## 🧪 Quick Feature Tests

### Test Filters
```
1. /my-bookings?status=pending
2. /my-bookings?date_from=2025-10-28&date_to=2025-10-30
3. /my-bookings?status=confirmed&date_from=2025-10-28&date_to=2025-11-30
4. /admin/users?search=admin&role=admin
5. /admin/bookings?field_id=1&status=pending&date=2025-10-28
```

### Test Modals
```
1. My Bookings → Click "Lihat Detail"
2. Dashboard → Click "Buat Reminder"
```

### Test Charts
```
1. Admin Dashboard → Default (30 days)
2. Change date range → Charts update
3. Hover on chart → Tooltip shows
```

---

## 🐛 Debugging Tips

### No Data Showing
```bash
# Check if seeder ran
php artisan tinker
>>> \App\Models\User::count()
>>> \App\Models\Booking::count()
>>> exit
```

### Filter Not Working
```
1. Check browser console for JS errors
2. Check URL parameters
3. Check controller uses filled() not has()
4. Check pagination uses withQueryString()
```

### Button Not Working
```
1. Right-click → Inspect Element
2. Check for nested <button> tags
3. Check onclick handler exists
4. Check browser console
```

### Charts Not Loading
```
1. Check browser console
2. Verify Chart.js CDN loaded (Network tab)
3. Check @stack('scripts') in layout
4. Check chartData variable exists
```

---

## ✅ Success Criteria

### All Green ✅
- [ ] No JavaScript errors in console
- [ ] No 404/500 errors in Network tab
- [ ] All buttons clickable and working
- [ ] All filters apply correctly
- [ ] All modals open/close properly
- [ ] All charts render correctly
- [ ] All CRUD operations work
- [ ] All success messages appear
- [ ] Branding = "Futsal Neo S" everywhere
- [ ] Responsive on mobile

---

## 📞 Quick Support

### Clear Cache
```bash
php artisan config:cache
php artisan view:clear
php artisan route:cache
```

### Reset Database
```bash
php artisan migrate:fresh --seed
```

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

---

**Testing Time: 30-45 minutes**
**Priority: Critical features first**
**Document: Note any issues found**

Happy Testing! 🚀
