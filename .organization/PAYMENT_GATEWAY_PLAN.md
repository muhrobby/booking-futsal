# Payment Gateway Integration - Comprehensive Plan

**Date**: November 8, 2025  
**Status**: 🎯 Planning Phase  
**Priority**: HIGH - Critical for Revenue

---

## 📋 Executive Summary

Saya akan membuat payment gateway integration dengan:

-   ✅ Professional payment flow (Booking → Payment → Confirmation)
-   ✅ Reservation protection (booking tidak bisa diambil saat pending payment)
-   ✅ Admin payment management dashboard
-   ✅ Multiple payment gateway support (Stripe, Midtrans, etc)
-   ✅ Audit trail & transaction logs
-   ✅ Webhook handling untuk real-time updates
-   ✅ User-friendly & powerful design

---

## 1. Database Schema Design

### New Tables Required

#### Table: `orders` (Pesanan/Invoice)

```sql
CREATE TABLE orders (
    id BIGINT PRIMARY KEY,
    user_id BIGINT NOT NULL (FK to users),
    booking_id BIGINT NOT NULL (FK to bookings),

    -- Order Details
    order_number VARCHAR(50) UNIQUE NOT NULL,  -- INV-20251108-001
    status ENUM('pending', 'processing', 'paid', 'failed', 'cancelled', 'refunded'),

    -- Amount
    subtotal INT NOT NULL,              -- Before tax
    tax INT DEFAULT 0,
    discount INT DEFAULT 0,
    total INT NOT NULL,                 -- Final amount

    -- Payment Details
    payment_method VARCHAR(50),         -- 'stripe', 'midtrans', 'card', etc
    payment_reference VARCHAR(100),     -- Payment gateway transaction ID
    payment_gateway_response LONGTEXT,  -- Store full response from gateway

    -- Timestamps
    paid_at TIMESTAMP NULL,
    expired_at TIMESTAMP,               -- Payment deadline (30 min default)
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_orders_user_id (user_id),
    INDEX idx_orders_booking_id (booking_id),
    INDEX idx_orders_status (status),
    INDEX idx_orders_expired_at (expired_at),
);
```

#### Table: `payment_methods` (Metode Pembayaran)

```sql
CREATE TABLE payment_methods (
    id BIGINT PRIMARY KEY,
    user_id BIGINT NOT NULL (FK to users),

    -- Payment Method Info
    type ENUM('credit_card', 'debit_card', 'e_wallet', 'bank_transfer'),
    last_four VARCHAR(4),               -- Last 4 digits
    brand VARCHAR(50),                  -- Visa, Mastercard, etc

    -- Payment Gateway
    gateway_customer_id VARCHAR(100),   -- Stripe customer ID, etc
    gateway_payment_method_id VARCHAR(100),  -- Payment method ID from gateway

    -- Status
    is_default BOOLEAN DEFAULT false,
    is_active BOOLEAN DEFAULT true,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_payment_methods_user_id (user_id),
);
```

#### Table: `payment_transactions` (Log Transaksi)

```sql
CREATE TABLE payment_transactions (
    id BIGINT PRIMARY KEY,
    order_id BIGINT NOT NULL (FK to orders),

    -- Transaction Details
    gateway VARCHAR(50),                -- 'stripe', 'midtrans', etc
    gateway_transaction_id VARCHAR(100),
    status ENUM('pending', 'processing', 'success', 'failed'),

    -- Amount
    amount INT NOT NULL,
    currency VARCHAR(3) DEFAULT 'IDR',

    -- Response Data
    request_payload LONGTEXT,           -- What we sent
    response_payload LONGTEXT,          -- What we got back
    error_message TEXT,                 -- If failed

    -- Timestamps
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_payment_transactions_order_id (order_id),
    INDEX idx_payment_transactions_gateway (gateway),
);
```

#### Table: `booking_locks` (Reservation Lock)

```sql
CREATE TABLE booking_locks (
    id BIGINT PRIMARY KEY,
    booking_id BIGINT NOT NULL (FK to bookings),
    order_id BIGINT NOT NULL (FK to orders),

    -- Lock Info
    locked_at TIMESTAMP,
    expires_at TIMESTAMP,               -- 30 minutes default
    reason VARCHAR(50),                 -- 'payment_pending', 'manual_hold'

    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    UNIQUE KEY unique_active_lock (booking_id, booking_id),
    INDEX idx_booking_locks_expires_at (expires_at),
);
```

---

## 2. Application Architecture

### Layer 1: Models

#### New Models

**`app/Models/Order.php`**

```php
class Order extends Model {
    protected $fillable = [
        'user_id', 'booking_id', 'order_number', 'status',
        'subtotal', 'tax', 'discount', 'total',
        'payment_method', 'payment_reference', 'paid_at', 'expired_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    // Relationships
    public function user() { return $this->belongsTo(User::class); }
    public function booking() { return $this->belongsTo(Booking::class); }
    public function transactions() { return $this->hasMany(PaymentTransaction::class); }

    // Scopes
    public function scopeExpired($query) {
        return $query->where('expired_at', '<', now())
                     ->where('status', 'pending');
    }
}
```

**`app/Models/PaymentTransaction.php`**

```php
class PaymentTransaction extends Model {
    protected $fillable = [
        'order_id', 'gateway', 'gateway_transaction_id',
        'status', 'amount', 'currency', 'error_message'
    ];

    public function order() { return $this->belongsTo(Order::class); }
}
```

**`app/Models/BookingLock.php`**

```php
class BookingLock extends Model {
    protected $fillable = ['booking_id', 'order_id', 'reason', 'expires_at'];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function order() { return $this->belongsTo(Order::class); }

    // Check if lock still active
    public function isActive() {
        return $this->expires_at->isFuture();
    }
}
```

### Layer 2: Services

**`app/Services/PaymentGatewayService.php`** (Abstract)

```php
abstract class PaymentGatewayService {
    abstract public function initiate(Order $order): PaymentInitiateResponse;
    abstract public function verify(PaymentTransaction $transaction): PaymentVerifyResponse;
    abstract public function refund(Order $order): RefundResponse;
    abstract public function handleWebhook(Request $request): void;
}
```

**`app/Services/StripePaymentService.php`** (Implementation)

```php
class StripePaymentService extends PaymentGatewayService {
    // Integrate dengan Stripe
    // - Create payment intent
    // - Handle webhooks
    // - Verify payments
}
```

