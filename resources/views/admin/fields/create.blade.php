@extends('layouts.admin')

@section('title', 'Tambah Lapangan Baru')

@section('content')
    <div class="max-w-2xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Tambah Lapangan Baru</h1>
            <p class="text-gray-600 mt-2">Isikan informasi lapangan futsal yang akan ditambahkan</p>
        </div>

        <!-- Form Card -->
        <x-card>
            <form method="POST" action="{{ route('admin.fields.store') }}" class="space-y-6">
                @csrf

                <!-- Nama Lapangan -->
                <x-form.input 
                    name="name" 
                    label="Nama Lapangan"
                    placeholder="Contoh: Lapangan A - Indoor"
                    :value="old('name')"
                    :required="true"
                />

                <!-- Lokasi -->
                <x-form.input 
                    name="location" 
                    label="Lokasi"
                    placeholder="Contoh: Jl. Raya Sudirman No. 123"
                    :value="old('location')"
                />

                <!-- Deskripsi -->
                <x-form.textarea 
                    name="description" 
                    label="Deskripsi"
                    placeholder="Deskripsi lengkap tentang lapangan..."
                    :value="old('description')"
                    rows="4"
                />

                <!-- Harga per Jam -->
                <x-form.input 
                    name="price_per_hour" 
                    type="number"
                    label="Harga per Jam (Rp)"
                    placeholder="Contoh: 150000"
                    :value="old('price_per_hour', 0)"
                    :required="true"
                />

                <!-- Status Active -->
                <x-form.checkbox 
                    name="is_active" 
                    label="Lapangan Aktif"
                    :checked="old('is_active', true) ? true : false"
                    value="1"
                />

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.fields.index') }}" class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium transition">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-medium transition">
                        Simpan Lapangan
                    </button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
