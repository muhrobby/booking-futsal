@extends('layouts.admin')

@section('title', 'Kelola Booking')

@section('content')
    <!-- Header Section -->
    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Kelola Booking</h1>
        <p class="text-gray-600 text-sm sm:text-base mt-1 sm:mt-2">Pantau, kelola, dan ubah status semua booking lapangan</p>
    </div>

    <!-- Filters Card -->
    <x-card class="mb-6 sm:mb-8">
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="space-y-3 sm:space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 sm:mb-4 gap-2">
                <h3 class="text-base sm:text-lg font-bold text-gray-900">Filter Booking</h3>
                <a href="{{ route('admin.bookings.index') }}" class="text-xs sm:text-sm text-blue-600 hover:text-blue-700">Reset</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-4">
                <!-- Field Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Lapangan</label>
                    <select name="field_id" class="w-full px-2 sm:px-4 py-1.5 sm:py-2 border border-gray-300 rounded-lg text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Lapangan</option>
                        @foreach ($fields as $field)
                            <option value="{{ $field->id }}" @if(($filters['field_id'] ?? null) == $field->id) selected @endif>
                                {{ $field->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-2 sm:px-4 py-1.5 sm:py-2 border border-gray-300 rounded-lg text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="pending" @if(($filters['status'] ?? null) === 'pending') selected @endif>Pending</option>
                        <option value="confirmed" @if(($filters['status'] ?? null) === 'confirmed') selected @endif>Confirmed</option>
                        <option value="cancelled" @if(($filters['status'] ?? null) === 'cancelled') selected @endif>Cancelled</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="date" value="{{ $filters['date'] ?? '' }}" class="w-full px-2 sm:px-4 py-1.5 sm:py-2 border border-gray-300 rounded-lg text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Action -->
                <div class="flex items-end gap-1 sm:gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-2 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-medium transition">
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Bookings Table -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-900">Tanggal</th>
                    <th class="hidden md:table-cell px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-900">Lapangan</th>
                    <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-900">Waktu</th>
                    <th class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-900">Pemesan</th>
                    <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-900">Status</th>
                    <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-900">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($bookings as $booking)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 sm:px-6 py-2 sm:py-4">
                            <p class="font-medium text-xs sm:text-sm text-gray-900">{{ $booking->booking_date->locale('id')->format('d M Y') }}</p>
                        </td>
                        <td class="hidden md:table-cell px-3 sm:px-6 py-2 sm:py-4">
                            <p class="font-medium text-xs sm:text-sm text-gray-900 truncate">{{ $booking->field?->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $booking->user?->name ?? $booking->customer_name }}</p>
                        </td>
                        <td class="px-3 sm:px-6 py-2 sm:py-4">
                            <p class="text-xs sm:text-sm text-gray-600">{{ $booking->timeSlot?->start_time->format('H:i') }} - {{ $booking->timeSlot?->end_time->format('H:i') }}</p>
                        </td>
                        <td class="hidden lg:table-cell px-3 sm:px-6 py-2 sm:py-4">
                            <p class="text-xs sm:text-sm text-gray-900">{{ $booking->user?->phone ?? $booking->customer_phone }}</p>
                        </td>
                        <td class="px-3 sm:px-6 py-2 sm:py-4">
                            <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="flex items-center gap-1">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="px-2 sm:px-3 py-1 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    onchange="this.form.submit()">
                                    <option value="pending" @if($booking->status === 'pending') selected @endif>Pending</option>
                                    <option value="confirmed" @if($booking->status === 'confirmed') selected @endif>Confirmed</option>
                                    <option value="cancelled" @if($booking->status === 'cancelled') selected @endif>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-3 sm:px-6 py-2 sm:py-4">
                            <button type="button" class="text-blue-600 hover:text-blue-700 text-xs sm:text-sm font-medium"
                                onclick="alert('Detail: ' + '{{ $booking->field?->name }}' + ' - {{ $booking->booking_date->format('d M Y') }}')">
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 sm:px-6 py-8 sm:py-12 text-center">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400 mx-auto mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <p class="text-gray-600 font-medium text-sm">Belum ada booking ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($bookings->hasPages())
        <div class="mt-6 sm:mt-8 flex justify-center overflow-x-auto">
            {{ $bookings->links() }}
        </div>
    @endif
@endsection
