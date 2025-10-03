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

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($timeSlots as $timeSlotOption)
                @php
                    $booking = $bookings->get($timeSlotOption->id);
                    // Hitung jam mulai untuk menentukan apakah slot sudah lewat.
                    $slotStart = $selectedDateCarbon->copy()->setTimeFromTimeString($timeSlotOption->start_time->format('H:i:s'));
                    $isPast = $slotStart->lt($now);

                    $statusBadgeClasses = 'bg-emerald-100 text-emerald-700';
                    $statusText = 'Tersedia';
                    $statusDescription = 'Slot ini masih tersedia untuk dipesan.';

                    if ($booking) {
                        $statusBadgeClasses = 'bg-rose-100 text-rose-700';
                        $statusText = 'Sudah dibooking';
                        $statusDescription = 'Sudah dibooking oleh ' . $booking->customer_name . '.';
                    } elseif ($selectedDateCarbon->isToday() && $isPast) {
                        $statusBadgeClasses = 'bg-slate-200 text-slate-600';
                        $statusText = 'Sudah lewat';
                        $statusDescription = 'Slot ini sudah melewati jam mulai.';
                    }
                @endphp
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300 hover:shadow-md">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Jam</p>
                            <p class="text-lg font-semibold text-slate-800">{{ $timeSlotOption->label }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusBadgeClasses }}">{{ $statusText }}</span>
                    </div>
                    <p class="mt-4 text-sm text-slate-500">{{ $statusDescription }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
