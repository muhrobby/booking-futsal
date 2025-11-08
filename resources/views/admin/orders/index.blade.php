@extends('layouts.admin')

@section('title', 'Kelola Pembayaran')

@section('content')
    <!-- Page Header -->
    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Kelola Pembayaran</h1>
        <p class="text-gray-600 text-sm sm:text-base mt-1 sm:mt-2">Pantau dan kelola semua transaksi pembayaran booking</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <x-card class="bg-white border-gray-200">
            <p class="text-xs sm:text-sm text-gray-600 font-medium">Total Orders</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </x-card>
        
        <x-card class="bg-yellow-50 border-yellow-200">
            <p class="text-xs sm:text-sm text-yellow-800 font-medium">Pending</p>
            <p class="text-xl sm:text-2xl font-bold text-yellow-600 mt-1">{{ $stats['pending'] }}</p>
        </x-card>
        
        <x-card class="bg-green-50 border-green-200">
            <p class="text-xs sm:text-sm text-green-800 font-medium">Paid</p>
            <p class="text-xl sm:text-2xl font-bold text-green-600 mt-1">{{ $stats['paid'] }}</p>
        </x-card>
        
        <x-card class="bg-red-50 border-red-200">
            <p class="text-xs sm:text-sm text-red-800 font-medium">Failed</p>
            <p class="text-xl sm:text-2xl font-bold text-red-600 mt-1">{{ $stats['failed'] }}</p>
        </x-card>
        
        <x-card class="bg-gray-50 border-gray-200">
            <p class="text-xs sm:text-sm text-gray-800 font-medium">Expired</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-600 mt-1">{{ $stats['expired'] }}</p>
        </x-card>
        
        <x-card class="bg-blue-50 border-blue-200">
            <p class="text-xs sm:text-sm text-blue-800 font-medium">Revenue</p>
            <p class="text-base sm:text-lg font-bold text-blue-600 mt-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
        </x-card>
    </div>

    <!-- Filters Card -->
    <x-card class="mb-6 sm:mb-8">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="space-y-3 sm:space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 sm:mb-4 gap-2">
                <h3 class="text-base sm:text-lg font-bold text-gray-900">Filter Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-xs sm:text-sm text-blue-600 hover:text-blue-700">Reset</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-4">
                <!-- Status Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-2 sm:px-4 py-1.5 sm:py-2 border border-gray-300 rounded-lg text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>Canceled</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Order number, nama, email..."
                           class="w-full px-2 sm:px-4 py-1.5 sm:py-2 border border-gray-300 rounded-lg text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-2 sm:px-4 py-1.5 sm:py-2 border border-gray-300 rounded-lg text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full px-2 sm:px-4 py-1.5 sm:py-2 border border-gray-300 rounded-lg text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="inline-flex items-center justify-center px-3 sm:px-4 py-2 text-xs sm:text-sm bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-medium transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Filter
                </button>
                <a href="{{ route('admin.orders.export', request()->all()) }}" 
                   class="inline-flex items-center justify-center px-3 sm:px-4 py-2 text-xs sm:text-sm bg-green-600 text-white hover:bg-green-700 rounded-lg font-medium transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export CSV
                </a>
            </div>
        </form>
    </x-card>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
        @if($orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-900">Order Number</th>
                            <th class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-900">Customer</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-900">Booking</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-900">Total</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-900">Status</th>
                            <th class="hidden md:table-cell px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-900">Tanggal</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-900">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-3 sm:px-6 py-2 sm:py-4">
                                <p class="text-xs sm:text-sm font-medium text-gray-900">{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-500 lg:hidden">{{ $order->user->name }}</p>
                            </td>
                            <td class="hidden lg:table-cell px-3 sm:px-6 py-2 sm:py-4">
                                <p class="text-xs sm:text-sm text-gray-900">{{ $order->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $order->user->email }}</p>
                            </td>
                            <td class="px-3 sm:px-6 py-2 sm:py-4">
                                <p class="text-xs sm:text-sm text-gray-900 font-medium">{{ $order->booking->field->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $order->booking->booking_date->format('d M Y') }}, 
                                    {{ $order->booking->timeSlot->start_time->format('H:i') }}
                                </p>
                            </td>
                            <td class="px-3 sm:px-6 py-2 sm:py-4">
                                <p class="text-xs sm:text-sm font-semibold text-gray-900">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </p>
                            </td>
                            <td class="px-3 sm:px-6 py-2 sm:py-4">
                                @if($order->status === 'paid')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Paid
                                    </span>
                                @elseif($order->status === 'pending')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @elseif($order->status === 'processing')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Processing
                                    </span>
                                @elseif($order->status === 'failed')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Failed
                                    </span>
                                @elseif($order->status === 'expired')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Expired
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="hidden md:table-cell px-3 sm:px-6 py-2 sm:py-4">
                                <p class="text-xs sm:text-sm text-gray-500">{{ $order->created_at->format('d M Y') }}</p>
                            </td>
                            <td class="px-3 sm:px-6 py-2 sm:py-4">
                                <a href="{{ route('admin.orders.show', $order) }}" 
                                   class="text-blue-600 hover:text-blue-900 text-xs sm:text-sm font-medium">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($orders->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $orders->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-600 font-medium">Belum ada order ditemukan</p>
                <p class="text-sm text-gray-500 mt-1">Order akan muncul di sini setelah customer melakukan booking</p>
            </div>
        @endif
    </div>
@endsection
