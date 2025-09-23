@extends('layouts.app')

@php
    // Clone tanggal supaya perhitungan slot tidak mengubah nilai asli.
    $baseDate = $selectedDateInstance ?? \Carbon\Carbon::createFromFormat('Y-m-d', $selectedDate);
    $selectedDateCarbon = $baseDate->copy();
    $now = \Carbon\Carbon::now();
@endphp

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-xl font-semibold text-slate-800">Jadwal Ketersediaan</h1>
        </div>

        <form method="GET" action="{{ route('schedule.index') }}" class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-600">Lapangan</label>
                <select name="field_id" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
                    @foreach ($fields as $field)
                        <option value="{{ $field->id }}" @selected($field->id == $selectedFieldId)>{{ $field->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-600">Tanggal</label>
                <input type="date" name="date" value="{{ $selectedDate }}" min="{{ now()->toDateString() }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Tampilkan</button>
            </div>
            <div class="flex items-end">
                @if ($selectedFieldId)
                    <a href="{{ route('bookings.create', ['field_id' => $selectedFieldId, 'booking_date' => $selectedDate]) }}" class="w-full rounded-xl bg-emerald-500 px-4 py-2 text-center text-sm font-semibold text-white transition hover:bg-emerald-600">Booking</a>
                @endif
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-6 py-3">Jam</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                @foreach ($timeSlots as $timeSlotOption)
                    @php
                        $booking = $bookings->get($timeSlotOption->id);
                        // Hitung jam mulai untuk menentukan apakah slot sudah lewat.
                        $slotStart = $selectedDateCarbon->copy()->setTimeFromTimeString($timeSlotOption->start_time->format('H:i:s'));
                        $isPast = $slotStart->lt($now);
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium">{{ $timeSlotOption->label }}</td>
                        <td class="px-6 py-4">
                            @if ($booking)
                                <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">Sudah dibooking oleh {{ $booking->customer_name }}</span>
                            @elseif ($selectedDateCarbon->isToday() && $isPast)
                                <span class="inline-flex items-center rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-600">Sudah lewat</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Tersedia</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
