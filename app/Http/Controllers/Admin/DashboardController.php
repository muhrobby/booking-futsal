<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Field;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function __invoke(Request $request): View
    {
        // Get date range from request or default to last 30 days
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Parse dates
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Get total statistics (filtered by date range)
        $totalUsers = User::whereBetween('created_at', [$start, $end])->count();
        $totalFields = Field::count();
        $totalBookings = Booking::whereBetween('booking_date', [$start, $end])->count();
        $totalRevenue = Booking::where('status', 'confirmed')
            ->whereBetween('booking_date', [$start, $end])
            ->join('fields', 'bookings.field_id', '=', 'fields.id')
            ->join('time_slots', 'bookings.time_slot_id', '=', 'time_slots.id')
            ->selectRaw('SUM(CAST((JULIANDAY(time_slots.end_time) - JULIANDAY(time_slots.start_time)) * 24 AS INTEGER) * fields.price_per_hour) as total')
            ->value('total') ?? 0;

        // Get recent bookings
        $recentBookings = Booking::with(['user', 'field', 'timeSlot'])
            ->whereBetween('booking_date', [$start, $end])
            ->latest('created_at')
            ->take(10)
            ->get();

        // Get booking status breakdown
        $bookingsByStatus = Booking::select('status')
            ->whereBetween('booking_date', [$start, $end])
            ->selectRaw('count(*) as count')
            ->groupBy('status')
            ->get()
            ->keyBy('status')
            ->map(fn ($item) => $item->count);

        // Get top fields by bookings
        $topFields = Field::withCount(['bookings' => function($query) use ($start, $end) {
                $query->whereBetween('booking_date', [$start, $end]);
            }])
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();

        // Prepare chart data for the date range
        $chartData = $this->getChartData($start, $end);

        // Get occupancy rate
        $totalTimeSlots = DB::table('time_slots')->count();
        $bookedSlots = Booking::where('status', 'confirmed')
            ->whereBetween('booking_date', [$start, $end])
            ->count();
        $occupancyRate = $totalTimeSlots > 0 ? round(($bookedSlots / $totalTimeSlots) * 100, 2) : 0;

        // Get today's bookings
        $todayBookings = Booking::whereDate('booking_date', now()->toDateString())->count();

        // Get pending confirmations
        $pendingBookings = Booking::where('status', 'pending')->count();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalFields' => $totalFields,
            'totalBookings' => $totalBookings,
            'totalRevenue' => $totalRevenue,
            'recentBookings' => $recentBookings,
            'bookingsByStatus' => $bookingsByStatus,
            'topFields' => $topFields,
            'occupancyRate' => $occupancyRate,
            'todayBookings' => $todayBookings,
            'pendingBookings' => $pendingBookings,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'chartData' => $chartData,
        ]);
    }

    /**
     * Get chart data for revenue and booking trends
     */
    private function getChartData(Carbon $start, Carbon $end): array
    {
        $dates = [];
        $revenues = [];
        $bookings = [];

        // Generate daily data within the date range
        $current = $start->copy();
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $dates[] = $current->format('d M');

            // Get revenue for this day
            $dailyRevenue = Booking::where('status', 'confirmed')
                ->whereDate('booking_date', $dateStr)
                ->join('fields', 'bookings.field_id', '=', 'fields.id')
                ->join('time_slots', 'bookings.time_slot_id', '=', 'time_slots.id')
                ->selectRaw('SUM(CAST((JULIANDAY(time_slots.end_time) - JULIANDAY(time_slots.start_time)) * 24 AS INTEGER) * fields.price_per_hour) as total')
                ->value('total') ?? 0;
            
            $revenues[] = $dailyRevenue;

            // Get booking count for this day
            $dailyBookings = Booking::whereDate('booking_date', $dateStr)->count();
            $bookings[] = $dailyBookings;

            $current->addDay();
        }

        return [
            'labels' => $dates,
            'revenues' => $revenues,
            'bookings' => $bookings,
        ];
    }
}
