@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h1 class="text-xl font-semibold text-slate-800">Form Booking Lapangan</h1>
            <p class="mt-2 text-sm text-slate-500">Lengkapi detail berikut untuk mengajukan pemesanan. Status awal booking adalah <span class="font-semibold text-amber-600">pending</span>.</p>

            <form method="POST" action="{{ route('bookings.store') }}" class="mt-8 space-y-6">
                @csrf
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-600">Lapangan</label>
                    <select name="field_id" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
                        <option value="">-- Pilih Lapangan --</option>
                        @foreach ($fields as $field)
                            <option value="{{ $field->id }}" @selected(old('field_id', $selectedFieldId) == $field->id)>{{ $field->name }}</option>
                        @endforeach
                    </select>
                    @error('field_id')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-600">Tanggal</label>
                        <input type="date" name="booking_date" value="{{ old('booking_date', $selectedDate) }}" min="{{ now()->toDateString() }}" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
                        @error('booking_date')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-600">Slot Jam</label>
                        <select name="time_slot_id" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
                            <option value="">-- Pilih Slot --</option>
                            {{-- Gunakan label accessor dari model supaya format jam konsisten. --}}
                            @foreach ($timeSlots as $timeSlotOption)
                                <option value="{{ $timeSlotOption->id }}" @selected(old('time_slot_id', $selectedSlotId) == $timeSlotOption->id)>{{ $timeSlotOption->label }}</option>
                            @endforeach
                        </select>
                        @error('time_slot_id')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-600">Nama Pemesan</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
                    @error('customer_name')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-600">Nomor HP</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone', auth()->user()->phone) }}" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
                    @error('customer_phone')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-600">Catatan</label>
                    <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <span class="text-sm text-slate-500">Status awal booking adalah <span class="font-semibold text-amber-600">pending</span>.</span>
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Simpan Booking</button>
                </div>
            </form>
        </div>
    </div>
@endsection
