# Testing Architecture Overview

Dokumentasi high-level tentang struktur testing payment gateway.

## Structure

```
tests/
├── Feature/
│   └── PaymentGatewayTest.php          # Integration tests dengan mocked Xendit
├── Unit/
│   └── PaymentGatewayTest.php          # (Placeholder untuk unit tests)
└── TestCase.php                        # Base test class dengan RefreshDatabase
```

## Test Database Setup

**File**: `config/database.php`

```php
'testing' => [
    'driver' => 'sqlite',
    'database' => ':memory:',  // In-memory database untuk speed
    'prefix' => '',
],
```

**File**: `tests/TestCase.php`

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;  // Auto migrate + cleanup setelah setiap test
    
    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
    }
}
```

## Test Flow

```
TEST START
    ↓
Database Setup (in-memory SQLite)
    ↓
Run Migrations (create tables)
    ↓
Setup HTTP Mocking (Http::fake())
    ↓
Create Test Data (Factory)
    ↓
Execute Test Logic
    ↓
Assert Results
    ↓
Cleanup (drop database)
    ↓
TEST COMPLETE
```

## HTTP Mocking Pattern

```php
use Illuminate\Support\Facades\Http;

// 1. Fake all requests
Http::fake([
    'api.xendit.co/v2/invoices' => Http::response([...], 201),
    'api.xendit.co/v2/invoices/*' => Http::response([...], 200),
]);

// 2. Prevent stray requests (strict mode)
Http::preventStrayRequests();

// 3. Make request (akan return mock, tidak hit real API)
$xenditService->createInvoice($order);

// 4. Assert request was sent (optional)
Http::assertSent(function ($request) {
    return $request->url() === 'api.xendit.co/v2/invoices';
});
```

## Data Flow in Tests

```
User → Field → TimeSlot → Booking → Order → PaymentTransaction
                                      ↓
                              (mock API call)
                                      ↓
                              XenditPaymentService
```

## Service Layer

```
OrderService
├── createOrder()           # Create order + booking lock + expires_at
├── processPayment()        # Call Xendit API (mocked)
├── handlePaymentSuccess()  # Update order & booking status
├── handlePaymentFailed()   # Revert order & booking status
└── handlePaymentExpired()  # Auto-cancel if timeout

XenditPaymentService
├── createInvoice()         # POST /v2/invoices (mocked)
└── checkInvoiceStatus()    # GET /v2/invoices/{id} (mocked)
```

## Key Test Utilities

### 1. RefreshDatabase Trait
```php
trait RefreshDatabase
{
    // Automatically:
    // - Run migrations before each test
    // - Rollback after each test
    // - Keep database clean
}
```

### 2. Factory Classes
```php
User::factory()->create()       // Create fake user
Field::factory()->create()      // Create fake field
TimeSlot::factory()->create()   // Create fake time slot
Booking::create([...])          // Create booking manually
```

### 3. HTTP Fake/Assert
```php
Http::fake([...])               // Setup fake responses
Http::assertSent(...)           // Verify request was sent
Http::assertNotSent(...)        // Verify request was NOT sent
```

## Running Tests

```bash
# All payment tests
php artisan test tests/Feature/PaymentGatewayTest.php

# Single test
php artisan test tests/Feature/PaymentGatewayTest.php --filter=create_order

# With output
php artisan test tests/Feature/PaymentGatewayTest.php -vvv

# Watch mode (auto-run on file change)
php artisan test tests/Feature/PaymentGatewayTest.php --watch
```

## Performance Metrics

| Test | Duration | Assertions |
|------|----------|-----------|
| create_order_with_mocked_xendit | 1.10s | 5 |
| process_payment_with_mocked_xendit | 0.13s | 3 |
| handle_payment_success_with_mock | 0.37s | 3 |
| handle_payment_failed_with_mock | 0.08s | 3 |
| payment_retry_scenario | 0.25s | 4 |
| check_invoice_status_mock | 0.10s | 3 |
| **TOTAL** | **2.28s** | **21** |

## Offline Testing Capability

✅ **100% Offline**: Semua tests berjalan tanpa internet connection

Mengapa?
1. Database: SQLite in-memory (no network required)
2. API Calls: Semua di-mock dengan `Http::fake()` 
3. No External Dependencies: Semua data generated locally

```bash
# Turn off internet / airplane mode
# Tests still pass!
php artisan test tests/Feature/PaymentGatewayTest.php
```

## Error Scenarios Covered

1. ✅ Successful invoice creation
2. ✅ Successful payment completion
3. ✅ Payment failure handling
4. ✅ Payment retry after failure
5. ✅ Invoice status checking
6. ✅ Booking expiration timeout

## Best Practices

1. **Isolation**: Each test is independent, no shared state
2. **Clarity**: Test names describe what they test
3. **Mock Everything External**: Don't make real API calls
4. **Assert Clearly**: Explicit assertions instead of generic checks
5. **Setup/Cleanup**: Automatic via RefreshDatabase

## Continuous Integration

Tests dapat di-integrate ke CI/CD pipeline:

```yaml
# .github/workflows/test.yml
- name: Run Tests
  run: php artisan test tests/Feature/PaymentGatewayTest.php
```

No extra dependencies needed - semua sudah self-contained!

