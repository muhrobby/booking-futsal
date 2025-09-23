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
        $phone = $request->string('phone')->toString();
        $user = $request->user();

        // Jika user tidak mengisi nomor HP, gunakan nomor yang tersimpan di profilnya.
        if (! $phone && $user?->phone) {
            $phone = $user->phone;
        }

        // Ambil riwayat booking berdasarkan nomor HP atau akun login.
        $bookings = Booking::query()
            ->with(['field', 'timeSlot'])
            ->when($phone || $user, function ($query) use ($phone, $user) {
                $query->where(function ($subQuery) use ($phone, $user) {
                    if ($phone) {
                        $subQuery->where('customer_phone', $phone);
                    }

                    if ($user) {
                        $subQuery->orWhere('user_id', $user->id);
                    }
                });
            })
            ->latest('booking_date')
            ->latest('time_slot_id')
            ->limit(30)
            ->get();

        return view('bookings.my', [
            'bookings' => $bookings,
            'phone' => $phone,
        ]);
    }
}
