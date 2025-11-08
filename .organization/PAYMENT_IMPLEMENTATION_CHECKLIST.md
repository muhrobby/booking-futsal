# Payment Gateway Implementation - Detailed Checklist

**Status**: 🚀 Ready to Implement  
**Gateway**: Xendit  
**Estimated Timeline**: 2-3 weeks

---

## Phase 1: Database & Models (Week 1)

### Database Migrations

-   [ ] **Migration 1: Create `orders` Table**

    ```bash
    php artisan make:migration create_orders_table
    ```

    -   [ ] Columns: user_id, booking_id, order_number, status
    -   [ ] Columns: subtotal, tax, discount, total
    -   [ ] Columns: payment_method, payment_reference, paid_at, expired_at
    -   [ ] Foreign keys & indexes
    -   [ ] Run: `php artisan migrate`

-   [ ] **Migration 2: Create `payment_methods` Table**

    ```bash
    php artisan make:migration create_payment_methods_table
    ```

    -   [ ] Columns: user_id, type, last_four, brand
    -   [ ] Columns: gateway_customer_id, gateway_payment_method_id
    -   [ ] Columns: is_default, is_active
    -   [ ] Foreign keys & indexes

-   [ ] **Migration 3: Create `payment_transactions` Table**

    ```bash
    php artisan make:migration create_payment_transactions_table
    ```

    -   [ ] Columns: order_id, gateway, gateway_transaction_id
    -   [ ] Columns: status, amount, currency, error_message
    -   [ ] Columns: request_payload, response_payload
    -   [ ] Foreign keys & indexes

-   [ ] **Migration 4: Create `booking_locks` Table**
    ```bash
    php artisan make:migration create_booking_locks_table
    ```
    -   [ ] Columns: booking_id, order_id, locked_at, expires_at
    -   [ ] Columns: reason
    -   [ ] Unique constraint on active locks
    -   [ ] Indexes for performance

### Models Creation

-   [ ] **Create `Order` Model**

    -   [ ] File: `app/Models/Order.php`
    -   [ ] Relationships: user, booking, transactions
    -   [ ] Scopes: expired, pending, paid
    -   [ ] Fillable attributes
    -   [ ] Status casting

-   [ ] **Create `PaymentTransaction` Model**

    -   [ ] File: `app/Models/PaymentTransaction.php`
    -   [ ] Relationship: order
    -   [ ] Casts for JSON payloads

-   [ ] **Create `BookingLock` Model**

    -   [ ] File: `app/Models/BookingLock.php`
    -   [ ] Relationships: booking, order
    -   [ ] Methods: isActive(), release()

-   [ ] **Update `Booking` Model**

    -   [ ] Add relationship: orders
    -   [ ] Add method: isLocked()
    -   [ ] Add method: getActiveLock()

-   [ ] **Update `User` Model**
    -   [ ] Add relationship: orders
    -   [ ] Add relationship: paymentMethods

---

## Phase 2: Services & Business Logic (Week 1-2)

### Service Layer

-   [ ] **Create `OrderService`**

    -   [ ] File: `app/Services/OrderService.php`
    -   [ ] Method: `createOrder(Booking, User): Order`
    -   [ ] Method: `processPayment(Order): array`
    -   [ ] Method: `handlePaymentSuccess(Order): void`
    -   [ ] Method: `handlePaymentFailed(Order): void`
    -   [ ] Method: `refundOrder(Order): void`
    -   [ ] Method: `expireUnpaidOrders(): void` (for cron)

-   [ ] **Create `XenditPaymentService`**
    -   [ ] File: `app/Services/XenditPaymentService.php`
    -   [ ] Method: `createInvoice(Order): array`
    -   [ ] Method: `getInvoiceStatus(string): array`
    -   [ ] Method: `refundInvoice(Order): array`
    -   [ ] Method: `verifyWebhookSignature(string, string): bool`
    -   [ ] Xendit SDK integration

### Notifications

-   [ ] **Create `PaymentConfirmed` Notification**

    -   [ ] File: `app/Notifications/PaymentConfirmed.php`
    -   [ ] Email template
    -   [ ] Include order details & confirmation code

-   [ ] **Create `PaymentFailed` Notification**

    -   [ ] File: `app/Notifications/PaymentFailed.php`
    -   [ ] Email template
    -   [ ] Include error details & retry link

