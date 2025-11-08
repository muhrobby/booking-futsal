<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Order Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($orders->count() > 0)
                        <div class="space-y-4">
                            @foreach($orders as $order)
                            <div class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-semibold text-lg">{{ $order->order_number }}</h3>
                                        <p class="text-sm text-gray-600">{{ $order->created_at->format('d F Y, H:i') }}</p>
                                    </div>
                                    <div>
                                        @if($order->status === 'paid')
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                                ✓ Dibayar
                                            </span>
                                        @elseif($order->status === 'pending')
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                                ⏱ Menunggu
                                            </span>
                                        @elseif($order->status === 'failed')
                                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                                ✗ Gagal
                                            </span>
                                        @elseif($order->status === 'expired')
                                            <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                                                ⌛ Kadaluarsa
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                    <div>
                                        <label class="block text-xs text-gray-600">Lapangan</label>
                                        <p class="font-semibold">{{ $order->booking->field->name }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-xs text-gray-600">Tanggal & Waktu</label>
                                        <p class="font-semibold">{{ $order->booking->booking_date->format('d M Y') }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ $order->booking->timeSlot->start_time }} - {{ $order->booking->timeSlot->end_time }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-xs text-gray-600">Total</label>
                                        <p class="font-bold text-lg text-green-600">
                                            Rp {{ number_format($order->total, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <a href="{{ route('orders.show', $order) }}" 
                                       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm">
                                        Lihat Detail
                                    </a>

                                    @if($order->status === 'pending')
                                    <a href="{{ route('orders.create', $order->booking) }}" 
                                       class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm">
                                        Bayar Sekarang
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum ada order</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai booking lapangan untuk membuat order.</p>
                            <div class="mt-6">
                                <a href="{{ route('fields.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    Lihat Lapangan
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
