<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Field;
use App\Models\TimeSlot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $fields = Field::query()->orderBy('name')->get();
        $timeSlots = TimeSlot::query()->orderBy('start_time')->get();

        $bookings = Booking::query()
            ->with(['field', 'timeSlot', 'user'])
            ->when($request->filled('field_id'), fn ($query) => $query->where('field_id', $request->integer('field_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('date'), fn ($query) => $query->whereDate('booking_date', $request->date('date')))
            ->orderByDesc('booking_date')
            ->orderBy('time_slot_id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'fields' => $fields,
            'timeSlots' => $timeSlots,
            'filters' => $request->only(['field_id', 'status', 'date']),
        ]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,canceled'],
            'notes' => ['nullable', 'string'],
        ]);

        $booking->update($validated);

        $filters = array_filter([
            'field_id' => $request->input('filter_field_id'),
            'status' => $request->input('filter_status'),
            'date' => $request->input('filter_date'),
        ], fn ($value) => filled($value));

        return redirect()->route('admin.bookings.index', $filters)
            ->with('status', 'Status booking diperbarui.');
    }
}
