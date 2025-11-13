# 🕐 Auto-Cancel Expired Bookings System

## Overview

Sistem otomatis untuk membatalkan booking yang pending lebih dari waktu yang ditentukan (default 30 menit). Ini memastikan slot booking yang sudah dipesan tapi tidak dibayar akan tersedia kembali untuk pengguna lain.

## 📋 Workflow

### 1. **Saat User Membuat Order (Checkout)**

```
User clicks "Bayar" → OrderService.createOrder()
  ↓
Booking status → "pending"
Booking expires_at → now() + 30 minutes
Order status → "pending"
Booking lock created → 30 minutes
```

### 2. **Monitoring Expired Bookings** (Every 5 minutes)

```
CancelExpiredBookings Job runs
  ↓
Find: WHERE status = 'pending' AND expires_at < now()
  ↓
For each expired booking:
  - Release booking lock
  - Update status → "cancelled"
  - Add note: "[Auto-cancelled: Payment timeout]"
  - Booking slot becomes available for others
```

### 3. **On Payment Success**

```
Payment confirmed → handlePaymentSuccess()
  ↓
Order status → "paid"
Booking status → "confirmed"
Booking expires_at → NULL (clear)
Booking lock → released
```

### 4. **On Payment Failed**

```
Payment failed → handlePaymentFailed()
  ↓
Order status → "failed"
Booking status → "available" (reverted)
Booking expires_at → NULL (clear)
Booking lock → released
```

## 🔧 Configuration

File: `.env`

```properties
PAYMENT_TIMEOUT_MINUTES=30    # Default: 30 minutes
PAYMENT_CURRENCY=IDR
```

File: `config/payment.php`

```php
'payment' => [
    'timeout_minutes' => env('PAYMENT_TIMEOUT_MINUTES', 30),
    'currency' => env('PAYMENT_CURRENCY', 'IDR'),
],
```

## 📊 Database

### Bookings Table

```sql
ALTER TABLE bookings ADD COLUMN expires_at TIMESTAMP NULL;
-- Nullable timestamp untuk booking expiration
```

## 🎯 Components

### 1. **Job: CancelExpiredBookings**

Location: `app/Jobs/CancelExpiredBookings.php`

Logika:

-   Query booking dengan status='pending' dan expires_at < now()
-   Release active booking locks
-   Update status ke 'cancelled'
-   Add auto-cancel note

### 2. **Service: OrderService**

Location: `app/Services/OrderService.php`

Perubahan:

-   `createOrder()`: Set booking.expires_at saat membuat order
-   `handlePaymentSuccess()`: Clear booking.expires_at saat bayar berhasil
-   `handlePaymentFailed()`: Revert booking status dan clear expires_at

### 3. **Model: Booking**

Location: `app/Models/Booking.php`

Perubahan:

-   Tambah `expires_at` ke fillable
-   Cast expires_at ke datetime

### 4. **Scheduler: Console/Kernel**

Location: `app/Console/Kernel.php`

Schedule:

```php
// Setiap 5 menit
$schedule->job(new CancelExpiredBookings())
    ->everyFiveMinutes()
    ->withoutOverlapping();

// Setiap hari tengah malam (cleanup)
$schedule->job(new CancelExpiredBookings())
    ->dailyAt('00:00')
    ->withoutOverlapping();
```

### 5. **Command: TestCancelExpiredBookings**

Location: `app/Console/Commands/TestCancelExpiredBookings.php`

Usage:

```bash
php artisan test:cancel-bookings
```

## 🚀 Cara Kerja

### Manual Testing

1. **Create booking pending:**

```php
// Via tinker or test code
$booking = Booking::create([
    'field_id' => 1,
    'time_slot_id' => 1,
    'booking_date' => today(),
    'customer_name' => 'Test User',
    'customer_phone' => '08123456789',
    'user_id' => 1,
    'status' => 'pending',
    'expires_at' => now()->subMinutes(5), // Already expired
]);
```

2. **Run cancel job:**

```bash
php artisan test:cancel-bookings
```

3. **Verify:**

```php
$booking->refresh();
echo $booking->status; // Should be 'cancelled'
```

### Production Setup

1. **Configure cron job** (in server crontab):

```bash
* * * * * cd /path/to/booking-futsal && php artisan schedule:run >> /dev/null 2>&1
```

2. **The scheduler will automatically:**

-   Run CancelExpiredBookings job every 5 minutes
-   Run cleanup at 00:00 daily
-   Use `withoutOverlapping()` to prevent duplicate runs

## 📈 Status Timeline

```
Order Created
    ↓ (expires_at = +30 min)
Booking Status: PENDING
    ↓
[2 scenarios]

Scenario A: Payment Success (within 30 min)
    ↓
Booking Status: CONFIRMED
Booking expires_at: NULL
    ↓
✅ Booking locked & confirmed

Scenario B: Timeout (30 min passed without payment)
    ↓ [Auto-cancel runs]
Booking Status: CANCELLED
Booking expires_at: NULL
    ↓
🔄 Slot available for others
```

## 🔍 Monitoring

### Check pending bookings:

```php
$pending = Booking::where('status', 'pending')->get();
```

### Check expired bookings:

```php
$expired = Booking::where('status', 'pending')
    ->where('expires_at', '<', now())
    ->get();
```

### Check logs:

```bash
tail -f storage/logs/laravel.log | grep "Auto-cancelled"
```

## ⚠️ Important Notes

1. **Timezone**: Ensure server timezone is set correctly in `config/app.php`
2. **Cron Job**: Production must have cron job running to trigger scheduler
3. **Queue Driver**: Currently using `database` queue driver
4. **Lock Mechanism**: Booking locks are separate from expires_at timeout
5. **Payment Timeout**: Configurable via `PAYMENT_TIMEOUT_MINUTES` in .env

## 🧪 Testing Checklist

-   [ ] User creates order → booking.expires_at is set
-   [ ] Payment success within 30 min → booking confirmed, expires_at cleared
-   [ ] Payment failed → booking reverted to available, expires_at cleared
-   [ ] Booking expired without payment → auto-cancelled by job
-   [ ] Expired slot available for new bookings
-   [ ] Manual test: `php artisan test:cancel-bookings` works
-   [ ] Logs show auto-cancelled bookings

## 📝 Logs Example

```
[2025-11-09 21:05:00] local.INFO: Auto-cancelled 2 expired bookings
[2025-11-09 21:05:00] local.INFO: Order created [...expires_at => 2025-11-09 21:35:00...]
[2025-11-09 21:35:05] local.INFO: Auto-cancelled 1 expired bookings
```
