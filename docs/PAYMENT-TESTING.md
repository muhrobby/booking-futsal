# Payment Gateway Testing Guide

Dokumentasi lengkap untuk testing payment gateway Xendit **tanpa koneksi internet** menggunakan HTTP mocking.

## Daftar Isi

-   [Overview](#overview)
-   [Quick Start](#quick-start)
-   [Test Scenarios](#test-scenarios)
-   [How It Works](#how-it-works)
-   [Offline Testing](#offline-testing)
-   [Troubleshooting](#troubleshooting)

---

## Overview

Aplikasi booking futsal menggunakan **Xendit Payment Gateway** untuk memproses pembayaran. Untuk development dan testing, kita menggunakan **HTTP Mocking** agar dapat melakukan testing tanpa memerlukan koneksi internet ke server Xendit.

### Keuntungan HTTP Mocking:

-   ✅ Testing tanpa internet connection
-   ✅ Predictable responses (tidak ada random failures)
-   ✅ Fast execution (tidak perlu menunggu API response)
-   ✅ Test coverage yang comprehensive
-   ✅ Tidak perlu sandbox account
-   ✅ Dapat mensimulasikan berbagai skenario (success, failed, expired)

---

## Quick Start

### 1. Jalankan All Payment Tests

```bash
php artisan test tests/Feature/PaymentGatewayTest.php
```

Hasil yang diharapkan:

```
PASS  Tests\Feature\PaymentGatewayTest
✓ create order with mocked xendit                      1.10s
✓ process payment with mocked xendit                   0.13s
✓ handle payment success with mock                     0.37s
✓ handle payment failed with mock                      0.08s
✓ payment retry scenario                               0.25s
✓ check invoice status mock                            0.10s

Tests:    6 passed (21 assertions)
Duration: 2.28s
```

### 2. Jalankan Specific Test

```bash
# Test payment creation
php artisan test tests/Feature/PaymentGatewayTest.php --filter=create_order

# Test payment processing
php artisan test tests/Feature/PaymentGatewayTest.php --filter=process_payment

# Test payment success
php artisan test tests/Feature/PaymentGatewayTest.php --filter=handle_payment_success
```

### 3. Run Tests dengan Monitoring

```bash
# Run tests dalam watch mode
php artisan test tests/Feature/PaymentGatewayTest.php --watch
```

---

## Test Scenarios

### Test 1: Create Order dengan Mock Xendit

**File**: `tests/Feature/PaymentGatewayTest.php::test_create_order_with_mocked_xendit`

```php
/**
 * Skenario:
 * 1. Mock Xendit API untuk mengembalikan invoice yang berhasil dibuat
 * 2. Create order dari booking
 * 3. Verify order status = pending
 * 4. Verify booking expires_at sudah di-set
 */
```

**Yang di-mock**:

```php
Http::fake([
    'api.xendit.co/v2/invoices' => Http::response([
        'id' => 'inv_mock_12345',
        'invoice_url' => 'https://checkout-staging.xendit.co/web/inv_mock_12345',
        'status' => 'PENDING',
        'amount' => 175000,
    ], 201),
]);
```

**Assertions**:

-   Order status = 'pending'
-   Order total = 175000
-   Booking status = 'pending'
-   Booking expires_at is not null

---

### Test 2: Process Payment dengan Mock

**File**: `tests/Feature/PaymentGatewayTest.php::test_process_payment_with_mocked_xendit`

```php
/**
 * Skenario:
 * 1. Create order
 * 2. Mock Xendit API untuk create invoice
 * 3. Process payment dengan order
 * 4. Verify order status berubah menjadi processing
 */
```

**Assertions**:

-   Result['success'] = true
-   Result['redirect_url'] is not null
-   Order status = 'processing'

---

### Test 3: Payment Success Handler

**File**: `tests/Feature/PaymentGatewayTest.php::test_handle_payment_success_with_mock`

```php
/**
 * Skenario:
 * 1. Create order
 * 2. Mock webhook data dari Xendit (status = PAID)
 * 3. Call handlePaymentSuccess()
 * 4. Verify order status = paid dan booking status = confirmed
 */
```

**Assertions**:

-   Order status = 'paid'
-   Booking status = 'confirmed'
-   Booking expires_at = null (sudah di-clear)

---

### Test 4: Payment Failed Handler

**File**: `tests/Feature/PaymentGatewayTest.php::test_handle_payment_failed_with_mock`

```php
/**
 * Skenario:
 * 1. Create order
 * 2. Call handlePaymentFailed() dengan error message
 * 3. Verify order status = failed
 * 4. Verify booking di-revert ke status pending
 */
```

**Assertions**:

-   Order status = 'failed'
-   Booking status = 'pending'
-   Booking expires_at = null

---

### Test 5: Payment Retry Scenario

**File**: `tests/Feature/PaymentGatewayTest.php::test_payment_retry_scenario`

```php
/**
 * Skenario:
 * 1. Create order, payment gagal
 * 2. User retry: create new order untuk booking yang sama
 * 3. Payment kedua berhasil (PAID)
 * 4. Verify booking di-confirm untuk kedua order attempts
 */
```

**Flow**:

```
Order 1 → Failed → Booking: pending
Order 2 → Success → Booking: confirmed
```

**Assertions**:

-   Order 1 status = 'failed'
-   Booking status (after order 1) = 'pending'
-   Order 2 status = 'paid'
-   Booking status (after order 2) = 'confirmed'

---

### Test 6: Invoice Status Check

**File**: `tests/Feature/PaymentGatewayTest.php::test_check_invoice_status_mock`

```php
/**
 * Skenario:
 * 1. Mock Xendit API untuk check invoice status
 * 2. Call checkInvoiceStatus() dengan invoice ID
 * 3. Verify response berisi data yang di-mock
 */
```

**Assertions**:

-   invoiceData is not null
-   invoiceData['status'] = 'PAID'
-   invoiceData['amount'] = 175000

---

## How It Works

### 1. HTTP Mocking Setup

Di `tests/TestCase.php`:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
    }
}
```

### 2. Mock HTTP Responses

Di test methods:

```php
use Illuminate\Support\Facades\Http;

Http::fake([
    'api.xendit.co/v2/invoices' => Http::response([
        'id' => 'inv_mock_12345',
        'status' => 'PENDING',
        'amount' => 175000,
    ], 201),
]);

// Semua request ke api.xendit.co/v2/invoices akan di-return mocked response
```

### 3. Database Isolation

Setiap test:

1. Create fresh in-memory SQLite database
2. Run migrations
3. Create test data (User, Field, TimeSlot, Booking)
4. Run assertions
5. Drop database (automatic cleanup)

---

## Offline Testing

### Testing Tanpa Internet

Aplikasi sudah fully mocked, jadi dapat testing tanpa internet:

```bash
# Matikan internet connection
# Atau gunakan airplane mode

# Jalankan tests
php artisan test tests/Feature/PaymentGatewayTest.php

# Semua test akan PASS
```

### Mengapa Bisa Offline?

1. **HTTP Requests di-intercept**: Semua HTTP request ke Xendit di-intercept oleh `Http::fake()`
2. **Mock Responses**: Responses sudah di-siapkan di-memory, tidak perlu hit real API
3. **preventStrayRequests()**: Laravel akan throw error jika ada request yang tidak di-mock

```php
// Di setUp() method:
Http::preventStrayRequests();

// Ini akan throw exception jika ada request ke URL yang tidak di-mock
```

---

## Troubleshooting

### Error: "Attempted request to [URL] without a matching fake"

**Penyebab**: URL endpoint tidak cocok dengan mock pattern

**Solusi**:

```php
// ❌ SALAH
Http::fake([
    'app.xendit.co/v2/invoices' => Http::response(...),
]);

// ✅ BENAR
Http::fake([
    'api.xendit.co/v2/invoices' => Http::response(...),
]);

// ✅ BENAR (dengan wildcard untuk ID)
Http::fake([
    'api.xendit.co/v2/invoices/*' => Http::response(...),
]);
```

### Error: "SQLSTATE[23000]: Integrity constraint violation"

**Penyebab**: Invalid enum value untuk booking status

**Solusi**:

```php
// ❌ SALAH - 'available' bukan valid status
$booking->update(['status' => 'available']);

// ✅ BENAR - valid status: 'pending', 'confirmed', 'canceled'
$booking->update(['status' => 'pending']);
```

### Error: "Cannot end a section without first starting one"

**Penyebab**: Blade template syntax error

**Solusi**:

-   Pastikan `@extends` ada di awal file
-   Pastikan `@section` dan `@endsection` seimbang
-   Gunakan `@section('content')` bukan `@section('content'`

### Test Timeout

**Solusi**:

```bash
# Increase timeout
php artisan test tests/Feature/PaymentGatewayTest.php --timeout=60
```

---

## Testing Checklist

-   [ ] Semua 6 tests pass
-   [ ] Tidak ada error messages di output
-   [ ] Duration di bawah 3 detik total
-   [ ] Dapat run offline (tanpa internet)
-   [ ] Database clean setelah test (migrations fresh)
-   [ ] Mock responses realistic dan correct

---

## Next Steps

Setelah testing di local, Anda dapat:

1. **Test dengan Real API** (staging):

    ```bash
    # Update .env
    XENDIT_API_URL=https://app.xendit.co
    XENDIT_SECRET_KEY=your_staging_key

    # Jalankan aplikasi
    php artisan serve
    ```

2. **End-to-End Testing** di browser:

    - Visit http://localhost:8000
    - Create booking
    - Click "Bayar Sekarang"
    - Verify redirect ke Xendit checkout
    - Verify payment status update

3. **Production Deployment**:
    - Switch ke production API keys
    - Test dengan real payment methods
    - Monitor webhook logs
    - Set up error alerting

---

## Reference

-   **Xendit Documentation**: https://docs.xendit.co
-   **Laravel Testing**: https://laravel.com/docs/testing
-   **Laravel HTTP Client**: https://laravel.com/docs/http-client
