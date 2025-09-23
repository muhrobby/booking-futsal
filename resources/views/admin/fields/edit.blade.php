@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h1 class="text-xl font-semibold text-slate-800">Edit Lapangan</h1>
            <form method="POST" action="{{ route('admin.fields.update', $field) }}" class="mt-8 space-y-6">
                @csrf
                @method('PUT')
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-600">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $field->name) }}" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
                    @error('name')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-600">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">{{ old('description', $field->description) }}</textarea>
                    @error('description')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-600">Harga per Jam</label>
                    <input type="number" name="price_per_hour" value="{{ old('price_per_hour', $field->price_per_hour) }}" min="0" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
                    @error('price_per_hour')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <label class="inline-flex items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $field->is_active)) class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-slate-600">Aktif</span>
                </label>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('admin.fields.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100">Kembali</a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection
