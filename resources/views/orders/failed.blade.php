<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pembayaran Gagal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-center">
                    <!-- Error Icon -->
                    <div class="mb-6">
                        <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-3xl font-bold text-gray-900 mb-4">
                        Pembayaran Gagal ❌
                    </h3>

                    <p class="text-gray-600 mb-8">
                        Maaf, pembayaran Anda tidak dapat diproses. Silakan coba lagi.
                    </p>

                    <!-- Order Details -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                        <h4 class="font-semibold text-lg mb-4">Detail Order</h4>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nomor Order</span>
                                <span class="font-semibold">{{ $order->order_number }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Lapangan</span>
                                <span class="font-semibold">{{ $order->booking->field->name }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal</span>
                                <span class="font-semibold">{{ $order->booking->booking_date->format('d F Y') }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Waktu</span>
                                <span class="font-semibold">
                                    {{ $order->booking->timeSlot->start_time }} - {{ $order->booking->timeSlot->end_time }}
                                </span>
                            </div>

                            <div class="border-t pt-3 mt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold">Total</span>
                                    <span class="text-2xl font-bold text-gray-900">
                                        Rp {{ number_format($order->total, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Status</span>
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Retry Information -->
                    <div class="bg-yellow-50 p-4 rounded-lg mb-6 text-left">
                        <h4 class="font-semibold text-yellow-900 mb-2">⚠️ Apa yang harus dilakukan?</h4>
                        <ul class="text-sm text-yellow-800 space-y-1">
                            <li>• Periksa saldo atau limit kartu Anda</li>
                            <li>• Pastikan koneksi internet stabil</li>
                            <li>• Coba gunakan metode pembayaran lain</li>
                            <li>• Hubungi customer service jika masalah berlanjut</li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4">
                        <a href="{{ route('dashboard') }}" 
                           class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center">
                            Kembali ke Dashboard
                        </a>
                        
                        <a href="{{ route('orders.create', $order->booking) }}" 
                           class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-center">
                            Coba Bayar Lagi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