**`app/Services/MidtransPaymentService.php`** (Implementation)

```php
class MidtransPaymentService extends PaymentGatewayService {
    // Integrate dengan Midtrans (Verifone)
    // - Snap Token generation
    // - Handle callbacks
    // - Verify transactions
}
```

**`app/Services/OrderService.php`** (Business Logic)

```php
class OrderService {
    public function createOrder(Booking $booking, User $user): Order {
        // Create order
        // Calculate amounts (subtotal, tax, discount)
        // Set expiration time (30 minutes)
        // Lock booking slot
        // Return order with payment URL
    }

    public function processPayment(Order $order, PaymentMethod $method): PaymentResponse {
        // Use appropriate gateway
        // Create payment transaction log
        // Return payment URL or status
    }

    public function handlePaymentSuccess(Order $order, string $transactionId): void {
        // Update order status to 'paid'
        // Update booking status to 'confirmed'
        // Remove booking lock
        // Send confirmation email
        // Log transaction
    }

    public function handlePaymentFailed(Order $order, string $error): void {
        // Update order status to 'failed'
        // Release booking lock
        // Send failure notification
        // Log error
    }

    public function expireUnpaidOrders(): void {
        // Find expired pending orders
        // Release booking locks
        // Send expiry notifications
    }

    public function refundOrder(Order $order, string $reason): RefundResponse {
        // Process refund via gateway
        // Update order status
        // Log refund transaction
    }
}
```

### Layer 3: Controllers

**`app/Http/Controllers/OrderController.php`**

```php
class OrderController extends Controller {
    // GET /bookings/{id}/payment - Show payment page
    public function showPayment(Booking $booking) {
        $order = $booking->order()->latest()->first();
        return view('orders.payment', compact('order'));
    }

    // POST /orders - Create order (redirect to payment)
    public function create(Booking $booking) {
        $order = $this->orderService->createOrder($booking, auth()->user());
        return redirect()->route('orders.payment', $order);
    }

    // GET /orders/{id}/checkout - Payment page
    public function checkout(Order $order) {
        // Get payment methods
        // Generate payment link
        return view('orders.checkout', compact('order'));
    }
}
```

**`app/Http/Controllers/Admin/OrderController.php`**

```php
class Admin\OrderController extends Controller {
    // GET /admin/orders - List all orders
    public function index() {
        $orders = Order::with(['user', 'booking'])
                      ->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    // GET /admin/orders/{id} - Order detail with full history
    public function show(Order $order) {
        $transactions = $order->transactions()->latest()->get();
        return view('admin.orders.show', compact('order', 'transactions'));
    }

    // POST /admin/orders/{id}/update-status - Admin dapat ubah status
    public function updateStatus(Order $order, UpdateOrderStatusRequest $request) {
        // Validate status change
        // Update order status
        // Update booking status jika perlu
        // Remove/add booking locks
        // Log activity
        // Send notification ke user
    }

    // POST /admin/orders/{id}/refund - Process refund
    public function refund(Order $order, RefundRequest $request) {
        // Validate refund
        // Process refund via gateway
        // Update statuses
        // Log activity
    }
}
```

**`app/Http/Controllers/WebhookController.php`**

```php
class WebhookController extends Controller {
    // POST /webhooks/stripe - Stripe webhook
    public function handleStripeWebhook(Request $request) {
        // Verify signature
        // Get payment intent
        // Update order status
        // Send notifications
    }

    // POST /webhooks/midtrans - Midtrans webhook
    public function handleMidtransWebhook(Request $request) {
        // Verify signature
        // Get transaction status
        // Update order status
        // Send notifications
    }
}
```

### Layer 4: Validation

**`app/Http/Requests/UpdateOrderStatusRequest.php`**

```php
class UpdateOrderStatusRequest extends FormRequest {
    public function rules() {
        return [
            'status' => 'required|in:pending,processing,paid,failed,cancelled,refunded',
            'reason' => 'required_if:status,failed|string|max:500',
            'note' => 'nullable|string|max:1000',
        ];
    }
}
```

---

## 3. UI/UX Flow

### Flow 1: Booking → Payment (User Perspective)

```
1. User melihat jadwal lapangan
   ↓
2. User klik "Pesan" (untuk available slot)
   ↓
3. Sistem create Order (status: pending)
   ↓
4. Sistem lock booking slot (booking tidak bisa diambil orang lain)
   ↓
5. Redirect ke halaman pembayaran
   ├─ Show order details (lapangan, tanggal, waktu, total)
   ├─ Show metode pembayaran (card, e-wallet, bank transfer)
   └─ Show harga breakdown (subtotal, tax, discount)
   ↓
6. User pilih metode pembayaran
   ↓
7. Redirect ke payment gateway (Stripe, Midtrans, etc)
   ↓
8. User selesaikan pembayaran di gateway
   ↓
9. Webhook update order status → paid
   ↓
10. Sistem update booking status → confirmed
   ↓
11. Redirect ke success page
    └─ Show confirmation code
    └─ Show booking details
    └─ Send confirmation email
```

### Flow 2: Expired Payment (Automatic)

```
User membuat order → 30 menit tidak bayar → Booking lock expired
├─ Booking slot kembali available
├─ Order status tetap 'pending' (untuk history)
└─ Send expiry notification ke user
```

### Flow 3: Admin Management

```
Admin Dashboard (Orders section)
├─ Table dengan filter:
│  ├─ Status (pending, paid, failed, cancelled)
│  ├─ Date range
│  ├─ User
│  └─ Search
├─ Bulk actions (export, refund)
└─ Detail page per order:
   ├─ Show booking & user info
   ├─ Show payment details
   ├─ Show all transactions (list)
   ├─ Show payment gateway response (JSON viewer)
   ├─ Action buttons:
   │  ├─ [Mark as Paid] - jika error di gateway
   │  ├─ [Mark as Failed] - manual reject
   │  ├─ [Refund] - process refund
   │  └─ [Send Reminder] - send notification
   └─ Activity log (siapa ubah apa kapan)
```

---

## 4. Payment Gateway: XENDIT ✅

### Why XENDIT?

**Perfect untuk Indonesia:**

-   ✅ Indonesian payment gateway (sudah integrated dengan 100+ metode pembayaran)
-   ✅ Fast setup & documentation
-   ✅ Competitive pricing (1.5% - 2.9%)
-   ✅ Real-time webhook
-   ✅ Easy API integration
-   ✅ Sandbox mode untuk testing
-   ✅ Support lokal yang responsif

