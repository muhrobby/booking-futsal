<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
    ) {
        // Middleware already handled in routes
    }

    /**
     * Display list of orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'booking.field', 'booking.timeSlot']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by order number or user name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate statistics
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'paid' => Order::where('status', 'paid')->count(),
            'failed' => Order::where('status', 'failed')->count(),
            'expired' => Order::where('status', 'expired')->count(),
            'refunded' => Order::where('status', 'refunded')->count(),
            'total_revenue' => Order::where('status', 'paid')->sum('total'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Show order details
     */
    public function show(Order $order)
    {
        $order->load([
            'user',
            'booking.field',
            'booking.timeSlot',
            'booking.bookingLocks',
            'paymentTransactions',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status manually
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,failed,expired,cancelled,refunded',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $oldStatus = $order->status;
            $order->status = $request->status;
            $order->admin_notes = $request->notes;
            $order->save();

            Log::info('Order status updated manually', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'updated_by' => auth()->id(),
            ]);

            // If status changed to paid, update booking
            if ($request->status === 'paid' && $oldStatus !== 'paid') {
                $order->booking->update(['status' => 'confirmed']);
            }

            // If status changed to cancelled/refunded, release the booking
            if (in_array($request->status, ['cancelled', 'refunded']) && $oldStatus === 'paid') {
                $order->booking->update(['status' => 'cancelled']);
            }

            return redirect()->back()
                ->with('success', 'Status order berhasil diupdate');

        } catch (\Exception $e) {
            Log::error('Failed to update order status', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Gagal update status: ' . $e->getMessage());
        }
    }

    /**
     * Process refund
     */
    public function refund(Request $request, Order $order)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'amount' => 'nullable|numeric|min:0|max:' . $order->total,
        ]);

        try {
            $refundAmount = $request->amount ?? $order->total;

            $result = $this->orderService->refundOrder($order, $refundAmount, $request->reason);

            if ($result) {
                return redirect()->back()
                    ->with('success', 'Refund berhasil diproses');
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal memproses refund');
            }

        } catch (\Exception $e) {
            Log::error('Failed to process refund', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Gagal memproses refund: ' . $e->getMessage());
        }
    }

    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $query = Order::with(['user', 'booking.field', 'booking.timeSlot']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $filename = 'orders_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Order Number',
                'User Name',
                'User Email',
                'Field',
                'Date',
                'Time Slot',
                'Status',
                'Subtotal',
                'Tax',
                'Discount',
                'Total',
                'Created At',
                'Paid At',
            ]);

            // Data rows
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->user->name,
                    $order->user->email,
                    $order->booking->field->name,
                    $order->booking->booking_date,
                    $order->booking->timeSlot->start_time . ' - ' . $order->booking->timeSlot->end_time,
                    $order->status,
                    $order->subtotal,
                    $order->tax,
                    $order->discount,
                    $order->total,
                    $order->created_at,
                    $order->paid_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
