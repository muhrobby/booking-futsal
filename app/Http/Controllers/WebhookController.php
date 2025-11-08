<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private OrderService $orderService,
    ) {}

    /**
     * Handle Xendit webhook callbacks
     */
    public function handleXenditWebhook(Request $request)
    {
        try {
            // Get raw webhook payload
            $payload = $request->all();
            
            Log::info('Xendit webhook received', [
                'payload' => $payload,
                'headers' => $request->headers->all(),
            ]);

            // Verify webhook signature
            $webhookToken = $request->header('x-callback-token');
            
            if ($webhookToken !== config('payment.xendit.webhook_token')) {
                Log::warning('Invalid webhook token', [
                    'received_token' => $webhookToken,
                ]);
                
                return response()->json([
                    'error' => 'Invalid webhook token',
                ], 401);
            }

            // Get event type
            $status = $payload['status'] ?? null;
            $externalId = $payload['external_id'] ?? null;

            if (!$externalId) {
                Log::error('Webhook missing external_id', ['payload' => $payload]);
                return response()->json(['error' => 'Missing external_id'], 400);
            }

            // Extract order ID from external_id (format: ORDER-{order_id})
            $orderId = str_replace('ORDER-', '', $externalId);

            // Handle different payment statuses
            switch (strtoupper($status)) {
                case 'PAID':
                case 'SETTLED':
                    $this->handleInvoicePaid($orderId, $payload);
                    break;

                case 'EXPIRED':
                    $this->handleInvoiceExpired($orderId, $payload);
                    break;

                case 'FAILED':
                    $this->handleInvoiceFailed($orderId, $payload);
                    break;

                default:
                    Log::info('Unhandled webhook status', [
                        'status' => $status,
                        'order_id' => $orderId,
                    ]);
            }

            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Webhook processing failed',
            ], 500);
        }
    }

    /**
     * Handle paid invoice
     */
    private function handleInvoicePaid($orderId, array $payload)
    {
        try {
            $order = \App\Models\Order::findOrFail($orderId);
            
            Log::info('Processing paid invoice', [
                'order_id' => $orderId,
                'invoice_id' => $payload['id'] ?? null,
            ]);

            $this->orderService->handlePaymentSuccess($order, [
                'transaction_id' => $payload['id'] ?? null,
                'payment_method' => $payload['payment_method'] ?? 'xendit',
                'payment_channel' => $payload['payment_channel'] ?? null,
                'amount' => $payload['amount'] ?? $order->total,
                'paid_at' => $payload['paid_at'] ?? now(),
                'raw_response' => $payload,
            ]);

            Log::info('Invoice paid processed successfully', [
                'order_id' => $orderId,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process paid invoice', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle expired invoice
     */
    private function handleInvoiceExpired($orderId, array $payload)
    {
        try {
            $order = \App\Models\Order::findOrFail($orderId);
            
            Log::info('Processing expired invoice', [
                'order_id' => $orderId,
                'invoice_id' => $payload['id'] ?? null,
            ]);

            $this->orderService->handlePaymentExpired($order);

            Log::info('Invoice expired processed successfully', [
                'order_id' => $orderId,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process expired invoice', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle failed invoice
     */
    private function handleInvoiceFailed($orderId, array $payload)
    {
        try {
            $order = \App\Models\Order::findOrFail($orderId);
            
            Log::info('Processing failed invoice', [
                'order_id' => $orderId,
                'invoice_id' => $payload['id'] ?? null,
            ]);

            $errorMessage = $payload['failure_reason'] ?? 'Payment failed';
            
            $this->orderService->handlePaymentFailed($order, $errorMessage);

            Log::info('Invoice failed processed successfully', [
                'order_id' => $orderId,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process failed invoice', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