**Supported Payment Methods (Xendit):**

-   💳 Credit/Debit Card (Visa, Mastercard, JCB)
-   📱 E-wallet (OVO, Dana, LinkAja, DANA, AXA)
-   🏦 Bank Transfer (BCA, Mandiri, BNI, Permata)
-   🔄 BNPL (Kredivo, Akulaku, dll)
-   📲 Retail (Indomaret, Alfamart)

### XENDIT Setup Requirements

```env
# .env
XENDIT_API_KEY=xnd_development_xxxxxxxxxxxxx (dari Xendit dashboard)
XENDIT_PUBLIC_KEY=xnd_public_development_xxxxxxxxxxxxx
XENDIT_WEBHOOK_TOKEN=your_webhook_verification_token
XENDIT_ENVIRONMENT=production OR development
```

### Xendit Integration Flow

```
1. User click "Pesan" pada jadwal lapangan
   ↓
2. App create Order + lock booking
   ↓
3. Redirect ke payment page dengan Xendit inline checkout
   ↓
4. User pilih payment method di Xendit modal
   ↓
5. User complete payment
   ↓
6. Xendit webhook trigger ke /webhooks/xendit
   ↓
7. App verify webhook signature
   ↓
8. Update order status → booking confirmed
   ↓
9. Send success email + redirect to confirmation
```

---

## 5. Admin Booking Dashboard with Orders - UI/UX Design

### Page Layout: `/admin/bookings`

```
┌─────────────────────────────────────────────────────────────────┐
│  🏠 Admin Dashboard > 📅 Bookings                    [Logout]   │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  📊 BOOKINGS OVERVIEW                                            │
│  ┌──────────────┬──────────────┬──────────────┬──────────────┐  │
│  │ Total        │ Confirmed    │ Pending Pay  │ Cancelled    │  │
│  │ 145          │ 102          │ 28           │ 15           │  │
│  │              │              │              │              │  │
│  └──────────────┴──────────────┴──────────────┴──────────────┘  │
│                                                                   │
│  🔍 FILTERS & SEARCH                                             │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │ [Status ▼]  [Date ▼]  [Field ▼]  [Search User...]      [🔄] │ │
│  │ ☐ Confirmed  ☐ Pending Payment  ☐ Cancelled  ☐ Failed      │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                                                                   │
│  📋 BOOKINGS TABLE                                               │
│  ┌──────┬────────┬─────────┬────────┬──────────┬──────────────┐  │
│  │ ID   │ User   │ Lapang. │ Date   │ Status   │ Action       │  │
│  ├──────┼────────┼─────────┼────────┼──────────┼──────────────┤  │
│  │ #001 │ Rosi   │ Field A │ Nov 15 │ ✅       │ [View Order] │  │
│  │      │        │ 19:00   │ 2025   │ Confirm. │              │  │
│  ├──────┼────────┼─────────┼────────┼──────────┼──────────────┤  │
│  │ #002 │ Budi   │ Field B │ Nov 16 │ ⏳       │ [View Order] │  │
│  │      │        │ 20:00   │ 2025   │ Pend.Pay │              │  │
│  ├──────┼────────┼─────────┼────────┼──────────┼──────────────┤  │
│  │ #003 │ Andi   │ Field A │ Nov 17 │ ❌       │ [View Order] │  │
│  │      │        │ 18:00   │ 2025   │ Failed   │              │  │
│  └──────┴────────┴─────────┴────────┴──────────┴──────────────┘  │
│                                                                   │
│  << Page 1 of 8 >>  [Rows per page: 10 ▼]                       │
└─────────────────────────────────────────────────────────────────┘
```

### Status Indicators

```
✅ Confirmed     - Pembayaran selesai, booking confirmed
⏳ Pending Pay   - Waiting pembayaran (30 menit)
⚠️  Failed       - Pembayaran failed
❌ Cancelled    - User/admin cancel booking
🔄 Processing   - Payment sedang diproses
```

---

## 6. Order Detail Page - `/admin/orders/{id}` - UI/UX Design

### Complete Order Detail Layout