-   [ ] **Create `PaymentExpired` Notification**

    -   [ ] File: `app/Notifications/PaymentExpired.php`
    -   [ ] Email template
    -   [ ] Include retry link

-   [ ] **Create `BookingConfirmed` Notification**
    -   [ ] File: `app/Notifications/BookingConfirmed.php`
    -   [ ] QR code generator (optional)
    -   [ ] Booking voucher

---

## Phase 3: Controllers & Routes (Week 2)

### Controllers

-   [ ] **Create `OrderController`**

    -   [ ] File: `app/Http/Controllers/OrderController.php`
    -   [ ] Method: `create(Booking)` - POST /orders
    -   [ ] Method: `checkout(Order)` - GET /orders/{id}/checkout
    -   [ ] Method: `initiatePayment(Order)` - POST /orders/{id}/pay
    -   [ ] Method: `success(Order)` - GET /orders/{id}/success
    -   [ ] Method: `failed(Order)` - GET /orders/{id}/failed

-   [ ] **Create `WebhookController`**

    -   [ ] File: `app/Http/Controllers/WebhookController.php`
    -   [ ] Method: `handleXenditWebhook(Request)`
    -   [ ] Method: `handleInvoicePaid(array)`
    -   [ ] Method: `handleInvoiceExpired(array)`

-   [ ] **Create Admin `OrderController`**
    -   [ ] File: `app/Http/Controllers/Admin/OrderController.php`
    -   [ ] Method: `index()` - GET /admin/orders
    -   [ ] Method: `show(Order)` - GET /admin/orders/{id}
    -   [ ] Method: `updateStatus(Order)` - PATCH /admin/orders/{id}/status
    -   [ ] Method: `refund(Order)` - POST /admin/orders/{id}/refund
    -   [ ] Method: `destroy(Order)` - DELETE /admin/orders/{id} (for testing)

### Request Validation

-   [ ] **Create `UpdateOrderStatusRequest`**

    -   [ ] File: `app/Http/Requests/UpdateOrderStatusRequest.php`
    -   [ ] Rules: status, reason, note
    -   [ ] Authorization check

-   [ ] **Create `RefundRequest`**
    -   [ ] File: `app/Http/Requests/RefundRequest.php`
    -   [ ] Rules: amount, reason, note
    -   [ ] Validation logic

### Routes

-   [ ] **Update `routes/web.php`**

    -   [ ] Customer order routes (auth)
    -   [ ] Admin order routes (admin middleware)
    -   [ ] Public webhook route

-   [ ] **Create `routes/webhook.php`** (optional)
    -   [ ] Separate webhook routes file
    -   [ ] Add to bootstrap providers

---

## Phase 4: Frontend - User Side (Week 2-3)

### Views - Payment Page

-   [ ] **Create `resources/views/orders/checkout.blade.php`**

    -   [ ] Order summary section
    -   [ ] Payment method selection
    -   [ ] Countdown timer (JavaScript)
    -   [ ] Pay button (trigger to Xendit)
    -   [ ] Responsive design

-   [ ] **Create `resources/views/orders/success.blade.php`**

    -   [ ] Success message
    -   [ ] Order confirmation details
    -   [ ] Confirmation code (copy button)
    -   [ ] Download receipt button
    -   [ ] Back to dashboard link

-   [ ] **Create `resources/views/orders/failed.blade.php`**
    -   [ ] Error message
    -   [ ] Order details
    -   [ ] Retry payment button
    -   [ ] Contact support info

### Components

-   [ ] **Create `OrderSummary` Component**

    -   [ ] File: `app/View/Components/OrderSummary.php`
    -   [ ] Display order details
    -   [ ] Show booking info
    -   [ ] Display payment breakdown

-   [ ] **Create `CountdownTimer` Component**
    -   [ ] File: `app/View/Components/CountdownTimer.php`
    -   [ ] Alpine.js for countdown
    -   [ ] Auto-refresh when expired
    -   [ ] Warning when < 5 minutes

### JavaScript

-   [ ] **Create `resources/js/payment.js`**
    -   [ ] Xendit integration
    -   [ ] Handle payment flow
    -   [ ] Error handling
    -   [ ] Loading states

---

## Phase 5: Frontend - Admin Side (Week 2-3)

### Admin Views

-   [ ] **Create `resources/views/admin/bookings/index.blade.php`**

    -   [ ] Update existing view
    -   [ ] Add filters: status, date range, field
    -   [ ] Add order status column
    -   [ ] Add "View Order" link

