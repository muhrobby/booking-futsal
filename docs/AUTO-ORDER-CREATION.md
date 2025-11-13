# Auto-Order Creation Feature

## Overview

Starting from this version, Orders are automatically created when Bookings are made. This eliminates the need for users to manually navigate to checkout - the system handles it automatically.

## Flow

### Before (Old Flow)

```
User: Book lapangan
  ↓
Booking created (status: pending)
  ↓
User clicks "Checkout" button
  ↓
Order created (status: pending)
  ↓
Payment page opens
  ↓
User pays
  ↓
Order status: paid, Booking status: confirmed
```

### After (New Flow - Current)

```
User: Book lapangan
  ↓
Booking created (status: pending) [AUTO]
Order created (status: pending)
Booking lock created (30 min timeout)
  ↓
User redirected to checkout page (order already exists)
  ↓
Payment page opens
  ↓
User pays
  ↓
Order status: paid, Booking status: confirmed
```

## Technical Implementation

### 1. BookingController.store()

When a booking is created, the system automatically creates an associated order:

```php
public function store(StoreBookingRequest $request): RedirectResponse
{
    $data = $request->validated();
    $data['user_id'] = Auth::id();
    $data['status'] = 'pending';

    // Create or reuse booking
    $booking = Booking::create($data);

    // Auto-create order immediately
    try {
        $orderService = app(OrderService::class);
        $order = $orderService->createOrder($booking, Auth::user());

        return redirect()
            ->route('orders.create', $booking)
            ->with('status', 'Booking berhasil dibuat. Silakan lanjutkan pembayaran.')
            ->with('order_id', $order->id);
    } catch (\Exception $e) {
        Log::error('Failed to create order', [
            'booking_id' => $booking->id,
            'error' => $e->getMessage(),
        ]);

        return redirect()
            ->route('orders.create', $booking)
            ->with('error', 'Terjadi kesalahan saat membuat order, silakan coba lagi.');
    }
}
```

### 2. OrderController.create()

Fallback mechanism to ensure order exists:

```php
public function create(Request $request, Booking $booking): View
{
    // ... authorization checks ...

    // Get existing order or create if missing (fallback)
    $order = $booking->orders()->first();
    if (!$order) {
        $order = app(OrderService::class)->createOrder($booking, Auth::user());
    }

    return view('orders.create', compact('booking', 'order'));
}
```

### 3. OrderController.store()

Uses existing auto-created order instead of creating new one:

```php
public function store(Request $request, Booking $booking)
{
    // ... authorization checks ...

    // Get the auto-created order
    $order = $booking->orders()->first();
    if (!$order) {
        throw new \Exception('Order not found. Please try again.');
    }

    // Process payment with existing order
    $paymentResult = $this->orderService->processPayment($order);
    // ... rest of payment flow
}
```

## Benefits

1. **Seamless User Experience**: Users don't need to manually navigate to checkout
2. **Automatic Booking Lock**: 30-minute payment timeout is set immediately
3. **Consistent Data**: Orders are always synchronized with bookings
4. **Error Prevention**: No more "Order not found" errors during payment
5. **Simplified Flow**: Less user interactions needed

## Database Structure

When a booking is created, the following happens:

**Booking Table:**

-   `status` = 'pending'
-   `expires_at` = now + 30 minutes
-   `user_id` = authenticated user

**Order Table:**

-   `booking_id` = reference to booking
-   `user_id` = same as booking user
-   `status` = 'pending'
-   `subtotal` = field price per hour
-   `total` = field price per hour
-   `currency` = 'IDR'
-   `order_number` = auto-generated (ORD-YYYYMMDD-XXXXX)

**BookingLock Table:**

-   `booking_id` = reference to booking
-   `is_active` = true
-   `expires_at` = now + 30 minutes

## Testing

Comprehensive test suite included: `BookingAutoOrderCreationTest.php`

Tests verify:

-   ✅ Order auto-created when booking created
-   ✅ Multiple bookings get separate orders
-   ✅ Checkout page gets existing order
-   ✅ Booking lock created with order
-   ✅ Order has pending status initially

Run tests:

```bash
php artisan test tests/Feature/BookingAutoOrderCreationTest.php
```

## Related Documentation

-   See `PAYMENT-TESTING.md` for offline payment testing
-   See `ADMIN-ORDERS-FAQ.md` for admin orders troubleshooting
-   See `PaymentGatewayTest.php` for payment integration tests

## Migration Notes

If upgrading from old flow:

1. Existing bookings without orders won't auto-create orders (by design)
2. To create orders for existing pending bookings, run:
    ```bash
    php artisan bookings:create-missing-orders
    ```
3. Admin can manually create orders via admin panel if needed

## Error Handling

If order creation fails:

-   User is still redirected to checkout page
-   Error message shown: "Terjadi kesalahan saat membuat order, silakan coba lagi."
-   Error is logged for debugging
-   System has fallback in OrderController.create() to retry order creation

## Rollback

To temporarily disable auto-order creation (not recommended):

Comment out in `BookingController.store()`:

```php
// $orderService = app(OrderService::class);
// $order = $orderService->createOrder($booking, Auth::user());
```

But note: OrderController.create() has fallback that will create order anyway.
