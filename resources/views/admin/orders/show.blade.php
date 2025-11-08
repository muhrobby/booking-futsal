                <div class="p-6">
                    <!-- Order Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold">{{ $order->order_number }}</h3>
                            <p class="text-gray-600">Created: {{ $order->created_at->format('d F Y, H:i:s') }}</p>
                        </div>
                        <div>
                            <span class="px-4 py-2 rounded-full text-sm font-semibold
                                {{ $order->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $order->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $order->status === 'expired' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Customer Information -->
                        <div class="border rounded-lg p-4">
                            <h4 class="font-semibold text-lg mb-4">Customer Information</h4>
                            <div class="space-y-2">
                                <div>
                                    <label class="text-sm text-gray-600">Name</label>
                                    <p class="font-semibold">{{ $order->user->name }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Email</label>
                                    <p class="font-semibold">{{ $order->user->email }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Phone</label>
                                    <p class="font-semibold">{{ $order->booking->customer_phone }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Information -->
                        <div class="border rounded-lg p-4">
                            <h4 class="font-semibold text-lg mb-4">Booking Information</h4>
                            <div class="space-y-2">
                                <div>
                                    <label class="text-sm text-gray-600">Field</label>
                                    <p class="font-semibold">{{ $order->booking->field->name }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Date</label>
                                    <p class="font-semibold">{{ $order->booking->booking_date->format('d F Y') }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Time</label>
                                    <p class="font-semibold">
                                        {{ $order->booking->timeSlot->start_time }} - {{ $order->booking->timeSlot->end_time }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Booking Status</label>
                                    <p>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                                            {{ ucfirst($order->booking->status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="border rounded-lg p-4 mt-6">
                        <h4 class="font-semibold text-lg mb-4">Payment Summary</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($order->tax > 0)
                            <div class="flex justify-between">
                                <span>Tax</span>
                                <span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @if($order->discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span>- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="border-t pt-2 flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span class="text-green-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                            @if($order->paid_at)
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Paid At</span>
                                <span>{{ $order->paid_at->format('d F Y, H:i:s') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Transactions -->
                    @if($order->paymentTransactions->count() > 0)
                    <div class="border rounded-lg p-4 mt-6">
                        <h4 class="font-semibold text-lg mb-4">Payment Transactions</h4>
                        <div class="space-y-3">
                            @foreach($order->paymentTransactions as $transaction)
                            <div class="bg-gray-50 p-3 rounded">
                                <div class="flex justify-between">
                                    <div>
                                        <p class="font-semibold">{{ ucfirst($transaction->gateway) }}</p>
                                        <p class="text-sm text-gray-600">{{ $transaction->created_at->format('d F Y, H:i:s') }}</p>
                                        @if($transaction->gateway_invoice_id)
                                        <p class="text-xs text-gray-500">Invoice ID: {{ $transaction->gateway_invoice_id }}</p>
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

                    <!-- Booking Lock -->
                    @if($order->bookingLock)
                    <div class="border rounded-lg p-4 mt-6">
                        <h4 class="font-semibold text-lg mb-4">Booking Lock</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <label class="text-gray-600">Status</label>
                                <p class="font-semibold">{{ $order->bookingLock->is_active ? 'Active' : 'Released' }}</p>
                            </div>
                            <div>
                                <label class="text-gray-600">Reason</label>
                                <p class="font-semibold">{{ $order->bookingLock->reason }}</p>
                            </div>
                            <div>
                                <label class="text-gray-600">Expires At</label>
                                <p class="font-semibold">{{ $order->bookingLock->expires_at->format('d F Y, H:i:s') }}</p>
                            </div>
                            @if($order->bookingLock->released_at)
                            <div>
                                <label class="text-gray-600">Released At</label>
                                <p class="font-semibold">{{ $order->bookingLock->released_at->format('d F Y, H:i:s') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Admin Actions -->
                    <div class="border rounded-lg p-4 mt-6">
                        <h4 class="font-semibold text-lg mb-4">Admin Actions</h4>
                        
                        <!-- Update Status -->
                        <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="mb-4">
                            @csrf
                            @method('PATCH')
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Update Status</label>
                                    <select name="status" class="w-full rounded-md border-gray-300">
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="failed" {{ $order->status === 'failed' ? 'selected' : '' }}>Failed</option>
                                        <option value="expired" {{ $order->status === 'expired' ? 'selected' : '' }}>Expired</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium mb-1">Notes</label>
                                    <input type="text" name="notes" value="{{ $order->notes }}" 
                                           class="w-full rounded-md border-gray-300" placeholder="Optional notes...">
                                </div>
                            </div>
                            <button type="submit" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Update Status
                            </button>
                        </form>

                        <!-- Refund -->
                        @if($order->status === 'paid')
                        <form method="POST" action="{{ route('admin.orders.refund', $order) }}" 
                              onsubmit="return confirm('Are you sure you want to refund this order?')">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Refund Amount</label>
                                    <input type="number" name="amount" value="{{ $order->total }}" 
                                           max="{{ $order->total }}" class="w-full rounded-md border-gray-300">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium mb-1">Refund Reason *</label>
                                    <input type="text" name="reason" required
                                           class="w-full rounded-md border-gray-300" placeholder="Reason for refund...">
                                </div>
                            </div>
                            <button type="submit" class="mt-3 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                Process Refund
                            </button>
                        </form>
                        @endif
                    </div>

                    <!-- Back Button -->
                    <div class="mt-6">
                        <a href="{{ route('admin.orders.index') }}" 
                           class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Back to Orders List
                        </a>
                    </div>

                    @if(session('success'))
                    <div class="mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