-   [ ] **Create `resources/views/admin/orders/index.blade.php`**

    -   [ ] Orders table with pagination
    -   [ ] Status indicators (✅, ⏳, ❌, 💸)
    -   [ ] Search & filters
    -   [ ] Bulk actions

-   [ ] **Create `resources/views/admin/orders/show.blade.php`**
    -   [ ] Order summary section
    -   [ ] User & booking info
    -   [ ] Payment details
    -   [ ] Transaction history
    -   [ ] Admin actions section

### Admin Components

-   [ ] **Create `OrderStatusBadge` Component**

    -   [ ] Display status with color
    -   [ ] Show icons for status

-   [ ] **Create `PaymentDetails` Component**

    -   [ ] Show payment breakdown
    -   [ ] Display transaction details
    -   [ ] Gateway reference

-   [ ] **Create `OrderActions` Component**

    -   [ ] Action buttons for admin
    -   [ ] Modals for confirmation
    -   [ ] Form handling

-   [ ] **Create `TransactionHistory` Component**
    -   [ ] Timeline of transactions
    -   [ ] Status for each transaction
    -   [ ] Timestamps

### Admin Modals

-   [ ] **Create Update Status Modal**

    -   [ ] File: `resources/views/admin/orders/modals/update-status.blade.php`
    -   [ ] Status selection
    -   [ ] Reason field
    -   [ ] Notes field

-   [ ] **Create Refund Modal**

    -   [ ] File: `resources/views/admin/orders/modals/refund.blade.php`
    -   [ ] Amount field
    -   [ ] Reason selection
    -   [ ] Customer note field

-   [ ] **Create Gateway Response Viewer**
    -   [ ] File: `resources/views/admin/orders/modals/gateway-response.blade.php`
    -   [ ] JSON viewer
    -   [ ] Copy button
    -   [ ] Download button

---

## Phase 6: Configuration & Setup (Week 1)

### Environment

-   [ ] **Setup `.env` Variables**

    -   [ ] `XENDIT_SECRET_KEY`
    -   [ ] `XENDIT_PUBLIC_KEY`
    -   [ ] `XENDIT_WEBHOOK_TOKEN`
    -   [ ] `ORDER_EXPIRY_MINUTES=30`
    -   [ ] `PAYMENT_TIMEOUT=30`

-   [ ] **Create `config/xendit.php`**
    -   [ ] Load from .env
    -   [ ] Set environment (development/production)
    -   [ ] Timeout & retry settings

### Database Seeding (Testing)

-   [ ] **Update `DatabaseSeeder.php`**

    -   [ ] Call order seeder for demo data

-   [ ] **Create `OrderSeeder.php`** (optional for testing)
    -   [ ] Create sample orders
    -   [ ] Mix of statuses: pending, paid, failed

---

## Phase 7: Testing (Week 3)

### Unit Tests

-   [ ] **Test `OrderService`**

    ```bash
    php artisan make:test Unit/Services/OrderServiceTest
    ```

    -   [ ] Test order creation
    -   [ ] Test booking lock creation
    -   [ ] Test payment success handling
    -   [ ] Test payment failure handling
    -   [ ] Test refund processing

-   [ ] **Test `XenditPaymentService`**
    ```bash
    php artisan make:test Unit/Services/XenditPaymentServiceTest
    ```
    -   [ ] Test invoice creation
    -   [ ] Test webhook signature verification
    -   [ ] Test status retrieval

### Feature Tests

-   [ ] **Test User Payment Flow**

    ```bash
    php artisan make:test Feature/BookingAndPaymentFlowTest
    ```

    -   [ ] Test booking → order creation
    -   [ ] Test payment page loading
    -   [ ] Test payment initiation

-   [ ] **Test Webhook Handling**

    ```bash
    php artisan make:test Feature/PaymentWebhookTest
    ```

    -   [ ] Test webhook signature verification
    -   [ ] Test invoice.paid event
    -   [ ] Test invoice.expired event
    -   [ ] Test booking confirmation
    -   [ ] Test notifications sent

-   [ ] **Test Admin Features**
    ```bash
    php artisan make:test Feature/Admin/OrderManagementTest
    ```
    -   [ ] Test orders listing
    -   [ ] Test order detail page
    -   [ ] Test manual status update
    -   [ ] Test refund processing
    -   [ ] Test activity logging

### Manual Testing

