<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Order') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Order Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $order->order_number }}</h3>
                            <p class="text-gray-600">Dibuat pada {{ $order->created_at->format('d F Y, H:i') }}</p>
                        </div>
                        <div>
                            @if($order->status === 'paid')
                                <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                    ✓ Dibayar
                                </span>
                            @elseif($order->status === 'pending')
                                <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                    ⏱ Menunggu Pembayaran
                                </span>
                            @elseif($order->status === 'failed')
                                <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                    ✗ Gagal
                                </span>
                            @elseif($order->status === 'expired')
                                <span class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                                    ⌛ Kadaluarsa
                                </span>
                            @else
                                <span class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                                    {{ ucfirst($order->status) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Booking Information -->
                    <div class="border-t pt-6 mb-6">
                        <h4 class="text-lg font-semibold mb-4">Informasi Booking</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600">Lapangan</label>
                                <p class="font-semibold">{{ $order->booking->field->name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-600">Tanggal</label>
                                <p class="font-semibold">{{ $order->booking->booking_date->format('d F Y') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-600">Waktu</label>
                                <p class="font-semibold">
                                    {{ $order->booking->timeSlot->start_time }} - {{ $order->booking->timeSlot->end_time }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-600">Status Booking</label>
                                <p class="font-semibold">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                                        {{ ucfirst($order->booking->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="border-t pt-6 mb-6">
                        <h4 class="text-lg font-semibold mb-4">Ringkasan Pembayaran</h4>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>

                            @if($order->tax > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pajak</span>
                                <span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            @if($order->discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Diskon</span>
                                <span>- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            <div class="border-t pt-2 flex justify-between items-center">
                                <span class="text-lg font-semibold">Total</span>
                                <span class="text-2xl font-bold text-green-600">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Transactions -->
                    @if($order->paymentTransactions->count() > 0)
                    <div class="border-t pt-6 mb-6">
                        <h4 class="text-lg font-semibold mb-4">Riwayat Transaksi</h4>
                        
                        <div class="space-y-3">
                            @foreach($order->paymentTransactions as $transaction)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold">{{ ucfirst($transaction->gateway) }}</p>
                                        <p class="text-sm text-gray-600">{{ $transaction->created_at->format('d F Y, H:i') }}</p>
                                        @if($transaction->gateway_invoice_id)
                                        <p class="text-xs text-gray-500">ID: {{ $transaction->gateway_invoice_id }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                        <span class="text-xs px-2 py-1 rounded 
                                            {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $transaction->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="border-t pt-6">
                        <div class="flex gap-4">
                            <a href="{{ route('orders.index') }}" 
                               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                Kembali ke Daftar Order
                            </a>

                            @if($order->status === 'pending')
                            <a href="{{ route('orders.create', $order->booking) }}" 
                               class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                Bayar Sekarang
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
