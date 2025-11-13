@extends('layouts.admin')

@section('title', 'Detail Order')

@section('content')
    <div class="p-6">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-700">
                ← Kembali ke Daftar Orders
            </a>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <!-- Order Header -->
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $order->order_number }}</h3>
                        <p class="text-gray-600">Dibuat: {{ $order->created_at->locale('id')->format('d F Y, H:i:s') }}</p>
                    </div>
                    <div>
                        @if($order->status === 'paid')
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                Lunas
                            </span>
                        @elseif($order->status === 'processing')
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                Proses
                            </span>
                        @elseif($order->status === 'pending')
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @elseif($order->status === 'failed')
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                Gagal
                            </span>
                        @elseif($order->status === 'expired')
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">
                                Kadaluarsa
                            </span>
                        @else
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">
                                {{ ucfirst($order->status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer Information -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold text-lg mb-4 text-gray-900">Informasi Customer</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm text-gray-600">Nama</label>
                                <p class="font-semibold text-gray-900">{{ $order->user->name }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Email</label>
                                <p class="font-semibold text-gray-900">{{ $order->user->email }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Telepon</label>
                                <p class="font-semibold text-gray-900">{{ $order->booking->customer_phone }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Information -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold text-lg mb-4 text-gray-900">Informasi Booking</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm text-gray-600">Lapangan</label>
                                <p class="font-semibold text-gray-900">{{ $order->booking->field->name }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Tanggal</label>
                                <p class="font-semibold text-gray-900">{{ $order->booking->booking_date->locale('id')->format('d F Y') }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Waktu</label>
                                <p class="font-semibold text-gray-900">
                                    {{ $order->booking->timeSlot->label }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Status Booking</label>
                                <p>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm font-semibold">
                                        {{ ucfirst($order->booking->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="border border-gray-200 rounded-lg p-4 mt-6">
                    <h4 class="font-semibold text-lg mb-4 text-gray-900">Ringkasan Pembayaran</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($order->tax > 0)
                        <div class="flex justify-between text-gray-700">
                            <span>Pajak</span>
                            <span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if($order->discount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Diskon</span>
                            <span>- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="border-t border-gray-200 pt-2 flex justify-between text-lg font-bold text-gray-900">
                            <span>Total</span>
                            <span class="text-green-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                        @if($order->paid_at)
                        <div class="flex justify-between text-sm text-gray-600 pt-2">
                            <span>Dibayar Pada</span>
                            <span>{{ $order->paid_at->locale('id')->format('d F Y, H:i:s') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Transactions -->
                @if($order->paymentTransactions->count() > 0)
                <div class="border border-gray-200 rounded-lg p-4 mt-6">
                    <h4 class="font-semibold text-lg mb-4 text-gray-900">Transaksi Pembayaran</h4>
                    <div class="space-y-3">
                        @foreach($order->paymentTransactions as $transaction)
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="flex justify-between">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ ucfirst($transaction->gateway) }}</p>
                                    <p class="text-sm text-gray-600">{{ $transaction->created_at->locale('id')->format('d F Y, H:i:s') }}</p>
                                    @if($transaction->gateway_invoice_id)
                                    <p class="text-xs text-gray-500 mt-1">Invoice ID: {{ $transaction->gateway_invoice_id }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                    @if($transaction->status === 'completed')
                                        <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-800 font-semibold">
                                            Selesai
                                        </span>
                                    @elseif($transaction->status === 'pending')
                                        <span class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-800 font-semibold">
                                            Pending
                                        </span>
                                    @elseif($transaction->status === 'failed')
                                        <span class="text-xs px-2 py-1 rounded bg-red-100 text-red-800 font-semibold">
                                            Gagal
                                        </span>
                                    @else
                                        <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-800 font-semibold">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Booking Locks -->
                @if($order->booking->bookingLocks->count() > 0)
                <div class="border border-gray-200 rounded-lg p-4 mt-6">
                    <h4 class="font-semibold text-lg mb-4 text-gray-900">Booking Lock History</h4>
                    <div class="space-y-3">
                        @foreach($order->booking->bookingLocks as $lock)
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <label class="text-gray-600">Status</label>
                                    <p class="font-semibold text-gray-900">
                                        @if($lock->is_active)
                                            <span class="text-green-600">● Active</span>
                                        @else
                                            <span class="text-gray-500">○ Released</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="text-gray-600">Alasan</label>
                                    <p class="font-semibold text-gray-900">{{ $lock->reason }}</p>
                                </div>
                                <div>
                                    <label class="text-gray-600">Kadaluarsa Pada</label>
                                    <p class="font-semibold text-gray-900">{{ $lock->expires_at->locale('id')->format('d F Y, H:i') }}</p>
                                </div>
                                @if($lock->released_at)
                                <div>
                                    <label class="text-gray-600">Released Pada</label>
                                    <p class="font-semibold text-gray-900">{{ $lock->released_at->locale('id')->format('d F Y, H:i') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Admin Actions -->
                <div class="border border-gray-200 rounded-lg p-4 mt-6">
                    <h4 class="font-semibold text-lg mb-4 text-gray-900">Admin Actions</h4>
                    
                    <!-- Update Status -->
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="mb-4">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1 text-gray-700">Update Status</label>
                                <select name="status" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="failed" {{ $order->status === 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="expired" {{ $order->status === 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-1 text-gray-700">Catatan Admin</label>
                                <input type="text" name="admin_notes" value="{{ $order->admin_notes }}" 
                                       class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                                       placeholder="Catatan opsional...">
                            </div>
                        </div>
                        <button type="submit" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Update Status
                        </button>
                    </form>

                    <!-- Refund -->
                    @if($order->status === 'paid')
                    <div class="border-t border-gray-200 pt-4">
                        <form method="POST" action="{{ route('admin.orders.refund', $order) }}" 
                              onsubmit="return confirm('Apakah Anda yakin ingin refund order ini?')">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1 text-gray-700">Jumlah Refund</label>
                                    <input type="number" name="amount" value="{{ $order->total }}" 
                                           max="{{ $order->total }}" 
                                           class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium mb-1 text-gray-700">Alasan Refund *</label>
                                    <input type="text" name="reason" required
                                           class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                                           placeholder="Alasan refund...">
                                </div>
                            </div>
                            <button type="submit" class="mt-3 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                Proses Refund
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                <!-- Back Button -->
                <div class="mt-6">
                    <a href="{{ route('admin.orders.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Daftar Orders
                    </a>
                </div>

                <!-- Notifications -->
                @if(session('success'))
                <div class="mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    <p class="font-semibold">✓ {{ session('success') }}</p>
                </div>
                @endif

                @if(session('error'))
                <div class="mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <p class="font-semibold">✗ {{ session('error') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
