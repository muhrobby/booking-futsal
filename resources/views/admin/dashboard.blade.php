@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Page Header with Date Range Filter -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
                <p class="text-gray-600 mt-2">Kelola dan pantau semua aktivitas platform Futsal Neo S</p>
            </div>
            
            <!-- Date Range Filter -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                    <div>
                        <label for="start_date" class="block text-xs font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input 
                            type="date" 
                            id="start_date" 
                            name="start_date" 
                            value="{{ $startDate }}"
                            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    <div>
                        <label for="end_date" class="block text-xs font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                        <input 
                            type="date" 
                            id="end_date" 
                            name="end_date" 
                            value="{{ $endDate }}"
                            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                        Filter
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium text-sm">
                        Reset
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <x-stats-card 
            title="Total Pengguna" 
            :value="$totalUsers"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 10H9m6 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </x-stats-card>

        <!-- Total Fields -->
        <x-stats-card 
            title="Total Lapangan" 
            :value="$totalFields"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
        </x-stats-card>

        <!-- Total Bookings -->
        <x-stats-card 
            title="Total Booking" 
            :value="$totalBookings"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
        </x-stats-card>

        <!-- Total Revenue -->
        <x-stats-card 
            title="Total Pendapatan" 
            :value="'Rp ' . number_format($totalRevenue, 0, ',', '.')"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </x-stats-card>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Pending Confirmations -->
        <x-card class="bg-blue-50 border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Menunggu Konfirmasi</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $pendingBookings }}</p>
                    <p class="text-xs text-gray-500 mt-1">Booking yang perlu dikonfirmasi</p>
                </div>
                <svg class="w-12 h-12 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1V3a1 1 0 011-1h5a1 1 0 011 1v1h1V3a1 1 0 011 1v1h1a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v1a2 2 0 01-2 2h-1v1a1 1 0 11-2 0v-1h-1v1a1 1 0 11-2 0v-1h-1v1a1 1 0 11-2 0v-1H7a2 2 0 01-2-2v-1H4a1 1 0 110-2h1v-2H4a1 1 0 110-2h1V9H4a1 1 0 110-2h1V7a2 2 0 012-2h1V4a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
        </x-card>

        <!-- Today's Bookings -->
        <x-card class="bg-emerald-50 border-emerald-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Booking Hari Ini</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-2">{{ $todayBookings }}</p>
                    <p class="text-xs text-gray-500 mt-1">Booking yang terjadi hari ini</p>
                </div>
                <svg class="w-12 h-12 text-emerald-200" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v2h16V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5H4v6a2 2 0 002 2h12a2 2 0 002-2V7h-2v1a1 1 0 11-2 0V7H9v1a1 1 0 11-2 0V7H6v1a1 1 0 11-2 0V7z" clip-rule="evenodd" />
                </svg>
            </div>
        </x-card>

        <!-- Occupancy Rate -->
        <x-card class="bg-orange-50 border-orange-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Tingkat Okupansi</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ $occupancyRate }}%</p>
                    <p class="text-xs text-gray-500 mt-1">Slot waktu yang terpesan</p>
                </div>
                <svg class="w-12 h-12 text-orange-200" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                </svg>
            </div>
        </x-card>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Revenue Trend Chart -->
        <x-card>
            <div class="pb-4 border-b border-gray-200 mb-6">
                <h3 class="text-lg font-bold text-gray-900">Trend Pendapatan</h3>
                <p class="text-sm text-gray-500 mt-1">Grafik pendapatan berdasarkan rentang tanggal</p>
            </div>
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </x-card>

        <!-- Booking Trend Chart -->
        <x-card>
            <div class="pb-4 border-b border-gray-200 mb-6">
                <h3 class="text-lg font-bold text-gray-900">Trend Booking</h3>
                <p class="text-sm text-gray-500 mt-1">Jumlah booking berdasarkan rentang tanggal</p>
            </div>
            <div class="h-80">
                <canvas id="bookingChart"></canvas>
            </div>
        </x-card>
    </div>

    <!-- Recent Bookings & Top Fields -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Recent Bookings -->
        <div class="lg:col-span-2">
            <x-card>
                <div class="pb-4 border-b border-gray-200 mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Booking Terbaru</h3>
                </div>

                <div class="space-y-4">
                    @forelse($recentBookings as $booking)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $booking->field?->name }}</p>
                                <p class="text-sm text-gray-500 mt-1">{{ $booking->user?->name }} • {{ $booking->booking_date->locale('id')->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $booking->timeSlot?->start_time->format('H:i') }} - {{ $booking->timeSlot?->end_time->format('H:i') }}</p>
                            </div>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                @if($booking->status === 'confirmed') bg-green-100 text-green-700
                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-700
                                @else bg-gray-100 text-gray-700
                                @endif
                            ">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">Belum ada booking terbaru</p>
                    @endforelse
                </div>

                <div class="pt-4 border-t border-gray-200 mt-4">
                    <a href="{{ route('admin.bookings.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition">
                        Lihat Semua Booking →
                    </a>
                </div>
            </x-card>
        </div>

        <!-- Top Fields -->
        <div>
            <x-card>
                <div class="pb-4 border-b border-gray-200 mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Lapangan Teratas</h3>
                </div>

                <div class="space-y-3">
                    @forelse($topFields as $field)
                        <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <p class="font-medium text-sm text-gray-900">{{ $field->name }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <p class="text-xs text-gray-500">{{ $field->bookings_count }} booking</p>
                                <div class="w-16 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-600" style="width: {{ $topFields->first()->bookings_count > 0 ? ($field->bookings_count / $topFields->first()->bookings_count) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8 text-sm">Belum ada data lapangan</p>
                    @endforelse
                </div>

                <div class="pt-4 border-t border-gray-200 mt-4">
                    <a href="{{ route('admin.fields.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition">
                        Kelola Lapangan →
                    </a>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Booking Status Distribution -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Booking Status Breakdown -->
        <x-card>
            <div class="pb-4 border-b border-gray-200 mb-6">
                <h3 class="text-lg font-bold text-gray-900">Status Booking</h3>
            </div>

            <div class="space-y-4">
                @php
                    $statuses = ['confirmed' => 'Confirmed', 'pending' => 'Pending', 'cancelled' => 'Cancelled'];
                    $statusColors = ['confirmed' => 'emerald', 'pending' => 'yellow', 'cancelled' => 'red'];
                    $totalStatus = $bookingsByStatus->sum();
                @endphp

                @foreach($statuses as $key => $label)
                    @php
                        $count = $bookingsByStatus[$key] ?? 0;
                        $percentage = $totalStatus > 0 ? ($count / $totalStatus) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-medium text-gray-900">{{ $label }}</p>
                            <span class="text-sm font-bold text-gray-900">{{ $count }}</span>
                        </div>
                        <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-{{ $statusColors[$key] }}-500" style="width: {{ $percentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ number_format($percentage, 1) }}%</p>
                    </div>
                @endforeach
            </div>
        </x-card>

        <!-- Quick Actions -->
        <x-card>
            <div class="pb-4 border-b border-gray-200 mb-6">
                <h3 class="text-lg font-bold text-gray-900">Aksi Cepat</h3>
            </div>

            <div class="space-y-3">
                <a href="{{ route('admin.bookings.index') }}" class="block p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                    <p class="font-medium text-gray-900">Kelola Booking</p>
                    <p class="text-xs text-gray-500 mt-1">Lihat semua booking dan ubah status</p>
                </a>

                <a href="{{ route('admin.fields.index') }}" class="block p-4 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition">
                    <p class="font-medium text-gray-900">Kelola Lapangan</p>
                    <p class="text-xs text-gray-500 mt-1">Tambah, ubah, atau hapus lapangan</p>
                </a>

                <a href="{{ route('admin.fields.create') }}" class="block p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                    <p class="font-medium text-gray-900">Tambah Lapangan Baru</p>
                    <p class="text-xs text-gray-500 mt-1">Tambahkan lapangan futsal baru</p>
                </a>
            </div>
        </x-card>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Chart data from backend
    const chartData = @json($chartData);

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: chartData.revenues,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(context.parsed.y);
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(value);
                        }
                    }
                }
            }
        }
    });

    // Booking Chart
    const bookingCtx = document.getElementById('bookingChart').getContext('2d');
    new Chart(bookingCtx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Jumlah Booking',
                data: chartData.bookings,
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgb(16, 185, 129)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y + ' booking';
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return value;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
