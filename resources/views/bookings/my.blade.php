@extends('layouts.app')

@section('title', 'Booking Saya')

@section('content')
    <!-- Header Section -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Booking Saya</h1>
            <p class="text-gray-600 mt-2">Kelola dan pantau semua booking lapangan Anda</p>
        </div>
        <a href="{{ route('bookings.create') }}">
            <x-button variant="primary" size="lg">
                + Pesan Lapangan Baru
            </x-button>
        </a>
    </div>

    <!-- Filters Section -->
    <x-card class="mb-8">
        <form method="GET" action="{{ route('bookings.my') }}" class="space-y-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Filter Booking</h3>
                <a href="{{ route('bookings.my') }}" class="text-sm text-blue-600 hover:text-blue-700">Reset</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        @foreach(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'canceled' => 'Cancelled'] as $value => $label)
                            <option value="{{ $value }}" @if($selectedStatus === $value) selected @endif>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-medium transition">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </x-card>

    <!-- Bookings List or Empty State -->
    @if ($bookings->isEmpty())
        <x-card class="text-center py-16">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <p class="text-gray-600 text-lg font-medium mb-2">Belum ada booking ditemukan</p>
            <p class="text-gray-500 mb-6">Mulai pesan lapangan futsal favorit Anda sekarang</p>
            <a href="{{ route('schedule.index') }}">
                <x-button variant="primary" size="lg">
                    Cari Lapangan
                </x-button>
            </a>
        </x-card>
    @else
        <!-- Desktop Table View -->
        <div class="hidden md:block rounded-lg overflow-hidden border border-gray-200 shadow-lg">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Lapangan</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Tanggal</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Waktu</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Harga</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($bookings as $booking)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $booking->field?->name }}</p>
                                <p class="text-sm text-gray-500">{{ $booking->field?->location }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">{{ $booking->booking_date?->locale('id')->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">{{ $booking->timeSlot?->label }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-900">Rp {{ number_format($booking->field?->price_per_hour, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                    @if($booking->status === 'confirmed') bg-green-100 text-green-700
                                    @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($booking->status === 'canceled') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700
                                    @endif
                                ">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    @if($booking->status === 'pending')
                                        <a href="{{ route('orders.create', $booking) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg transition">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                            Lanjutkan Pembayaran
                                        </a>
                                    @endif
                                    <button onclick="showBookingDetail({{ $booking->id }})" class="text-blue-600 hover:text-blue-700 font-medium text-sm transition">
                                        Lihat Detail
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4">
            @foreach ($bookings as $booking)
                <x-card class="hover:shadow-lg transition">
                    <div class="pb-4 border-b border-gray-200">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $booking->field?->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $booking->field?->location }}</p>
                            </div>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                @if($booking->status === 'confirmed') bg-green-100 text-green-700
                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-700
                                @elseif($booking->status === 'cancelled') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-700
                                @endif
                            ">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="py-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Tanggal</span>
                            <span class="font-medium text-gray-900">{{ $booking->booking_date?->locale('id')->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Waktu</span>
                            <span class="font-medium text-gray-900">{{ $booking->timeSlot?->label }}</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-200 pt-3">
                            <span class="text-gray-600 text-sm font-medium">Harga</span>
                            <span class="font-bold text-blue-600">Rp {{ number_format($booking->field?->price_per_hour, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200 space-y-2">
                        @if($booking->status === 'pending')
                            <a href="{{ route('orders.create', $booking) }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Lanjutkan Pembayaran
                            </a>
                        @endif
                        <button onclick="showBookingDetail({{ $booking->id }})" class="w-full text-center text-blue-600 hover:text-blue-700 font-medium text-sm transition py-2">
                            Lihat Detail
                        </button>
                    </div>
                </x-card>
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($bookings->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $bookings->links() }}
            </div>
        @endif
    @endif

    <!-- Booking Detail Modal -->
    <div id="bookingDetailModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeBookingDetail()"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Detail Booking</h3>
                        <button onclick="closeBookingDetail()" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div id="modalContent" class="space-y-4">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button onclick="closeBookingDetail()" type="button" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const bookings = @json($bookings->items());

    function showBookingDetail(bookingId) {
        const booking = bookings.find(b => b.id === bookingId);
        if (!booking) return;

        const statusColors = {
            'confirmed': 'bg-green-100 text-green-700',
            'pending': 'bg-yellow-100 text-yellow-700',
            'cancelled': 'bg-red-100 text-red-700'
        };

        const statusClass = statusColors[booking.status] || 'bg-gray-100 text-gray-700';
        
        const content = `
            <div class="border-b border-gray-200 pb-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-xl font-bold text-gray-900">${booking.field?.name || '-'}</h4>
                        <p class="text-sm text-gray-500 mt-1">${booking.field?.location || '-'}</p>
                    </div>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold ${statusClass}">
                        ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                    </span>
                </div>
            </div>

            ${booking.status === 'pending' ? `
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Pembayaran Menunggu</p>
                        <p class="text-sm text-yellow-700 mt-1">Silakan lanjutkan pembayaran untuk mengkonfirmasi booking Anda.</p>
                        <a href="/bookings/${booking.id}/checkout" class="inline-flex items-center mt-3 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium text-sm rounded-lg transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Lanjutkan Pembayaran
                        </a>
                    </div>
                </div>
            </div>
            ` : ''}

            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Booking ID</span>
                    <span class="font-medium text-gray-900">#${booking.id}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Tanggal Booking</span>
                    <span class="font-medium text-gray-900">${new Date(booking.booking_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Waktu</span>
                    <span class="font-medium text-gray-900">${booking.time_slot?.start_time.substring(0, 5) || '-'} - ${booking.time_slot?.end_time.substring(0, 5) || '-'}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Nama Pemesan</span>
                    <span class="font-medium text-gray-900">${booking.customer_name || '-'}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">No. Telepon</span>
                    <span class="font-medium text-gray-900">${booking.customer_phone || '-'}</span>
                </div>

                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                    <span class="text-sm font-medium text-gray-600">Harga per Jam</span>
                    <span class="text-lg font-bold text-blue-600">Rp ${new Intl.NumberFormat('id-ID').format(booking.field?.price_per_hour || 0)}</span>
                </div>

                ${booking.notes ? `
                <div class="pt-3 border-t border-gray-200">
                    <span class="text-sm text-gray-600 block mb-1">Catatan</span>
                    <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">${booking.notes}</p>
                </div>
                ` : ''}

                <div class="pt-3 border-t border-gray-200">
                    <span class="text-sm text-gray-600 block mb-1">Dibuat pada</span>
                    <span class="text-sm text-gray-900">${new Date(booking.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</span>
                </div>
            </div>
        `;

        document.getElementById('modalContent').innerHTML = content;
        document.getElementById('bookingDetailModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeBookingDetail() {
        document.getElementById('bookingDetailModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeBookingDetail();
        }
    });
</script>
@endpush
