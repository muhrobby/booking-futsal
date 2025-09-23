<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Field;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        // Tampilkan hanya lapangan aktif agar jadwal rapi.
        $fields = Field::query()->where('is_active', true)->orderBy('name')->get();
        $selectedFieldId = $request->integer('field_id') ?: $fields->first()?->id;

        $selectedDateInput = $request->input('date');

        // Usahakan parse tanggal dari query string; fallback ke hari ini bila format tidak cocok.
        $selectedDate = now();
        if ($selectedDateInput) {
            try {
                $selectedDate = Carbon::createFromFormat('Y-m-d', $selectedDateInput);
            } catch (\Throwable $exception) {
                try {
                    $selectedDate = Carbon::parse($selectedDateInput);
                } catch (\Throwable $fallbackException) {
                    $selectedDate = now();
                }
            }
        }
        $selectedDate = $selectedDate->startOfDay();

        // Ambil booking yang bukan canceled untuk mengisi jadwal.
        $bookings = Booking::query()
            ->whereDate('booking_date', $selectedDate)
            ->where('status', '!=', 'canceled')
            ->when($selectedFieldId, fn ($query) => $query->where('field_id', $selectedFieldId))
            ->get()
            ->keyBy(fn (Booking $booking) => $booking->time_slot_id);

        // Daftar slot aktif untuk dirender di tabel.
        $timeSlots = TimeSlot::query()->where('is_active', true)->orderBy('start_time')->get();

        return view('schedule.index', [
            'fields' => $fields,
            'selectedFieldId' => $selectedFieldId,
            'selectedDate' => $selectedDate->toDateString(),
            'selectedDateInstance' => $selectedDate,
            'timeSlots' => $timeSlots,
            'bookings' => $bookings,
        ]);
    }
}