```
┌──────────────────────────────────────────────────────────────────┐
│  🏠 Admin Dashboard > 📋 Orders > Order #INV-20251108-001       │
├──────────────────────────────────────────────────────────────────┤
│                                                                    │
│  ╔════════════════════════════════════════════════════════════╗  │
│  ║  ORDER SUMMARY                                             ║  │
│  ╠════════════════════════════════════════════════════════════╣  │
│  ║  Order ID: INV-20251108-001                               ║  │
│  ║  Status: 🟢 PAID (Updated: Nov 8, 2025 14:20)             ║  │
│  ║  Created: Nov 8, 2025 13:50                               ║  │
│  ╚════════════════════════════════════════════════════════════╝  │
│                                                                    │
│  ┌─────────────────────┬──────────────────────────────────────┐  │
│  │ 👤 USER INFO        │ 📅 BOOKING INFO                      │  │
│  ├─────────────────────┼──────────────────────────────────────┤  │
│  │ Name: Rosi Kusuma   │ Lapangan: Futsal Neo A               │  │
│  │ Email: rosi@...     │ Date: Nov 15, 2025                   │  │
│  │ Phone: 081234567890 │ Time: 19:00 - 20:00 (1 jam)          │  │
│  │ Member Since: ...   │ Location: Jl. Sudirman No. 123       │  │
│  └─────────────────────┴──────────────────────────────────────┘  │
│                                                                    │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │ 💰 PAYMENT DETAILS                                         │  │
│  ├────────────────────────────────────────────────────────────┤  │
│  │ Subtotal        : Rp 150,000                               │  │
│  │ Tax (10%)       : Rp  15,000                               │  │
│  │ Discount        : Rp   5,000                               │  │
│  │ ─────────────────────────────────                          │  │
│  │ Total           : Rp 160,000  ← Final Amount               │  │
│  │                                                            │  │
│  │ Payment Method  : 💳 Bank Transfer (BCA)                  │  │
│  │ Payment Status  : ✅ SUCCESS                               │  │
│  │ Paid At         : Nov 8, 2025 14:15                        │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                    │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │ 🔐 TRANSACTION DETAILS (Xendit)                            │  │
│  ├────────────────────────────────────────────────────────────┤  │
│  │ Gateway              : XENDIT                              │  │
│  │ Transaction ID       : xendit_6757a8d9c7...               │  │
│  │ Reference Number     : BCA_TRF_1234567890                  │  │
│  │ Payment Channel      : Bank Transfer                       │  │
│  │ Verified At          : Nov 8, 2025 14:15:32               │  │
│  │ Gateway Status       : COMPLETED                           │  │
│  │                                                            │  │
│  │ [📄 View Gateway Response (JSON)] [📥 Download Receipt]    │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                    │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │ 📝 TRANSACTION HISTORY                                     │  │
│  ├────────────────────────────────────────────────────────────┤  │
│  │                                                            │  │
│  │  📌 Nov 8, 14:15:32 - Payment Completed                   │  │
│  │     Status: SUCCESS                                        │  │
│  │     Amount: Rp 160,000                                     │  │
│  │     Gateway: XENDIT                                        │  │
│  │                                                            │  │
│  │  📌 Nov 8, 14:10:00 - Payment Initiated                   │  │
│  │     Status: PENDING                                        │  │
│  │     Amount: Rp 160,000                                     │  │
│  │     Method: Bank Transfer                                  │  │
│  │                                                            │  │
│  │  📌 Nov 8, 13:50:00 - Order Created                        │  │
│  │     Status: PENDING_PAYMENT                                │  │
│  │     Expires: Nov 8, 14:20:00 (30 min timeout)              │  │
│  │                                                            │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                    │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │ ⚙️  ADMIN ACTIONS                                          │  │
│  ├────────────────────────────────────────────────────────────┤  │
│  │                                                            │  │
│  │  ┌─────────────────────────────────────────────────────┐  │  │
│  │  │ Current Status: 🟢 PAID                             │  │  │
│  │  │                                                     │  │  │
│  │  │ Change Status:                                      │  │  │
│  │  │ [✅ Mark as Paid]       (untuk manual override)     │  │  │
│  │  │ [❌ Mark as Failed]     (untuk reject transaksi)    │  │  │
│  │  │ [🔄 Mark as Processing] (untuk manual hold)        │  │  │
│  │  │ [💸 Refund Order]       (process refund)           │  │  │
│  │  │ [📧 Send Reminder]      (send notification)        │  │  │
│  │  │                                                     │  │  │
│  │  │ [📄 Notes]  [Add admin note...]                    │  │  │
│  │  │ [🔗 Delete] (only for testing/error)               │  │  │
│  │  └─────────────────────────────────────────────────────┘  │  │
│  │                                                            │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                    │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │ 📋 ACTIVITY LOG                                            │  │
│  ├────────────────────────────────────────────────────────────┤  │
│  │                                                            │  │
│  │  🔔 Nov 8, 14:30 - Admin Viewed Order                    │  │
│  │     By: admin@futsal.com                                  │  │
│  │                                                            │  │
│  │  ✅ Nov 8, 14:15 - Payment Success (Webhook)             │  │
│  │     By: XENDIT_WEBHOOK                                    │  │
│  │     Reason: Payment confirmed by gateway                  │  │
│  │                                                            │  │
│  │  🟡 Nov 8, 13:50 - Order Created                          │  │
│  │     By: rosi@futsal.com (Customer)                        │  │
│  │                                                            │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                    │
│  [⬅️  Back to Orders] [🔄 Refresh] [⚙️ Settings]                 │
└──────────────────────────────────────────────────────────────────┘
```

### Status Change Modal

```
┌─────────────────────────────────────────────────┐
│  ⚠️  Change Order Status                         │
├─────────────────────────────────────────────────┤
│                                                  │
│  Current Status: 🟢 PAID                        │
│  New Status:                                    │
│  ○ ✅ Mark as Paid                              │
│  ○ ❌ Mark as Failed                            │
│  ○ 🔄 Mark as Processing                       │
│  ○ 💸 Refund                                    │
│                                                  │
│  Reason (Required):                             │
│  ┌──────────────────────────────────────────┐   │
│  │ Select reason...                         │   │
│  ├──────────────────────────────────────────┤   │
│  │ Manual override - Gateway error          │   │
│  │ Customer requested refund                │   │
│  │ Suspicious transaction                   │   │
│  │ Test/Development                         │   │
│  │ Other...                                 │   │
│  └──────────────────────────────────────────┘   │
│                                                  │
│  Admin Note (Optional):                         │
│  ┌──────────────────────────────────────────┐   │
│  │ Add any additional notes...              │   │
│  │ [                                    ]   │   │
│  └──────────────────────────────────────────┘   │
│                                                  │
│  [✅ Confirm]  [❌ Cancel]                       │
└─────────────────────────────────────────────────┘
```

### Gateway Response Viewer (JSON)

```
┌─────────────────────────────────────────────────┐
│  📄 Xendit Gateway Response (Raw JSON)           │
├─────────────────────────────────────────────────┤
│                                                  │
│ {                                               │
│   "id": "xendit_6757a8d9c7...",                │
│   "business_id": "5f1234567890...",            │
│   "reference_id": "INV-20251108-001",          │
│   "status": "COMPLETED",                        │
│   "currency": "IDR",                            │
│   "amount": 160000,                             │
│   "payment_method": "BANK_TRANSFER",            │
│   "bank_code": "BCA",                           │
│   "description": "Futsal Booking - Field A",   │
│   "created": "2025-11-08T13:50:00Z",           │
│   "updated": "2025-11-08T14:15:32Z",           │
│   "paid_at": "2025-11-08T14:15:32Z",           │
│   "channel_properties": {                       │
│     "reference_number": "BCA_TRF_1234567890",  │
│     "account_holder_name": "PT. Futsal Neo"     │
│   }                                             │
│ }                                               │
│                                                  │
│ [📋 Copy] [📥 Download] [❌ Close]              │
└─────────────────────────────────────────────────┘
```

---

## 7. Order Status Update Modal - Manual Override

### Update Status Dialog

