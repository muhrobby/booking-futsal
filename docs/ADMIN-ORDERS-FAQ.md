# Admin Orders Page - Troubleshooting Guide

## Issue: "Booking #4 sudah terbuat namun tidak ada di halaman admin/orders"

### Root Cause

Halaman `/admin/orders` menampilkan **ORDERS** (pesanan pembayaran), bukan **BOOKINGS** (reservasi lapangan).

**Data Structure**:

```
Booking (Reservasi Lapangan)
├── status: pending, confirmed, canceled
├── booking_date, customer_name, field_id, time_slot_id
└── expires_at: automatic expiration time

Order (Pesanan Pembayaran)
├── booking_id: foreign key ke booking
├── status: pending, processing, paid, failed, expired, refunded
├── total, currency, xendit_invoice_id
└── paid_at, payment_reference
```

### Scenario Breakdown

**Booking tanpa Order**:

-   Booking dibuat (status = pending)
-   User belum mulai checkout / membuat order
-   **TIDAK AKAN MUNCUL** di halaman admin/orders

**Booking dengan Order (pending)**:

-   Booking dibuat (status = pending)
-   User checkout → Order dibuat (status = pending)
-   **AKAN MUNCUL** di halaman admin/orders dengan filter "pending"

**Booking dengan Order (paid)**:

-   Booking dibuat (status = pending)
-   User checkout → Order dibuat (status = pending)
-   User bayar → Order status = paid, Booking status = confirmed
-   **AKAN MUNCUL** di halaman admin/orders dengan filter "paid"

### Solution

Untuk membuat booking muncul di admin/orders, **harus membuat Order**:

```bash
# Option 1: Via Tinker
php artisan tinker
> $booking = \App\Models\Booking::find(4);
> \App\Models\Order::create([
    'user_id' => $booking->user_id,
    'booking_id' => $booking->id,
    'order_number' => 'ORD-20251113-xyz123',
    'status' => 'pending',
    'subtotal' => 150000,
    'total' => 150000,
    'currency' => 'IDR',
    'xendit_invoice_id' => 'inv_xyz123',
  ]);

# Option 2: Buat seed untuk test orders
php artisan db:seed --class=OrderSeeder

# Option 3: User checkout melalui UI
# - User klik "Bayar Sekarang" → system membuat Order otomatis
```

### Where to View

-   **Bookings tanpa Order**: `/admin/bookings`
-   **Orders dengan Booking**: `/admin/orders`
-   **Member bookings**: `/bookings/my`
-   **Member orders**: `/orders`

### Test Data

Setelah `php artisan migrate:fresh --seed`:

```
Total Bookings: 4
├── Booking #1: pending (No Order)
├── Booking #2: pending (Order #1 - pending)
├── Booking #3: pending (Order #2 - processing)
└── Booking #4: pending (Order #3 - failed)

Total Orders: 3
├── Order #1: pending (Booking #2)
├── Order #2: processing (Booking #3)
└── Order #3: failed (Booking #4)
```

### Database Query to Check

```bash
# Check bookings without orders
php artisan tinker
> \App\Models\Booking::doesntHave('orders')->get()

# Check orders for specific booking
> \App\Models\Order::where('booking_id', 4)->get()
```

### Admin Orders Statistics

Halaman admin/orders menampilkan stats untuk Orders:

-   **Total Orders**: Semua orders
-   **Pending**: Orders waiting for payment
-   **Processing**: Orders being paid
-   **Paid**: Orders successfully paid
-   **Failed**: Orders with payment failure
-   **Expired**: Orders with timeout
-   **Revenue**: Total dari paid orders

**TIDAK TERMASUK** bookings yang belum punya order.

### Related Routes

```
GET /admin/bookings              # View all bookings (dengan atau tanpa order)
GET /admin/orders                # View all orders (hanya booking dengan order)
GET /admin/orders?status=pending # Filter orders by status
POST /admin/orders/{id}/status   # Update order status
```

### When to Check Each Page

| Needs                          | Page                           | Status          |
| ------------------------------ | ------------------------------ | --------------- |
| Lihat semua reservasi lapangan | `/admin/bookings`              | Any             |
| Lihat semua pesanan pembayaran | `/admin/orders`                | Any             |
| Lihat pending payments         | `/admin/orders?status=pending` | pending         |
| Lihat successful payments      | `/admin/orders?status=paid`    | paid            |
| Track revenue                  | `/admin/orders`                | Statistics card |

### Conclusion

**Booking #4 tidak muncul di admin/orders karena tidak ada Order yang terkait.**

Ini adalah behavior yang benar - halaman orders hanya menampilkan orders.

Untuk melihat Booking #4, gunakan `/admin/bookings` atau buat Order terlebih dahulu.
