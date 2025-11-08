<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     */
    public function __invoke(): View
    {
        $user = auth('web')->user();
        
        // Get user's booking statistics
        $totalBookings = $user->bookings()->count();
        $upcomingBookings = $user->bookings()
            ->whereHas('timeSlot', function ($query) {
                $query->where('start_time', '>', now());
            })
            ->count();
        
        $completedBookings = $user->bookings()
            ->whereHas('timeSlot', function ($query) {
                $query->where('end_time', '<', now());
            })
            ->count();
        
        $cancelledBookings = $user->bookings()
            ->where('status', 'cancelled')
            ->count();
        
        // Get recent bookings (last 5)
        $recentBookings = $user->bookings()
            ->with(['field', 'timeSlot'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get next booking
        $nextBooking = $user->bookings()
            ->with(['field', 'timeSlot'])
            ->whereHas('timeSlot', function ($query) {
                $query->where('start_time', '>', now());
            })
            ->orderBy('created_at', 'asc')
            ->first();
        
        // Get total spending (database-agnostic using raw calculations)
        $totalSpending = 0;
        foreach ($user->bookings()->with(['field', 'timeSlot'])->where('status', '!=', 'cancelled')->get() as $booking) {
            if ($booking->timeSlot) {
                $hours = $booking->timeSlot->start_time->diffInHours($booking->timeSlot->end_time);
                $totalSpending += $hours * $booking->field->price_per_hour;
            }
        }

        // Get recent orders (last 5)
        $recentOrders = $user->orders()
            ->with(['booking.field', 'booking.timeSlot'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get pending orders count
        $pendingOrders = $user->orders()
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        return view('dashboard', [
            'user' => $user,
            'totalBookings' => $totalBookings,
            'upcomingBookings' => $upcomingBookings,
            'completedBookings' => $completedBookings,
            'cancelledBookings' => $cancelledBookings,
            'recentBookings' => $recentBookings,
            'nextBooking' => $nextBooking,
            'totalSpending' => $totalSpending,
            'recentOrders' => $recentOrders,
            'pendingOrders' => $pendingOrders,
        ]);
    }
}
