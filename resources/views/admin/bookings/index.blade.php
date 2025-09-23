@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-6">
        <h1 class="text-xl font-semibold text-slate-800">Kelola Booking</h1>

        <form method="GET" action="{{ route('admin.bookings.index') }}" class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-600">Lapangan</label>
                <select name="field_id" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
                    <option value="">Semua</option>
                    @foreach ($fields as $field)
                        <option value="{{ $field->id }}" @selected(($filters['field_id'] ?? null) == $field->id)>{{ $field->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-600">Status</label>
                <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
                    <option value="">Semua</option>
                    @foreach (['pending', 'confirmed', 'canceled'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? null) === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-600">Tanggal</label>
                <input type="date" name="date" value="{{ $filters['date'] ?? '' }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">Filter</button>
                <a href="{{ route('admin.bookings.index') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Reset</a>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-6 py-3 text-left">Tanggal</th>
                        <th class="px-6 py-3 text-left">Lapangan</th>
                        <th class="px-6 py-3 text-left">Slot</th>
                        <th class="px-6 py-3 text-left">Pemesan</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Catatan</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($bookings as $booking)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $booking->booking_date->format('d M Y') }}</td>
                            <td class="px-6 py-4">{{ $booking->field?->name }}</td>
                            <td class="px-6 py-4">{{ $booking->timeSlot?->label }}</td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $booking->customer_name }}</div>
                                <div class="text-xs text-slate-500">{{ $booking->customer_phone }}</div>
                            </td>
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
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="flex flex-wrap items-center justify-end gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100" required>
                                        @foreach (['pending', 'confirmed', 'canceled'] as $status)
                                            <option value="{{ $status }}" @selected($booking->status === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="filter_field_id" value="{{ $filters['field_id'] ?? '' }}">
                                    <input type="hidden" name="filter_status" value="{{ $filters['status'] ?? '' }}">
                                    <input type="hidden" name="filter_date" value="{{ $filters['date'] ?? '' }}">
                                    <input type="text" name="notes" value="{{ $booking->notes }}" placeholder="Catatan" class="w-40 rounded-lg border border-slate-300 px-3 py-2 text-xs text-slate-700 focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
                                    <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">Belum ada data booking.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $bookings->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
