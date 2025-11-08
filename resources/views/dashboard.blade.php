@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Selamat Datang, {{ $user->name }}! 👋</h1>
        <p class="text-gray-600 text-sm sm:text-base mt-1 sm:mt-2">Kelola booking lapangan futsal Anda dengan mudah</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-8 sm:mb-12">
        <!-- Total Bookings -->
        <x-stats-card 
            title="Total Booking" 
            :value="$totalBookings"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
        </x-stats-card>

        <!-- Upcoming Bookings -->
        <x-stats-card 
            title="Booking Mendatang" 
            :value="$upcomingBookings"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </x-stats-card>

        <!-- Completed Bookings -->
        <x-stats-card 
            title="Booking Selesai" 
            :value="$completedBookings"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </x-stats-card>

        <!-- Total Spending -->
        <x-stats-card 
            title="Total Pengeluaran" 
            :value="'Rp ' . number_format($totalSpending, 0, ',', '.')"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </x-stats-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
        <!-- Next Booking / Quick Actions -->
        <div class="lg:col-span-2 space-y-6 sm:space-y-8">
            <!-- Next Booking -->
            @if ($nextBooking)
                <x-card class="bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between pb-3 sm:pb-4 border-b border-blue-200 gap-2">
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-blue-900">Booking Mendatang</h3>
                            <p class="text-xs sm:text-sm text-blue-700 mt-1">Jangan lupa untuk hadir tepat waktu!</p>
                        </div>
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    
                    <div class="py-3 sm:py-4">
                        <div class="grid grid-cols-2 gap-2 sm:gap-4 mb-3 sm:mb-4">
                            <div>
                                <p class="text-xs font-semibold text-blue-700 uppercase">Lapangan</p>
                                <p class="text-sm sm:text-lg font-bold text-blue-900 mt-1">{{ $nextBooking->field->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-blue-700 uppercase">Harga</p>
                                <p class="text-sm sm:text-lg font-bold text-blue-900 mt-1">Rp {{ number_format($nextBooking->field->price_per_hour, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-blue-700 uppercase">Tanggal</p>
                                <p class="text-sm sm:text-lg font-bold text-blue-900 mt-1">{{ $nextBooking->timeSlot->start_time->locale('id')->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-blue-700 uppercase">Jam</p>
                                <p class="text-sm sm:text-lg font-bold text-blue-900 mt-1">{{ $nextBooking->timeSlot->start_time->format('H:i') }} - {{ $nextBooking->timeSlot->end_time->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 sm:pt-4 border-t border-blue-200 flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('bookings.my') }}" class="flex-1">
                            <button class="w-full inline-flex items-center justify-center px-3 sm:px-4 py-2 text-sm sm:text-base bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-medium transition">
                                Lihat Detail
                            </button>
                        </a>
                        <button onclick="createReminder({{ $nextBooking->id }})" class="flex-1 inline-flex items-center justify-center px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium transition">
                            Buat Reminder
                        </button>
                    </div>
                </x-card>
            @else
                <x-card class="bg-gray-50 border-gray-200 text-center py-8 sm:py-12">
                    <svg class="w-10 sm:w-12 h-10 sm:h-12 text-gray-400 mx-auto mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-600 font-medium text-sm sm:text-base">Belum ada booking mendatang</p>
                    <p class="text-gray-500 text-xs sm:text-sm mt-1">Pesan lapangan sekarang untuk memulai</p>
                    <a href="{{ route('schedule.index') }}">
                        <x-button variant="primary" class="mt-3 sm:mt-4 w-full sm:w-auto text-sm">
                            Cari Lapangan
                        </x-button>
                    </a>
                </x-card>
            @endif

            <!-- Recent Orders -->
            <x-card>
                <div class="pb-3 sm:pb-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">Pembayaran Saya</h3>
                    @if($pendingOrders > 0)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            {{ $pendingOrders }} Pending
                        </span>
                    @endif
                </div>
                
                <div class="py-3 sm:py-4">
                    @if ($recentOrders->isEmpty())
                        <div class="text-center py-6 sm:py-8">
                            <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <p class="text-gray-500 text-xs sm:text-sm mt-2">Belum ada riwayat pembayaran</p>
                        </div>
                    @else
                        <div class="overflow-x-auto -mx-3 sm:mx-0">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                        <th class="px-3 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lapangan</th>
                                        <th class="px-3 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-3 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-3 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($recentOrders as $order)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2 sm:px-4 sm:py-3 whitespace-nowrap">
                                                <div class="text-xs sm:text-sm font-medium text-gray-900">#{{ $order->order_number }}</div>
                                                <div class="text-xs text-gray-500">{{ $order->created_at->locale('id')->format('d M Y') }}</div>
                                            </td>
                                            <td class="px-3 py-2 sm:px-4 sm:py-3">
                                                <div class="text-xs sm:text-sm text-gray-900">{{ $order->booking->field->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $order->booking->timeSlot->start_time->locale('id')->format('d M, H:i') }}</div>
                                            </td>
                                            <td class="px-3 py-2 sm:px-4 sm:py-3 whitespace-nowrap">
                                                <div class="text-xs sm:text-sm font-bold text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                                            </td>
                                            <td class="px-3 py-2 sm:px-4 sm:py-3 whitespace-nowrap">
                                                @if($order->status === 'paid')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                        Lunas
                                                    </span>
                                                @elseif($order->status === 'processing')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Proses
                                                    </span>
                                                @elseif($order->status === 'pending')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                @elseif($order->status === 'failed')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                        Gagal
                                                    </span>
                                                @elseif($order->status === 'expired')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Expired
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 sm:px-4 sm:py-3 whitespace-nowrap text-xs sm:text-sm">
                                                @if(in_array($order->status, ['pending', 'processing']))
                                                    <a href="{{ route('orders.create', $order->booking) }}" class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                        </svg>
                                                        Bayar
                                                    </a>
                                                @else
                                                    <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 transition">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        Detail
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <a href="{{ route('orders.index') }}" class="mt-3 sm:mt-4 block text-center text-xs sm:text-sm font-medium text-blue-600 hover:text-blue-700 transition">
                            Lihat Semua Pembayaran →
                        </a>
                    @endif
                </div>
            </x-card>

            <!-- Quick Actions -->
            <x-card>
                <div class="pb-3 sm:pb-4 border-b border-gray-200">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">Aksi Cepat</h3>
                </div>
                
                <div class="py-3 sm:py-4 grid grid-cols-2 gap-2 sm:gap-3">
                    <a href="{{ route('schedule.index') }}" class="block">
                        <div class="p-2 sm:p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition text-center">
                            <svg class="w-5 sm:w-6 h-5 sm:h-6 text-blue-600 mx-auto mb-1 sm:mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <p class="text-xs sm:text-sm font-medium text-gray-900">Pesan Lapangan</p>
                        </div>
                    </a>

                    <a href="{{ route('bookings.my') }}" class="block">
                        <div class="p-2 sm:p-4 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition text-center">
                            <svg class="w-5 sm:w-6 h-5 sm:h-6 text-emerald-600 mx-auto mb-1 sm:mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-xs sm:text-sm font-medium text-gray-900">Booking Saya</p>
                        </div>
                    </a>

                    <a href="{{ route('orders.index') }}" class="block">
                        <div class="p-2 sm:p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition text-center">
                            <svg class="w-5 sm:w-6 h-5 sm:h-6 text-purple-600 mx-auto mb-1 sm:mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <p class="text-xs sm:text-sm font-medium text-gray-900">Order Saya</p>
                        </div>
                    </a>

                    <a href="{{ route('profile') }}" class="block">
                        <div class="p-2 sm:p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition text-center">
                            <svg class="w-5 sm:w-6 h-5 sm:h-6 text-orange-600 mx-auto mb-1 sm:mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <p class="text-xs sm:text-sm font-medium text-gray-900">Profile</p>
                        </div>
                    </a>
                </div>
            </x-card>
        </div>

        <!-- Recent Bookings Sidebar -->
        <div>
            <x-card class="sticky top-16 sm:top-20">
                <div class="pb-3 sm:pb-4 border-b border-gray-200">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">Booking Terakhir</h3>
                </div>
                
                <div class="py-3 sm:py-4">
                    @if ($recentBookings->isEmpty())
                        <div class="text-center py-4 sm:py-6">
                            <p class="text-gray-500 text-xs sm:text-sm">Belum ada riwayat booking</p>
                        </div>
                    @else
                        <div class="space-y-2 sm:space-y-3">
                            @foreach ($recentBookings as $booking)
                                <div class="p-2 sm:p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer" onclick="window.location.href='{{ route('bookings.my') }}'">
                                    <p class="font-medium text-xs sm:text-sm text-gray-900">{{ $booking->field->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5 sm:mt-1">
                                        {{ $booking->timeSlot->start_time->locale('id')->format('d M Y') }}
                                    </p>
                                    <div class="mt-1 sm:mt-2 flex items-center justify-between gap-1">
                                        <p class="text-xs font-medium">
                                            <span class="inline-block px-1.5 sm:px-2 py-0.5 text-xs rounded-full 
                                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif
                                            ">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </p>
                                        <p class="text-xs font-bold text-gray-900">Rp {{ number_format($booking->field->price_per_hour, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <a href="{{ route('bookings.my') }}" class="mt-3 sm:mt-4 block text-center text-xs sm:text-sm font-medium text-blue-600 hover:text-blue-700 transition">
                            Lihat Semua Booking →
                        </a>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function createReminder(bookingId) {
        @if($nextBooking)
        const booking = {
            id: {{ $nextBooking->id }},
            field: "{{ $nextBooking->field->name }}",
            date: "{{ $nextBooking->booking_date->locale('id')->format('d F Y') }}",
            time: "{{ $nextBooking->timeSlot->start_time->format('H:i') }} - {{ $nextBooking->timeSlot->end_time->format('H:i') }}",
            location: "{{ $nextBooking->field->location ?? '-' }}",
            price: "Rp {{ number_format($nextBooking->field->price_per_hour, 0, ',', '.') }}"
        };

        const reminderText = `📅 REMINDER BOOKING FUTSAL

🏟️ Lapangan: ${booking.field}
📆 Tanggal: ${booking.date}
⏰ Waktu: ${booking.time}
📍 Lokasi: ${booking.location}
💰 Harga: ${booking.price}

Jangan lupa hadir tepat waktu! ⚽`;

        // Copy to clipboard
        navigator.clipboard.writeText(reminderText).then(() => {
            // Show success notification
            showNotification('✅ Reminder berhasil disalin!', 'Paste ke aplikasi kalender atau notes Anda.', 'success');
        }).catch(() => {
            // Fallback: show modal with text
            showReminderModal(reminderText);
        });
        @endif
    }

    function showNotification(title, message, type = 'success') {
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-blue-500';
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg z-50 max-w-sm animate-fade-in`;
        notification.innerHTML = `
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-semibold">${title}</p>
                    <p class="text-sm mt-1 opacity-90">${message}</p>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    function showReminderModal(text) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 overflow-y-auto';
        modal.innerHTML = `
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="this.parentElement.parentElement.remove()"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">📅 Reminder Booking</h3>
                            <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <textarea readonly class="w-full p-4 border border-gray-300 rounded-lg bg-gray-50 text-sm font-mono" rows="10">${text}</textarea>
                        <p class="text-sm text-gray-600 mt-2">Copy teks di atas untuk membuat reminder di aplikasi kalender atau notes Anda.</p>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                        <button onclick="navigator.clipboard.writeText(\`${text}\`).then(() => {showNotification('✅ Berhasil disalin!', 'Paste ke aplikasi Anda.', 'success'); this.closest('.fixed').remove();})" class="w-full inline-flex justify-center rounded-lg bg-blue-600 px-4 py-2 text-base font-medium text-white hover:bg-blue-700 sm:w-auto sm:text-sm">
                            Copy Text
                        </button>
                        <button onclick="this.closest('.fixed').remove()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endpush
