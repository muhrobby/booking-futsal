<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
    ) {}

    /**
     * Show checkout page
     */
    public function create(Booking $booking)
    {
        // Pastikan booking milik user yang login
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking');
        }

        // Pastikan booking belum dibayar
        if ($booking->status !== 'pending') {
            return redirect()->route('dashboard')
                ->with('error', 'Booking ini sudah diproses');
        }

        // Check if order already exists for this booking
        $order = $booking->orders()->first();
        if (!$order) {
            // Order should have been created in BookingController, but create if missing
            try {
                $order = app(\App\Services\OrderService::class)->createOrder($booking, Auth::user());
            } catch (\Exception $e) {
                return redirect()->route('bookings.show', $booking)
                    ->with('error', 'Gagal membuat order: ' . $e->getMessage());
            }
        }

        return view('orders.create', compact('booking', 'order'));
    }

    /**
     * Initiate payment for existing order
     */
    public function store(Request $request, Booking $booking)
    {
        try {
            // Pastikan booking milik user yang login
            if ($booking->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access to booking');
            }

            // Get the auto-created order
            $order = $booking->orders()->first();
            if (!$order) {
                throw new \Exception('Order not found. Please try again.');
            }

            // Process payment (generate Xendit invoice)
            $paymentResult = $this->orderService->processPayment($order);

            if (!$paymentResult['success']) {
                throw new \Exception($paymentResult['error'] ?? 'Failed to create payment invoice');
            }

            $paymentUrl = $paymentResult['invoice_url'];

            Log::info('Order created and payment initiated', [
                'order_id' => $order->id,
                'booking_id' => $booking->id,
                'payment_url' => $paymentUrl,
            ]);

            // Redirect to Xendit payment page
            return redirect()->away($paymentUrl);

        } catch (\Exception $e) {
            Log::error('Failed to create order', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Gagal membuat order: ' . $e->getMessage());
        }
    }

    /**
     * Payment success page
     */
    public function success(Request $request)
    {
        $orderId = $request->query('order');
        
        if (!$orderId) {
            return redirect()->route('dashboard');
        }

        $order = Order::with(['booking.field', 'booking.timeSlot'])
            ->where('id', $orderId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return redirect()->route('dashboard')
                ->with('error', 'Order tidak ditemukan');
        }

        // Auto-check payment status jika masih pending (untuk handle kasus webhook belum diterima)
        if ($order->status === 'pending' || $order->status === 'processing') {
            try {
                $xenditService = app(\App\Services\XenditPaymentService::class);
                
                // Get latest transaction
                $lastTransaction = $order->paymentTransactions()->latest()->first();
                
                if ($lastTransaction && $lastTransaction->gateway_invoice_id) {
                    // Check status dari Xendit API
                    $invoiceData = $xenditService->checkInvoiceStatus($lastTransaction->gateway_invoice_id);
                    
                    if ($invoiceData && in_array(strtoupper($invoiceData['status']), ['PAID', 'SETTLED'])) {
                        // Update order status
                        $this->orderService->handlePaymentSuccess($order, [
                            'transaction_id' => $invoiceData['id'],
                            'payment_method' => $invoiceData['payment_method'] ?? 'xendit',
                            'payment_channel' => $invoiceData['payment_channel'] ?? null,
                            'amount' => $invoiceData['amount'] ?? $order->total,
                            'paid_at' => $invoiceData['paid_at'] ?? now(),
                            'raw_response' => $invoiceData,
                        ]);
                        
                        // Reload order untuk dapat status terbaru
                        $order->refresh();
                        $order->load(['booking.field', 'booking.timeSlot']);
                        
                        Log::info('Payment status auto-updated from success page', [
                            'order_id' => $order->id,
                            'new_status' => $order->status,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to auto-check payment status', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('orders.success', compact('order'));
    }

    /**
     * Payment failed page
     */
    public function failed(Request $request)
    {
        $orderId = $request->query('order_id');
        
        if (!$orderId) {
            return redirect()->route('dashboard');
        }

        $order = Order::with(['booking.field', 'booking.timeSlot'])
            ->where('id', $orderId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return redirect()->route('dashboard')
                ->with('error', 'Order tidak ditemukan');
        }

        return view('orders.failed', compact('order'));
    }

    /**
     * Show order details
     */
    public function show(Order $order)
    {
        // Pastikan order milik user yang login
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        $order->load(['booking.field', 'booking.timeSlot', 'paymentTransactions']);

        return view('orders.show', compact('order'));
    }

    /**
     * List user's orders
     */
    public function index()
    {
        $orders = Order::with(['booking.field', 'booking.timeSlot'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }
}
