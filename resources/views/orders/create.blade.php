<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-6">Detail Booking</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Booking Information -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lapangan</label>
                                <p class="mt-1 text-lg font-semibold">{{ $booking->field->name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Booking</label>
                                <p class="mt-1 text-lg">{{ $booking->booking_date->format('d F Y') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Waktu</label>
                                <p class="mt-1 text-lg">{{ $booking->timeSlot->start_time }} - {{ $booking->timeSlot->end_time }}</p>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama</label>
                                <p class="mt-1 text-lg">{{ $booking->customer_name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Telepon</label>
                                <p class="mt-1 text-lg">{{ $booking->customer_phone }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <p class="mt-1 text-lg">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Price Summary -->
                    <div class="border-t pt-6 mb-8">
                        <h4 class="text-lg font-semibold mb-4">Ringkasan Pembayaran</h4>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Harga per Jam</span>
                                <span class="font-medium">Rp {{ number_format($booking->field->price_per_hour, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="flex justify-between text-sm text-gray-500">
                                <span>Durasi: 1 jam</span>
                            </div>

                            <div class="border-t pt-2 mt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold">Total Pembayaran</span>
                                    <span class="text-2xl font-bold text-green-600">
                                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="bg-blue-50 p-4 rounded-lg mb-6">
                        <h4 class="font-semibold text-blue-900 mb-2">ℹ️ Informasi Pembayaran</h4>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Anda akan diarahkan ke halaman pembayaran Xendit</li>
                            <li>• Slot akan dikunci selama 30 menit untuk pembayaran</li>
                            <li>• Setelah pembayaran berhasil, booking akan otomatis dikonfirmasi</li>
                            <li>• Jika tidak membayar dalam 30 menit, booking akan dibatalkan</li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4">
                        <a href="{{ route('bookings.my') }}" 
                           class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                            Kembali
                        </a>
                        
                        <form action="{{ route('orders.store', $booking) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                                Lanjut ke Pembayaran
                            </button>
                        </form>
                    </div>

                    @if ($errors->any())
                        <div class="mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
