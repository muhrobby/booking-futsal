<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingLock;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        private XenditPaymentService $xenditService,
    ) {}

    /**
     * Create an order from a booking
     *
     * @param Booking $booking
     * @param User $user
     * @return Order
     * @throws \Exception
     */
    public function createOrder(Booking $booking, User $user): Order
    {
        return DB::transaction(function () use ($booking, $user) {
            // Create the order
            $order = Order::create([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'order_number' => 'ORD-' . date('Ymd') . '-' . uniqid(),
                'status' => 'pending',
                'subtotal' => $booking->total_price,
                'tax' => 0,
                'discount' => 0,
                'total' => $booking->total_price,
                'currency' => config('payment.payment.currency', 'IDR'),
            ]);

            // Update booking status to pending and set expires_at
            $paymentTimeout = config('payment.timeout_minutes', 30);
            $booking->update([
                'status' => 'pending',
                'expires_at' => now()->addMinutes($paymentTimeout),
            ]);

            // Create booking lock (30 minutes)
            $booking->lockForPayment($order);

            Log::info('Order created', [
                'order_id' => $order->id,
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'amount' => $order->total,
                'expires_at' => $booking->expires_at,
            ]);

            return $order;
        });
    }

    /**
     * Process payment for an order
     *
     * @param Order $order
     * @return array
     * @throws \Exception
     */
    public function processPayment(Order $order): array
    {
        try {
            if (!$order->isPending()) {
                throw new \Exception("Order status must be pending, currently {$order->status}");
            }

            // Mark as processing
            $order->update(['status' => 'processing']);

            // Create Xendit invoice
            $invoiceData = $this->xenditService->createInvoice($order);

            // Store invoice ID
            $order->update([
                'xendit_invoice_id' => $invoiceData['id'],
                'payment_reference' => $invoiceData['invoice_url'] ?? $invoiceData['id'],
            ]);

            // Log transaction
            PaymentTransaction::create([
                'order_id' => $order->id,
                'gateway' => 'xendit',
                'gateway_invoice_id' => $invoiceData['id'],
                'status' => 'pending',
                'amount' => $order->total,
                'currency' => $order->currency,
                'request_payload' => json_encode($invoiceData),
            ]);

            Log::info('Payment processing initiated', [
                'order_id' => $order->id,
                'invoice_id' => $invoiceData['id'],
            ]);

            return [
                'success' => true,
                'invoice_url' => $invoiceData['invoice_url'] ?? null,
                'invoice_id' => $invoiceData['id'],
                'redirect_url' => $invoiceData['invoice_url'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            $this->handlePaymentFailed($order, $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle successful payment
     *
     * @param Order $order
     * @param array $webhookData
     * @return void
     */
    public function handlePaymentSuccess(Order $order, array $webhookData = []): void
    {
        DB::transaction(function () use ($order, $webhookData) {
            if ($order->isPaid()) {
                Log::warning('Payment already marked as paid', ['order_id' => $order->id]);
                return;
            }

            // Mark order as paid
            $order->markAsPaid();

            // Update the booking to confirmed and clear expires_at
            $order->booking()->update([
                'status' => 'confirmed',
                'expires_at' => null,
            ]);

            // Release the booking lock
            $order->releaseLock('payment_confirmed');

            // Log transaction
            $lastTransaction = $order->transactions()->latest()->first();
            if ($lastTransaction) {
                $lastTransaction->update([
                    'status' => 'completed',
                    'webhook_received_at' => now(),
                    'webhook_payload' => json_encode($webhookData),
                ]);
            }

            Log::info('Payment successful', [
                'order_id' => $order->id,
                'booking_id' => $order->booking_id,
            ]);

            // Send notification
            event(new \App\Events\PaymentSuccessfulEvent($order));
        });
    }

    /**
     * Handle failed payment
     *
     * @param Order $order
     * @param string $errorMessage
     * @param array $webhookData
     * @return void
     */
    public function handlePaymentFailed(
        Order $order,
        string $errorMessage = '',
        array $webhookData = []
    ): void {
        DB::transaction(function () use ($order, $errorMessage, $webhookData) {
            if ($order->isFailed()) {
                return;
            }

            // Mark order as failed
            $order->markAsFailed($errorMessage);

            // Release the lock and revert booking to available
            $order->booking()->update([
                'status' => 'available',
                'expires_at' => null,
            ]);
            $order->releaseLock('payment_failed');

            // Log transaction
            $lastTransaction = $order->transactions()->latest()->first();
            if ($lastTransaction) {
                $lastTransaction->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                    'webhook_received_at' => now(),
                    'webhook_payload' => json_encode($webhookData),
                ]);
            }

            Log::error('Payment failed', [
                'order_id' => $order->id,
                'error' => $errorMessage,
            ]);

            // Send notification
            event(new \App\Events\PaymentFailedEvent($order, $errorMessage));
        });
    }

    /**
     * Handle expired payment
     *
     * @param Order $order
     * @return void
     */
    public function handlePaymentExpired(Order $order): void
    {
        DB::transaction(function () use ($order) {
            if ($order->isExpired()) {
                return;
            }

            // Mark order as expired
            $order->markAsExpired();

            // Release the lock
            $order->releaseLock('payment_expired');

            // Cancel the booking
            $order->booking()->update(['status' => 'canceled']);

            Log::warning('Payment expired', [
                'order_id' => $order->id,
                'booking_id' => $order->booking_id,
            ]);

            // Send notification
            event(new \App\Events\PaymentExpiredEvent($order));
        });
    }

    /**
     * Expire unpaid orders
     * Run this via scheduler or command
     *
     * @return int Number of expired orders
     */
    public function expireUnpaidOrders(): int
    {
        $expiredOrderIds = Order::pending()
            ->where('expired_at', '<=', now())
            ->pluck('id');

        foreach ($expiredOrderIds as $orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $this->handlePaymentExpired($order);
            }
        }

        return count($expiredOrderIds);
    }

    /**
     * Refund an order
     *
     * @param Order $order
     * @param float|null $amount
     * @param string $reason
     * @return array
     */
    public function refundOrder(Order $order, ?float $amount = null, string $reason = ''): array
    {
        try {
            $refundAmount = $amount ?? $order->total;

            if (!$order->canBeRefunded()) {
                throw new \Exception("Order cannot be refunded. Current status: {$order->status}");
            }

            // Process refund via Xendit
            $refundResult = $this->xenditService->refundInvoice(
                $order->xendit_invoice_id,
                $refundAmount
            );

            if (!$refundResult['success']) {
                throw new \Exception($refundResult['error'] ?? 'Refund failed');
            }

            // Mark order as refunded
            $order->update([
                'status' => 'refunded',
                'admin_notes' => $reason,
            ]);

            // Log transaction
            PaymentTransaction::create([
                'order_id' => $order->id,
                'gateway' => 'xendit',
                'status' => 'refunded',
                'amount' => $refundAmount,
                'refunded_amount' => $refundAmount,
                'refunded_at' => now(),
                'request_payload' => json_encode(['type' => 'refund', 'reason' => $reason]),
                'response_payload' => json_encode($refundResult),
            ]);

            Log::info('Order refunded', [
                'order_id' => $order->id,
                'amount' => $refundAmount,
                'reason' => $reason,
            ]);

            return [
                'success' => true,
                'refund_id' => $refundResult['refund_id'] ?? null,
                'amount' => $refundAmount,
            ];
        } catch (\Exception $e) {
            Log::error('Refund failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Update order status (for admin manual override)
     *
     * @param Order $order
     * @param string $newStatus
     * @param string $reason
     * @return bool
     */
    public function updateOrderStatus(Order $order, string $newStatus, string $reason = ''): bool
    {
        $validStatuses = ['pending', 'processing', 'paid', 'failed', 'expired', 'refunded', 'canceled'];

        if (!in_array($newStatus, $validStatuses)) {
            throw new \Exception("Invalid status: {$newStatus}");
        }

        $oldStatus = $order->status;

        $order->update([
            'status' => $newStatus,
            'admin_notes' => ($order->admin_notes ? $order->admin_notes . "\n" : '') . 
                            "Status changed by admin from {$oldStatus} to {$newStatus}. Reason: {$reason}",
        ]);

        // Handle related logic based on new status
        if ($newStatus === 'paid' && $oldStatus !== 'paid') {
            $order->booking()->update(['status' => 'confirmed']);
            $order->releaseLock('manually_confirmed');
        } elseif ($newStatus === 'canceled' && $oldStatus !== 'canceled') {
            $order->booking()->update(['status' => 'canceled']);
            $order->releaseLock('manually_canceled');
        }

        Log::warning('Order status manually updated', [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'reason' => $reason,
        ]);

        return true;
    }

    /**
     * Get order details with all relationships
     *
     * @param Order $order
     * @return array
     */
    public function getOrderDetails(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'user' => [
                'id' => $order->user->id,
                'name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone,
            ],
            'booking' => [
                'id' => $order->booking->id,
                'booking_date' => $order->booking->booking_date,
                'start_time' => $order->booking->start_time,
                'end_time' => $order->booking->end_time,
                'field' => $order->booking->field->name ?? 'N/A',
            ],
            'amount' => [
                'subtotal' => $order->subtotal,
                'tax' => $order->tax,
                'discount' => $order->discount,
                'total' => $order->total,
                'currency' => $order->currency,
            ],
            'payment' => [
                'method' => $order->payment_method,
                'reference' => $order->payment_reference,
                'xendit_invoice_id' => $order->xendit_invoice_id,
                'paid_at' => $order->paid_at,
                'expired_at' => $order->expired_at,
            ],
            'transactions' => $order->transactions->map(fn($t) => [
                'id' => $t->id,
                'status' => $t->status,
                'amount' => $t->amount,
                'gateway' => $t->gateway,
                'created_at' => $t->created_at,
            ]),
            'admin_notes' => $order->admin_notes,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
        ];
    }
}