-   [ ] **Xendit Sandbox Testing**

    -   [ ] Setup Xendit sandbox account
    -   [ ] Test successful payment flow
    -   [ ] Test failed payment flow
    -   [ ] Test expired payment flow
    -   [ ] Test webhook delivery
    -   [ ] Test different payment methods

-   [ ] **Edge Cases**
    -   [ ] Double booking prevention
    -   [ ] Concurrent payment attempts
    -   [ ] Webhook replay attacks
    -   [ ] Order timeout handling
    -   [ ] Database transaction rollback

---

## Phase 8: Deployment & Monitoring (Week 3+)

### Pre-Production

-   [ ] **Environment Setup**

    -   [ ] Get Xendit production keys
    -   [ ] Update `.env` for production
    -   [ ] Setup webhook URL in Xendit dashboard
    -   [ ] Update webhook token

-   [ ] **Database**

    -   [ ] Run migrations on production
    -   [ ] Add indexes for performance
    -   [ ] Setup backup strategy

-   [ ] **Security**
    -   [ ] Enable HTTPS everywhere
    -   [ ] Rate limiting for webhook
    -   [ ] IP whitelisting (if available)
    -   [ ] Secrets management

### Monitoring

-   [ ] **Setup Logging**

    -   [ ] Payment transaction logs
    -   [ ] Webhook delivery logs
    -   [ ] Error logs
    -   [ ] Admin action logs

-   [ ] **Setup Alerts**

    -   [ ] Payment failure rate > 5%
    -   [ ] Webhook delivery failures
    -   [ ] Timeout errors
    -   [ ] Admin suspicious actions

-   [ ] **Setup Monitoring Dashboard**
    -   [ ] Payment metrics
    -   [ ] Revenue tracking
    -   [ ] Failed transactions
    -   [ ] Customer support metrics

---

## Xendit Configuration Steps

### 1. Get Xendit Account

-   [ ] Go to xendit.co
-   [ ] Create business account
-   [ ] Verify email & setup 2FA
-   [ ] Complete KYC verification

### 2. Get API Keys

-   [ ] Dashboard → Settings → API
-   [ ] Copy Secret Key (xnd*development*...)
-   [ ] Copy Public Key (xnd*public_development*...)
-   [ ] Generate Webhook Token

### 3. Configure Webhook

-   [ ] Dashboard → Settings → Webhooks
-   [ ] Add webhook URL: `https://yourdomain.com/webhooks/xendit`
-   [ ] Select events: `invoice.paid`, `invoice.expired`
-   [ ] Add webhook token to `.env`
-   [ ] Test webhook delivery

### 4. Setup Xendit Dashboard

-   [ ] Customize invoice branding
-   [ ] Setup webhook retry policy
-   [ ] Configure payment methods
-   [ ] Test in sandbox mode

---

## Quick Start Commands

```bash
# 1. Install Xendit package
composer require xendit/xendit-php

# 2. Generate migrations
php artisan make:migration create_orders_table
php artisan make:migration create_payment_methods_table
php artisan make:migration create_payment_transactions_table
php artisan make:migration create_booking_locks_table

# 3. Generate models
php artisan make:model Order
php artisan make:model PaymentTransaction
php artisan make:model BookingLock

# 4. Generate controllers
php artisan make:controller OrderController
php artisan make:controller Admin/OrderController
php artisan make:controller WebhookController

# 5. Generate requests
php artisan make:request UpdateOrderStatusRequest
php artisan make:request RefundRequest

# 6. Generate notifications
php artisan make:notification PaymentConfirmed
php artisan make:notification PaymentFailed
php artisan make:notification PaymentExpired

# 7. Run migrations
php artisan migrate

# 8. Create views
# (manually or using artisan scaffolding)
```

---

## Next Steps

1. ✅ Start with Phase 1 (Database & Models)
2. ✅ Proceed to Phase 2 (Services)
3. ✅ Then Phase 3 (Controllers)
4. ✅ Frontend implementation (Phase 4 & 5)
5. ✅ Testing (Phase 7)
6. ✅ Deploy to production (Phase 8)

---

## Support Resources

-   **Xendit Documentation**: https://docs.xendit.co/
-   **Laravel Documentation**: https://laravel.com/docs
-   **Payment Best Practices**: https://cheatsheetseries.owasp.org/
-   **Invoice Templates**: Bootstrap Invoice templates

---

**Ready to start? Let's begin with Phase 1! 🚀**