```
┌────────────────────────────────────────────────┐
│  ✏️ Update Order Status (Manual Override)      │
├────────────────────────────────────────────────┤
│                                                 │
│  Order: INV-20251108-001                       │
│  Current Status: ⏳ PENDING_PAYMENT             │
│                                                 │
│  ┌──────────────────────────────────────────┐  │
│  │ New Status:                              │  │
│  ├──────────────────────────────────────────┤  │
│  │ ○ 🟢 PAID                                │  │
│  │ ○ ❌ FAILED                              │  │
│  │ ○ 🔄 PROCESSING                         │  │
│  │ ○ 💸 REFUNDED                           │  │
│  │ ○ 🚫 CANCELLED                          │  │
│  └──────────────────────────────────────────┘  │
│                                                 │
│  Reason (Required):                            │
│  ┌──────────────────────────────────────────┐  │
│  │ Why are you changing this status?       │  │
│  ├──────────────────────────────────────────┤  │
│  │ ▼ Select or type...                     │  │
│  │  • Gateway timeout error                │  │
│  │  • Duplicate payment detected           │  │
│  │  • Manual customer request              │  │
│  │  • System error recovery                │  │
│  │  • Other...                             │  │
│  └──────────────────────────────────────────┘  │
│                                                 │
│  Admin Notes:                                  │
│  ┌──────────────────────────────────────────┐  │
│  │ Additional information...               │  │
│  │                                          │  │
│  │                                          │  │
│  └──────────────────────────────────────────┘  │
│                                                 │
│  ⚠️  Important:                                │
│  If marking as PAID: Booking will auto         │
│  change to CONFIRMED and user will be notified │
│                                                 │
│  [✅ Update Status]  [❌ Cancel]                │
│                                                 │
└────────────────────────────────────────────────┘
```

---

## 8. Refund Processing Modal

```
┌────────────────────────────────────────────────┐
│  💸 Process Refund                             │
├────────────────────────────────────────────────┤
│                                                 │
│  Order: INV-20251108-001                       │
│  Amount to Refund: Rp 160,000                  │
│  Payment Method: Bank Transfer (BCA)           │
│                                                 │
│  Refund Type:                                  │
│  ○ Full Refund (Rp 160,000)                    │
│  ○ Partial Refund                              │
│    Amount: [Rp ________]                       │
│                                                 │
│  Reason:                                       │
│  ┌──────────────────────────────────────────┐  │
│  │ • Customer cancelled booking             │  │
│  │ • Double payment                         │  │
│  │ • Technical error                        │  │
│  │ • Other...                               │  │
│  └──────────────────────────────────────────┘  │
│                                                 │
│  Refund Note (visible to customer):            │
│  ┌──────────────────────────────────────────┐  │
│  │ Refund will be processed within 1-2 days│  │
│  │                                          │  │
│  └──────────────────────────────────────────┘  │
│                                                 │
│  ✅ This will:                                 │
│    • Send refund to gateway                    │
│    • Change order status to REFUNDED           │
│    • Change booking status to CANCELLED        │
│    • Unlock booking slot for others            │
│    • Send notification to customer             │
│                                                 │
│  [✅ Process Refund]  [❌ Cancel]              │
│                                                 │
└────────────────────────────────────────────────┘
```

---

## 9. Booking Admin Dashboard Widget

### Booking List with Order Status

```
📅 RECENT BOOKINGS (with Order Status)

┌────┬─────────┬─────────┬───────┬──────────────┬────────────┐
│ ID │ User    │ Lapang  │ Date  │ Order Status │ Action     │
├────┼─────────┼─────────┼───────┼──────────────┼────────────┤
│001 │ Rosi    │ Field A │ Nov15 │ ✅ PAID      │ [View]     │
│    │         │ 19:00   │       │ INV-...      │            │
├────┼─────────┼─────────┼───────┼──────────────┼────────────┤
│002 │ Budi    │ Field B │ Nov16 │ ⏳ PENDING   │ [View]     │
│    │         │ 20:00   │       │ exp 14:20    │            │
├────┼─────────┼─────────┼───────┼──────────────┼────────────┤
│003 │ Andi    │ Field A │ Nov17 │ ❌ FAILED    │ [View]     │
│    │         │ 18:00   │       │ INV-...      │ [Retry]    │
├────┼─────────┼─────────┼───────┼──────────────┼────────────┤
│004 │ Citra   │ Field C │ Nov18 │ 💸 REFUNDED  │ [View]     │
│    │         │ 21:00   │       │ INV-...      │            │
└────┴─────────┴─────────┴───────┴──────────────┴────────────┘

Legend:
✅ PAID - Booking confirmed
⏳ PENDING - Waiting payment (time countdown)
❌ FAILED - Payment failed
💸 REFUNDED - Refund processed
🔄 PROCESSING - Payment processing
```

---

## 10. User Payment Page - `/orders/{id}/checkout` - UI/UX

```
┌──────────────────────────────────────────────────────────┐
│  Futsal Neo - Pembayaran Lapangan                    [X]  │
├──────────────────────────────────────────────────────────┤
│                                                            │
│  ⏱️  WAKTU PEMBAYARAN: 29:45                              │
│  [████████░░░░░░░░░░░░░░░░░░░░░░░] 1 menit tersisa       │
│                                                            │
│  ┌────────────────────────────────────────────────────┐   │
│  │ 📋 RINGKASAN PESANAN                              │   │
│  ├────────────────────────────────────────────────────┤   │
│  │                                                    │   │
│  │ Lapangan        : Futsal Neo A                     │   │
│  │ Tanggal         : 15 November 2025                 │   │
│  │ Jam             : 19:00 - 20:00 (1 jam)            │   │
│  │ Lokasi          : Jl. Sudirman No. 123             │   │
│  │                                                    │   │
│  │ ─────────────────────────────────────────         │   │
│  │ Harga per jam   : Rp 150,000                       │   │
│  │ Pajak (10%)     : Rp  15,000                       │   │
│  │ Diskon          : Rp   5,000                       │   │
│  │ ─────────────────────────────────────────         │   │
│  │ TOTAL           : Rp 160,000 💰                    │   │
│  │                                                    │   │
│  └────────────────────────────────────────────────────┘   │
│                                                            │
│  ┌────────────────────────────────────────────────────┐   │
│  │ 💳 PILIH METODE PEMBAYARAN                        │   │
│  ├────────────────────────────────────────────────────┤   │
│  │                                                    │   │
│  │ ☐ 💳 Kartu Kredit/Debit                           │   │
│  │   (Visa, Mastercard, JCB)                         │   │
│  │                                                    │   │
│  │ ☐ 📱 E-Wallet                                      │   │
│  │   (OVO, Dana, LinkAja, Gopay)                     │   │
│  │                                                    │   │
│  │ ⦿ 🏦 Transfer Bank                                │   │
│  │   (BCA, Mandiri, BNI, Permata, dll)               │   │
│  │                                                    │   │
│  │ ☐ 📦 Cicilan (BNPL)                               │   │
│  │   (Kredivo, Akulaku, Cicilan Paylater)           │   │
│  │                                                    │   │
│  │ ☐ 🏪 Retail                                        │   │
│  │   (Indomaret, Alfamart)                           │   │
│  │                                                    │   │
│  └────────────────────────────────────────────────────┘   │
│                                                            │
│  ┌────────────────────────────────────────────────────┐   │
│  │ [🔒 BAYAR SEKARANG - Rp 160,000]                   │   │
│  │ Redirect ke Xendit Payment Gateway                │   │
│  └────────────────────────────────────────────────────┘   │
│                                                            │
│  ℹ️  Pembayaran aman & terenkripsi (PCI Compliance)      │
│  Powered by Xendit 🔐                                     │
│                                                            │
│  [← Kembali ke Booking]  [?] Bantuan                      │
│                                                            │
└──────────────────────────────────────────────────────────┘
```

