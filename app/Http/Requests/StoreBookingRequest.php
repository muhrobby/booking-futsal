<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'field_id' => [
                'required',
                Rule::exists('fields', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'time_slot_id' => [
                'required',
                Rule::exists('time_slots', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+]+$/'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_phone.regex' => 'Nomor HP hanya boleh berisi angka atau tanda +.',
            'booking_date.after_or_equal' => 'Tanggal booking minimal hari ini.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled(['field_id', 'booking_date', 'time_slot_id'])) {
                return;
            }

            // Tolak bila kombinasi lapangan + tanggal + slot sudah dipakai (selain status canceled).
            $exists = Booking::query()
                ->where('field_id', $this->integer('field_id'))
                ->whereDate('booking_date', $this->date('booking_date'))
                ->where('time_slot_id', $this->integer('time_slot_id'))
                ->where('status', '!=', 'canceled')
                ->exists();

            if ($exists) {
                $validator->errors()->add('time_slot_id', 'Slot sudah dipesan.');
            }

            $bookingDateInput = $this->input('booking_date');
            $slotId = $this->integer('time_slot_id');

            if ($bookingDateInput && $slotId) {
                try {
                    // Validasi awal untuk format YYYY-mm-dd.
                    $bookingDate = Carbon::createFromFormat('Y-m-d', $bookingDateInput)->startOfDay();
                } catch (\Throwable $exception) {
                    // Jika format berbeda (misal dari browser tertentu), fallback ke parse bebas.
                    $bookingDate = Carbon::parse($bookingDateInput)->startOfDay();
                }

                if ($bookingDate->isToday()) {
                    $timeSlot = TimeSlot::find($slotId);

                    if ($timeSlot) {
                        // Larang booking slot hari ini bila jam sudah lewat.
                        $slotStart = $bookingDate->copy()->setTimeFromTimeString($timeSlot->start_time->format('H:i:s'));

                        if (Carbon::now()->greaterThanOrEqualTo($slotStart)) {
                            $validator->errors()->add('time_slot_id', 'Slot ini sudah lewat dan tidak dapat dipesan.');
                        }
                    }
                }
            }
        });
    }
}
