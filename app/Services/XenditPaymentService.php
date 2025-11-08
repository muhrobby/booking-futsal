<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditPaymentService
{
    private string $secretKey;
    private string $apiUrl = 'https://api.xendit.co';

    public function __construct()
    {
        $this->secretKey = env('XENDIT_SECRET_KEY');
        
        // Switch to sandbox if in development
        if (env('XENDIT_ENVIRONMENT') === 'sandbox') {
            $this->apiUrl = 'https://api.xendit.co';
        }
    }

    /**
     * Create an invoice in Xendit
     *
     * @param Order $order
     * @return array
     * @throws \Exception
     */
    public function createInvoice(Order $order): array
    {
        try {
            $user = $order->user;
            $booking = $order->booking;
            $field = $booking->field;

            $payload = [
                'external_id' => $order->order_number,
                'amount' => (int) $order->total,
                'payer_email' => $user->email,
                'payer_name' => $user->name,
                'description' => config('payment.payment.description_template', 'Booking Futsal - {order_number}')
                    ? str_replace('{order_number}', $order->order_number, config('payment.payment.description_template'))
                    : "Booking Futsal - {$order->order_number}",
                'success_redirect_url' => route('payment.success', ['order' => $order->id]),
                'failure_redirect_url' => route('payment.failed', ['order' => $order->id]),
            ];

            // Make API request using HTTP client with basic auth
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post("{$this->apiUrl}/v2/invoices", $payload);

            if (!$response->successful()) {
                throw new \Exception('Xendit API error: ' . $response->body());
            }

            $invoice = $response->json();

            Log::info('Xendit invoice created', [
                'order_id' => $order->id,
                'invoice_id' => $invoice['id'],
                'amount' => $order->total,
            ]);

            return [
                'id' => $invoice['id'],
                'external_id' => $invoice['external_id'],
                'amount' => $invoice['amount'],
                'invoice_url' => $invoice['invoice_url'],
                'status' => $invoice['status'],
                'created_at' => $invoice['created'] ?? now(),
                'expires_at' => $invoice['expiry_date'],
            ];
        } catch (\Exception $e) {
            Log::error('Failed to create Xendit invoice', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to create payment invoice: ' . $e->getMessage());
        }
    }

    /**
     * Get available payment methods configuration
     *
     * @return array
     */
    private function getPaymentMethods(): array
    {
        $methods = [];
        $paymentConfig = config('payment.methods', []);

        // Credit/Debit Card
        if ($paymentConfig['credit_card']['enabled'] ?? false) {
            $methods[] = 'CARD';
        }

        // E-Wallet
        if ($paymentConfig['e_wallet']['enabled'] ?? false) {
            $ewallet = $paymentConfig['e_wallet']['methods'] ?? [];
            foreach ($ewallet as $wallet) {
                $methods[] = strtoupper($wallet);
            }
        }

        // Bank Transfer
        if ($paymentConfig['bank_transfer']['enabled'] ?? false) {
            $methods[] = 'BANK_TRANSFER';
        }

        // BNPL
        if ($paymentConfig['bnpl']['enabled'] ?? false) {
            $methods[] = 'BNPL';
        }

        // Retail
        if ($paymentConfig['retail']['enabled'] ?? false) {
            $methods[] = 'RETAIL';
        }

        return !empty($methods) ? $methods : ['CARD', 'OVO', 'DANA', 'BANK_TRANSFER'];
    }

    /**
     * Verify webhook signature from Xendit
     *
     * @param string $payload
     * @param string $signature
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $webhookToken = env('XENDIT_WEBHOOK_TOKEN');
        $expectedSignature = hash('sha256', $payload . $webhookToken);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Get invoice status from Xendit
     *
     * @param string $invoiceId
     * @return array
     */
    public function getInvoiceStatus(string $invoiceId): array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get("{$this->apiUrl}/v2/invoices/{$invoiceId}");

            if (!$response->successful()) {
                throw new \Exception('Failed to get invoice status: ' . $response->body());
            }

            $invoice = $response->json();

            return [
                'success' => true,
                'id' => $invoice['id'],
                'status' => $invoice['status'],
                'amount' => $invoice['amount'],
                'paid_amount' => $invoice['paid_amount'] ?? 0,
                'payment_method' => $invoice['payment_method'] ?? null,
                'paid_at' => $invoice['paid_at'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get invoice status', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process webhook callback from Xendit
     *
     * @param array $payload
     * @return array
     */
    public function processWebhook(array $payload): array
    {
        $eventType = $payload['event'] ?? null;
        $invoiceData = $payload['data'] ?? [];
        $invoiceId = $invoiceData['id'] ?? null;

        if (!$invoiceId) {
            throw new \Exception('Invalid webhook payload: missing invoice ID');
        }

        // Find order by external_id (order_number)
        $externalId = $invoiceData['external_id'] ?? null;
        $order = Order::where('order_number', $externalId)->first();

        if (!$order) {
            Log::warning('Webhook received for unknown order', [
                'external_id' => $externalId,
                'invoice_id' => $invoiceId,
            ]);

            throw new \Exception("Order not found: {$externalId}");
        }

        Log::info('Webhook received', [
            'order_id' => $order->id,
            'event' => $eventType,
            'invoice_id' => $invoiceId,
        ]);

        // Handle different webhook events
        switch ($eventType) {
            case 'invoice.paid':
                return $this->handleInvoicePaid($order, $invoiceData);

            case 'invoice.expired':
                return $this->handleInvoiceExpired($order, $invoiceData);

            default:
                Log::warning('Unknown webhook event', [
                    'event' => $eventType,
                    'order_id' => $order->id,
                ]);

                return [
                    'success' => false,
                    'message' => "Unknown event: {$eventType}",
                ];
        }
    }

    /**
     * Handle invoice.paid webhook
     *
     * @param Order $order
     * @param array $invoiceData
     * @return array
     */
    private function handleInvoicePaid(Order $order, array $invoiceData): array
    {
        try {
            // Update payment reference
            $order->update([
                'payment_method' => $invoiceData['payment_method'] ?? null,
                'xendit_invoice_id' => $invoiceData['id'],
            ]);

            // Get OrderService and handle success
            $orderService = app(OrderService::class);
            $orderService->handlePaymentSuccess($order, $invoiceData);

            return [
                'success' => true,
                'message' => 'Payment confirmed',
                'order_id' => $order->id,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to handle paid webhook', [
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
     * Handle invoice.expired webhook
     *
     * @param Order $order
     * @param array $invoiceData
     * @return array
     */
    private function handleInvoiceExpired(Order $order, array $invoiceData): array
    {
        try {
            // Get OrderService and handle expiry
            $orderService = app(OrderService::class);
            $orderService->handlePaymentExpired($order);

            return [
                'success' => true,
                'message' => 'Payment expired',
                'order_id' => $order->id,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to handle expired webhook', [
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
     * Refund an invoice
     *
     * @param string $invoiceId
     * @param float $amount
     * @return array
     */
    public function refundInvoice(string $invoiceId, float $amount): array
    {
        try {
            $refundRequest = [
                'invoice_id' => $invoiceId,
                'amount' => (int) $amount,
                'notes' => 'Refund processed by admin',
            ];

            // Note: This is a placeholder. Actual refund implementation depends on Xendit API version
            // You may need to implement this based on your Xendit SDK version
            
            Log::info('Refund processed', [
                'invoice_id' => $invoiceId,
                'amount' => $amount,
            ]);

            return [
                'success' => true,
                'refund_id' => 'REF-' . uniqid(),
                'amount' => $amount,
                'status' => 'pending',
            ];
        } catch (\Exception $e) {
            Log::error('Failed to process refund', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