---

## 11. Payment Success Page - `/orders/{id}/success`

```
┌──────────────────────────────────────────────────────────┐
│                                                            │
│                   ✅ PEMBAYARAN BERHASIL!                 │
│                                                            │
│              Lapangan Anda telah dikonfirmasi              │
│                                                            │
├──────────────────────────────────────────────────────────┤
│                                                            │
│  📋 NOMOR KONFIRMASI                                      │
│  ┌────────────────────────────────────────────────────┐   │
│  │ INV-20251108-001                                   │   │
│  │ [📋 Salin]                                         │   │
│  └────────────────────────────────────────────────────┘   │
│                                                            │
│  🎉 DETAIL PEMESANAN ANDA                                │
│  ┌────────────────────────────────────────────────────┐   │
│  │ Lapangan    : Futsal Neo A                         │   │
│  │ Tanggal     : 15 November 2025                     │   │
│  │ Jam         : 19:00 - 20:00                        │   │
│  │ Lokasi      : Jl. Sudirman No. 123                 │   │
│  │ Total       : Rp 160,000                           │   │
│  │ Status      : ✅ CONFIRMED                          │   │
│  │ Pembayaran  : ✅ BERHASIL                           │   │
│  └────────────────────────────────────────────────────┘   │
│                                                            │
│  💌 Kami telah mengirimkan bukti pembayaran ke email     │
│     Periksa folder inbox atau spam Anda                  │
│                                                            │
│  📞 Pertanyaan? Hubungi: 081234567890                    │
│  📧 Email: support@futsalneo.com                         │
│                                                            │
│  [🏠 Kembali ke Dashboard]  [📄 Unduh Receipt]            │
│                                                            │
└──────────────────────────────────────────────────────────┘
```

---

## 12. Xendit Integration Implementation Guide

### Step 1: Install Xendit Package

```bash
composer require xendit/xendit-php
```

### Step 2: Environment Configuration

```env
# .env
XENDIT_SECRET_KEY=xnd_development_xxxxxxxxxxxxx
XENDIT_PUBLIC_KEY=xnd_public_development_xxxxxxxxxxxxx
XENDIT_WEBHOOK_TOKEN=your_webhook_verification_token_123
XENDIT_ENVIRONMENT=development

# Payment Config
ORDER_EXPIRY_MINUTES=30
PAYMENT_TIMEOUT=30
```

### Step 3: Service Implementation

**`app/Services/XenditPaymentService.php`**

```php
<?php
namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;

class XenditPaymentService {
    protected $invoiceApi;

    public function __construct() {
        Configuration::setXenditKey(config('xendit.secret_key'));
        $this->invoiceApi = new InvoiceApi();
    }

    /**
     * Create payment invoice di Xendit
     */
    public function createInvoice(Order $order): array {
        $payload = [
            'reference_id' => $order->order_number,
            'currency' => 'IDR',
            'amount' => $order->total,
            'description' => "Futsal Booking - {$order->booking->field->name}",
            'invoice_expiration' => now()->addMinutes(30)->timestamp,
            'customer_name' => $order->user->name,
            'customer_email' => $order->user->email,
            'customer_mobile_number' => $order->user->phone,
            'items' => [
                [
                    'name' => $order->booking->field->name,
                    'quantity' => 1,
                    'price' => $order->subtotal,
                ]
            ],
            'fees' => [
                [
                    'type' => 'TAX',
                    'value' => $order->tax,
                ]
            ],
            'success_redirect_url' => route('orders.success', $order),
            'failure_redirect_url' => route('orders.failed', $order),
            'metadata' => [
                'booking_id' => $order->booking_id,
                'user_id' => $order->user_id,
            ],
        ];

        try {
            $response = $this->invoiceApi->createInvoice($payload);

            // Log transaction
            PaymentTransaction::create([
                'order_id' => $order->id,
                'gateway' => 'xendit',
                'gateway_transaction_id' => $response['id'],
                'status' => 'pending',
                'amount' => $order->total,
                'currency' => 'IDR',
                'request_payload' => json_encode($payload),
                'response_payload' => json_encode($response),
            ]);

            return [
                'success' => true,
                'invoice_id' => $response['id'],
                'payment_url' => $response['invoice_url'],
                'expires_at' => $response['expiry_date'],
            ];

        } catch (\Exception $e) {
            PaymentTransaction::create([
                'order_id' => $order->id,
                'gateway' => 'xendit',
                'status' => 'failed',
                'amount' => $order->total,
                'error_message' => $e->getMessage(),
                'request_payload' => json_encode($payload),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify payment dari webhook
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool {
        $expected = hash_hmac(
            'sha256',
            $payload,
            config('xendit.webhook_token')
        );
        return hash_equals($expected, $signature);
    }

    /**
     * Get invoice status dari Xendit
     */
    public function getInvoiceStatus(string $invoiceId): array {
        try {
            $response = $this->invoiceApi->getInvoiceById(['invoice_id' => $invoiceId]);
            return [
                'success' => true,
                'status' => $response['status'],
                'paid_amount' => $response['paid_amount'] ?? 0,
                'data' => $response,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
```

### Step 4: Controller Implementation

**`app/Http/Controllers/OrderController.php`**

