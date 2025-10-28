<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Field;
use App\Models\TimeSlot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(Request $request): View
    {
        // Ambil daftar lapangan & slot aktif agar form hanya menampilkan opsi valid.
        $fields = Field::query()->where('is_active', true)->orderBy('name')->get();
        $timeSlots = TimeSlot::query()
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        return view('bookings.create', [
            'fields' => $fields,
            'timeSlots' => $timeSlots,
            'selectedFieldId' => $request->integer('field_id'),
            'selectedDate' => $request->input('booking_date', now()->toDateString()),
            'selectedSlotId' => $request->integer('time_slot_id'),
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        // Reuse slot yang pernah dibatalkan agar constraint unik tidak memblokir booking baru.
        $existingCanceled = Booking::query()
            ->where('field_id', $data['field_id'])
            ->whereDate('booking_date', $data['booking_date'])
            ->where('time_slot_id', $data['time_slot_id'])
            ->where('status', 'canceled')
            ->first();

        if ($existingCanceled) {
            $existingCanceled->update($data);
        } else {
            Booking::create($data);
        }

        return redirect()
            ->route('bookings.my', ['phone' => $data['customer_phone']])
            ->with('status', 'Booking berhasil dibuat dan menunggu konfirmasi admin.');
    }

    public function myBookings(Request $request): View
    {
        $user = $request->user();
        $query = Booking::query()->with(['field', 'timeSlot']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->input('date_to'));
        }

        // Get user bookings only
        if ($user) {
            $query->where('user_id', $user->id);
        }

        // Order by latest first
        $bookings = $query->latest('booking_date')
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('bookings.my', [
            'bookings' => $bookings,
            'statuses' => ['pending', 'confirmed', 'cancelled'],
            'selectedStatus' => $request->input('status'),
            'dateFrom' => $request->input('date_from'),
            'dateTo' => $request->input('date_to'),
        ]);
    }
}
