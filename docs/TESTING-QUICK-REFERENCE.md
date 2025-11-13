# 🧪 Payment Gateway Testing - Quick Reference

Panduan cepat untuk testing payment gateway **tanpa koneksi internet**.

## ⚡ Quick Start

```bash
# Run semua payment tests
php artisan test tests/Feature/PaymentGatewayTest.php

# Expected output:
# Tests: 6 passed (21 assertions)
# Duration: 2.70s
```

✅ **Semua test pass tanpa internet!**

## 🎯 Test Cases

| #   | Test                               | Purpose                         | Duration |
| --- | ---------------------------------- | ------------------------------- | -------- |
| 1   | create_order_with_mocked_xendit    | Create order dengan Xendit mock | 1.39s    |
| 2   | process_payment_with_mocked_xendit | Process payment workflow        | 0.13s    |
| 3   | handle_payment_success_with_mock   | Handle successful payment       | 0.36s    |
| 4   | handle_payment_failed_with_mock    | Handle failed payment           | 0.09s    |
| 5   | payment_retry_scenario             | Complete retry workflow         | 0.32s    |
| 6   | check_invoice_status_mock          | Check invoice status            | 0.10s    |

## 📚 Documentation

-   **[PAYMENT-TESTING.md](./PAYMENT-TESTING.md)** - Complete testing guide
    -   Quick start, test scenarios, how it works
    -   Troubleshooting, testing checklist
-   **[TESTING-ARCHITECTURE.md](./TESTING-ARCHITECTURE.md)** - Architecture overview

    -   Structure, database setup, data flow
    -   Service layer, utilities, performance metrics

-   **[OFFLINE-TESTING-SESSION.md](./OFFLINE-TESTING-SESSION.md)** - Session summary
    -   Tasks completed, bugs fixed
    -   Technical implementation, next steps

## 🔧 How It Works

### HTTP Mocking

```php
// Semua requests ke Xendit di-intercept
Http::fake([
    'api.xendit.co/v2/invoices' => Http::response([...], 201),
]);

// Tidak ada real API call - semuanya mock!
$xenditService->createInvoice($order);
```

### Database Isolation

```php
// In-memory SQLite database
// Auto-migrate, auto-cleanup
// Zero external dependencies
```

### Result

✅ Testing **100% offline** tanpa internet  
✅ Execution time **< 3 seconds**  
✅ Comprehensive coverage **6 scenarios**

## 💻 Common Commands

```bash
# Run all tests
php artisan test tests/Feature/PaymentGatewayTest.php

# Run specific test
php artisan test tests/Feature/PaymentGatewayTest.php --filter=create_order

# Watch mode (auto-run on changes)
php artisan test tests/Feature/PaymentGatewayTest.php --watch

# Verbose output
php artisan test tests/Feature/PaymentGatewayTest.php -vvv

# Fail fast (stop on first failure)
php artisan test tests/Feature/PaymentGatewayTest.php --fail-fast
```

## ✨ Key Features

-   ✅ **Offline Capability**: Test tanpa internet
-   ✅ **HTTP Mocking**: All Xendit API calls mocked
-   ✅ **Database Isolation**: In-memory SQLite
-   ✅ **Fast Execution**: 2.70 seconds for all tests
-   ✅ **Comprehensive**: 6 scenarios, 21 assertions
-   ✅ **Well Documented**: 600+ lines documentation

## 🚀 Test Flow

```
Start Test
    ↓
Create in-memory database
    ↓
Run migrations
    ↓
Setup HTTP mocking
    ↓
Create test data
    ↓
Execute test logic
    ↓
Assert results
    ↓
Cleanup
    ↓
Test complete ✓
```

## 📊 Coverage

| Scenario                   | Status    |
| -------------------------- | --------- |
| Invoice creation           | ✅ Tested |
| Payment processing         | ✅ Tested |
| Payment success            | ✅ Tested |
| Payment failure            | ✅ Tested |
| Retry after failure        | ✅ Tested |
| Invoice status check       | ✅ Tested |
| Booking status transitions | ✅ Tested |
| Order status transitions   | ✅ Tested |
| Expires_at management      | ✅ Tested |

## 🆘 Troubleshooting

### Error: "Attempted request without matching fake"

```php
// Check: Are you using correct URL?
// ❌ app.xendit.co
// ✅ api.xendit.co
```

### Error: "Integrity constraint violation: status"

```php
// Check: Valid enum values for booking status
// Valid: 'pending', 'confirmed', 'canceled'
// Invalid: 'available'
```

### Tests timing out

```bash
php artisan test tests/Feature/PaymentGatewayTest.php --timeout=60
```

## 📖 Learn More

-   [Laravel HTTP Client](https://laravel.com/docs/http-client)
-   [Laravel Testing](https://laravel.com/docs/testing)
-   [Xendit API Docs](https://docs.xendit.co)

## 🎯 Next Steps

1. **Integrate to CI/CD**: Add tests ke GitHub Actions
2. **Expand Coverage**: Add more edge cases
3. **Performance Test**: Load testing untuk payment
4. **Security Audit**: Auth & CSRF testing
5. **E2E Tests**: Browser-based testing dengan Dusk

## 📞 Need Help?

Check the comprehensive guides:

1. `PAYMENT-TESTING.md` - Testing guide
2. `TESTING-ARCHITECTURE.md` - Architecture overview
3. `OFFLINE-TESTING-SESSION.md` - Session summary

---

**Status**: ✅ All 6 tests passing  
**Last Updated**: November 13, 2025  
**Maintainer**: Booking Futsal Team