```php
<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Booking;
use App\Models\BookingLock;
use App\Services\XenditPaymentService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller {

    public function __construct(
        protected XenditPaymentService $xendit,
        protected OrderService $orderService
    ) {}

    /**
     * Create order & lock booking
     * POST /orders
     */
    public function create(Booking $booking) {
        // Check if user owns this booking request
        if (auth()->user()->bookings()->where('field_id', $booking->field_id)->exists()) {
            return abort(403, 'Already booked');
        }

        // Check if slot is available
        if (!$booking->is_available()) {
            return abort(400, 'Slot not available');
        }

        // Create order
        $order = $this->orderService->createOrder($booking, auth()->user());

        // Lock booking
        BookingLock::create([
            'booking_id' => $booking->id,
            'order_id' => $order->id,
            'reason' => 'payment_pending',
            'expires_at' => now()->addMinutes(30),
        ]);

        return redirect()->route('orders.checkout', $order);
    }

    /**
     * Show checkout page
     * GET /orders/{id}/checkout
     */
    public function checkout(Order $order) {
        // Verify ownership
        if ($order->user_id !== auth()->id()) {
            return abort(403);
        }

        // Check if order still pending
        if ($order->status !== 'pending') {
            return abort(400, 'Order already processed');
        }

        // Check if expired
        if ($order->expired_at < now()) {
            return abort(400, 'Order expired');
        }

        return view('orders.checkout', compact('order'));
    }

    /**
     * Initiate payment
     * POST /orders/{id}/pay
     */
    public function initiatePayment(Order $order) {
        if ($order->user_id !== auth()->id()) {
            return abort(403);
        }

        if ($order->status !== 'pending' || $order->expired_at < now()) {
            return abort(400, 'Order invalid or expired');
        }

        // Create Xendit invoice
        $response = $this->xendit->createInvoice($order);

        if (!$response['success']) {
            return back()->withErrors('Failed to initiate payment');
        }

        // Update order with invoice ID
        $order->update([
            'payment_reference' => $response['invoice_id'],
        ]);

        // Redirect to Xendit payment page
        return redirect()->away($response['payment_url']);
    }

    /**
     * Success callback
     * GET /orders/{id}/success
     */
    public function success(Order $order) {
        return view('orders.success', compact('order'));
    }

    /**
     * Failed callback
     * GET /orders/{id}/failed
     */
    public function failed(Order $order) {
        return view('orders.failed', compact('order'));
    }
}
```

**`app/Http/Controllers/WebhookController.php`** (Webhook Handler)

```php
<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\BookingLock;
use App\Services\XenditPaymentService;
use Illuminate\Http\Request;

class WebhookController extends Controller {

    public function __construct(protected XenditPaymentService $xendit) {}

    /**
     * Handle Xendit webhook
     * POST /webhooks/xendit
     */
    public function handleXenditWebhook(Request $request) {
        // Verify signature
        $signature = $request->header('X-Callback-Verification');
        if (!$this->xendit->verifyWebhookSignature(
            $request->getContent(),
            $signature
        )) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->all();

        // Handle invoice paid
        if ($data['event'] === 'invoice.paid') {
            return $this->handleInvoicePaid($data);
        }

        // Handle invoice expired
        if ($data['event'] === 'invoice.expired') {
            return $this->handleInvoiceExpired($data);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle invoice paid event
     */
    private function handleInvoicePaid(array $data) {
        $order = Order::where(
            'payment_reference',
            $data['data']['id']
        )->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Update transaction
        PaymentTransaction::where(
            'gateway_transaction_id',
            $data['data']['id']
        )->update([
            'status' => 'success',
            'response_payload' => json_encode($data['data']),
        ]);

        // Update order
        $order->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Update booking & remove lock
        $booking = $order->booking;
        $booking->update(['status' => 'confirmed']);

        BookingLock::where('order_id', $order->id)->delete();

        // Send notification
        $order->user->notify(new \App\Notifications\PaymentConfirmed($order));

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle invoice expired event
     */
    private function handleInvoiceExpired(array $data) {
        $order = Order::where(
            'payment_reference',
            $data['data']['id']
        )->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Release booking lock
        BookingLock::where('order_id', $order->id)->delete();

        // Send notification
        $order->user->notify(new \App\Notifications\PaymentExpired($order));

        return response()->json(['status' => 'ok']);
    }
}
```

### Step 5: Routes Configuration

**`routes/web.php`**

```php
// Order routes (Customer)
Route::middleware('auth')->group(function () {
    Route::post('/orders', [OrderController::class, 'create'])->name('orders.create');
    Route::get('/orders/{order}/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::post('/orders/{order}/pay', [OrderController::class, 'initiatePayment'])->name('orders.pay');
    Route::get('/orders/{order}/success', [OrderController::class, 'success'])->name('orders.success');
    Route::get('/orders/{order}/failed', [OrderController::class, 'failed'])->name('orders.failed');
});

// Webhook (public)
Route::post('/webhooks/xendit', [WebhookController::class, 'handleXenditWebhook'])->name('webhooks.xendit');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('orders', Admin\OrderController::class);
    Route::patch('/orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/refund', [Admin\OrderController::class, 'refund'])->name('orders.refund');
});
```

---

## 13. Best Practices Implementation

### Security

```php
// ✅ Always verify gateway signature
public function verifyGatewaySignature($payload, $signature) {
    $hash = hash_hmac('sha256', $payload, config('payment.gateway_secret'));
    return hash_equals($hash, $signature);
}

// ✅ Use idempotency keys
$order->update(['idempotency_key' => Str::uuid()]);

// ✅ PCI Compliance
// - Never store full card details
// - Use tokenized payment methods
// - Use 3D Secure for added security

// ✅ Rate limiting
Route::middleware('throttle:10,1')->post('/webhook', [WebhookController::class]);
```

### User Experience

```php
// ✅ Preserve booking info saat pending
Order {
    id: 1,
    status: 'pending',
    booking: {
        field: 'Lapangan A',
        date: '2025-11-15',
        time: '19:00 - 20:00'
    },
    expired_at: '2025-11-08 14:30'  // 30 min from now
}

// ✅ Show countdown timer di payment page
<div class="countdown">
    Time remaining: <span id="timer">29:45</span>
</div>

// ✅ Show order summary
Order #INV-20251108-001
Lapangan A | Nov 15, 2025 | 19:00-20:00
Subtotal: Rp 150,000
Tax (10%): Rp 15,000
─────────────────────────
Total: Rp 165,000
```

### Reliability

