<?php

namespace App\Http\Controllers;

use App\Models\Booking;
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
        
        // Get total spending
        $totalSpending = $user->bookings()
            ->join('fields', 'bookings.field_id', '=', 'fields.id')
            ->join('time_slots', 'bookings.time_slot_id', '=', 'time_slots.id')
            ->selectRaw('SUM(CAST((JULIANDAY(time_slots.end_time) - JULIANDAY(time_slots.start_time)) * 24 AS INTEGER) * fields.price_per_hour) as total')
            ->where('bookings.status', '!=', 'cancelled')
            ->value('total') ?? 0;

        return view('dashboard', [
            'user' => $user,
            'totalBookings' => $totalBookings,
            'upcomingBookings' => $upcomingBookings,
            'completedBookings' => $completedBookings,
            'cancelledBookings' => $cancelledBookings,
            'recentBookings' => $recentBookings,
            'nextBooking' => $nextBooking,
            'totalSpending' => $totalSpending,
        ]);
    }
}
