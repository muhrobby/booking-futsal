@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-xl font-semibold text-slate-800">Booking Saya</h1>
            <a href="{{ route('bookings.create') }}" class="inline-flex items-center justify-center rounded-xl border border-blue-600 px-4 py-2 text-sm font-medium text-blue-600 transition hover:bg-blue-50">+ Booking Baru</a>
        </div>

        <form method="GET" action="{{ route('bookings.my') }}" class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:grid-cols-[auto_auto_auto] sm:items-end">
            <div class="space-y-2 sm:min-w-[220px]">
                <label class="block text-sm font-medium text-slate-600">Nomor HP</label>
                <input type="text" name="phone" value="{{ $phone }}" placeholder="Contoh: 08123456789" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
            </div>
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">Cari Booking</button>
            <a href="{{ route('bookings.my') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Reset</a>
        </form>

        @if ($bookings->isEmpty())
            <div class="rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center text-slate-500 shadow-sm">
                Belum ada booking yang ditemukan. Pastikan nomor HP sesuai dengan data pada saat booking.
            </div>
        @else
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                        <tr>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Lapangan</th>
                            <th class="px-6 py-3">Pemesan</th>
                            <th class="px-6 py-3">Slot</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($bookings as $booking)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-slate-800">{{ $booking->booking_date->format('d M Y') }}</td>
                                <td class="px-6 py-4">{{ $booking->field?->name }}</td>
                                <td class="px-6 py-4">{{ $booking->customer_name }}</td>
                                <td class="px-6 py-4">{{ $booking->timeSlot?->label }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'confirmed' => 'bg-emerald-100 text-emerald-700',
                                            'canceled' => 'bg-rose-100 text-rose-700',
                                        ];
                                    @endphp
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$booking->status] ?? 'bg-slate-200 text-slate-600' }}">{{ $booking->status }}</span>
                                </td>
                                <td class="px-6 py-4">{{ $booking->notes ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