```php
// ✅ Automatic retry mechanism
public function processPayment(Order $order) {
    retry(3, function() use ($order) {
        return $this->gateway->charge($order);
    }, delay: 2000); // Wait 2 seconds between retries
}

// ✅ Duplicate prevention
if (Order::where('idempotency_key', $key)->exists()) {
    return Order::where('idempotency_key', $key)->first();
}

// ✅ Timeout handling
$gateway->setTimeout(30); // 30 seconds max
$gateway->setRetry(true);
```

### Admin Control

```php
// ✅ Admin dapat manual override
- Mark order as paid (jika gateway error)
- Mark order as failed (jika suspicious)
- Refund dengan alasan custom
- View semua transaction logs
- Audit trail untuk semua changes

// ✅ Activity logging
Activity::log('order-updated', [
    'order_id' => $order->id,
    'old_status' => 'pending',
    'new_status' => 'paid',
    'changed_by' => auth()->user()->id,
    'reason' => 'Manual override - gateway error',
    'changed_at' => now(),
]);
```

---

## 20. Feature Checklist

### Core Features ✅

-   [ ] Order creation & management
-   [ ] Booking slot locking mechanism
-   [ ] Multiple payment gateway support
-   [ ] Payment method tokenization
-   [ ] Webhook handling
-   [ ] Automatic expiry handling
-   [ ] Admin order dashboard
-   [ ] Order detail page with full history
-   [ ] Admin manual status update
-   [ ] Refund processing
-   [ ] Activity logging
-   [ ] Notification system

### User Friendly Features 🎯

-   [ ] Countdown timer on payment page
-   [ ] Order summary with breakdown
-   [ ] Booking confirmation email
-   [ ] Payment receipt PDF
-   [ ] Order history in user dashboard
-   [ ] Payment method manager
-   [ ] Saved payment methods
-   [ ] One-click payment with saved method
-   [ ] Transaction history view
-   [ ] Refund status tracking

### Admin Powerful Features 💪

-   [ ] Orders list with filters
-   [ ] Bulk actions (export, refund)
-   [ ] Order detail with transaction history
-   [ ] JSON response viewer (for debugging)
-   [ ] Manual status update with reason
-   [ ] Refund processing
-   [ ] Admin notes per order
-   [ ] Activity log / audit trail
-   [ ] Payment gateway logs
-   [ ] Revenue reports

### Backend Features ⚙️

-   [ ] Expired order automatic cleanup (cron job)
-   [ ] Failed payment retries
-   [ ] Idempotency key handling
-   [ ] Duplicate payment prevention
-   [ ] Rate limiting per user
-   [ ] IP whitelisting untuk webhook
-   [ ] Signature verification
-   [ ] Timeout & retry logic
-   [ ] Error logging & alerting
-   [ ] Encryption untuk sensitive data

---

## 21. Database Migrations Order

```
1. Create `orders` table
   - Store order details & status
   - Link to booking & user

2. Create `payment_methods` table
   - Store saved payment methods

3. Create `payment_transactions` table
   - Log every transaction attempt

4. Create `booking_locks` table
   - Prevent double booking

5. Add migration to modify `bookings` table
   - Add status: 'confirmed', 'pending_payment'
   - Drop redundant fields

6. Create table `order_activities`
   - Activity log untuk audit trail
```

---

## 22. Implementation Priority

### Phase 1: Foundation (Week 1-2)

-   [ ] Create database tables
-   [ ] Create models & relationships
-   [ ] Create OrderService class
-   [ ] Implement basic payment flow
-   [ ] Integrate Midtrans

### Phase 2: Admin Panel (Week 2-3)

-   [ ] Admin orders dashboard
-   [ ] Order detail page
-   [ ] Manual status update
-   [ ] Refund processing

### Phase 3: Polish (Week 3-4)

-   [ ] Notifications & emails
-   [ ] User dashboard orders
-   [ ] Payment method manager
-   [ ] Reports & analytics

### Phase 4: Advanced (Week 4+)

-   [ ] Multiple gateway support
-   [ ] Recurring payments
-   [ ] Subscription plans
-   [ ] Revenue analytics

---

## 23. Configuration & Environment

```env
# .env
PAYMENT_GATEWAY=midtrans
MIDTRANS_SERVER_KEY=SB-Mid-...
MIDTRANS_CLIENT_KEY=Mid-...
MIDTRANS_ENVIRONMENT=sandbox

STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...

PAYMENT_TIMEOUT=30
PAYMENT_RETRY_COUNT=3
ORDER_EXPIRY_MINUTES=30
```

---

## 24. Testing Strategy

```php
// Unit Tests
public function test_order_creation_locks_booking() { ... }
public function test_expired_order_releases_booking() { ... }
public function test_payment_success_confirms_booking() { ... }

// Integration Tests
public function test_midtrans_webhook_updates_order() { ... }
public function test_stripe_webhook_refund_works() { ... }

// Feature Tests
public function test_user_can_book_and_pay() { ... }
public function test_admin_can_manually_update_order() { ... }
public function test_concurrent_bookings_prevented() { ... }

// Load Tests
// - 100 concurrent payment attempts
// - Webhook rate limiting
// - DB lock handling
```

---

## 25. Monitoring & Alerts

```php
// Setup alerts untuk:
- Payment failures > 5% per hour
- Webhook failures
- Duplicate payment attempts
- Failed refunds
- Expired orders > 1000 per day
- Admin actions on sensitive orders

// Setup logs untuk:
- All payment transactions
- All gateway responses
- All webhook deliveries
- All admin actions
- All errors & exceptions
```

---

## 26. Recommended Tech Stack

```
Payment Gateway: Midtrans (primary) + Stripe (future)
Queue: Laravel Queue + Redis untuk webhook handling
Cache: Redis untuk order state & booking locks
Job: Scheduled job untuk expired orders cleanup
Notification: Email + SMS + Push notifications
Logging: Structured logging to file + monitoring service
```

---

## Summary

Dengan plan ini, Anda akan mendapatkan:

✅ **Professional Payment System**

-   Aman, reliable, user-friendly
-   Production-ready architecture
-   Scalable untuk growth

✅ **Smart Booking Protection**

-   Automatic slot locking
-   Prevent overbooking
-   Auto-release setelah timeout

✅ **Powerful Admin Control**

-   Full visibility ke transactions
-   Manual override capability
-   Comprehensive audit trail

✅ **Best Practices**

-   Security: PCI compliance
-   UX: Smooth payment flow
-   Reliability: Error handling & retries

Apakah Anda ingin saya mulai implement Phase 1? 🚀
