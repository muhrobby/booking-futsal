@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <!-- Hero Section -->
    <section class="mb-12 sm:mb-16">
        <div class="relative overflow-hidden rounded-xl sm:rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-12 sm:px-6 sm:py-16 lg:px-12 lg:py-24 text-white shadow-lg">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <svg class="h-full w-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse">
                            <path d="M 20 0 L 0 0 0 20" fill="none" stroke="white" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#grid)" />
                </svg>
            </div>

            <div class="relative z-10">
                <div class="mx-auto max-w-3xl">
                    <p class="mb-2 sm:mb-4 text-xs sm:text-sm font-semibold uppercase tracking-widest text-blue-100">Selamat Datang di Futsal Neo S</p>
                    <h1 class="mb-3 sm:mb-4 text-2xl sm:text-4xl lg:text-5xl xl:text-6xl font-extrabold leading-tight">Booking Lapangan Futsal Jadi Mudah</h1>
                    <p class="mb-6 sm:mb-8 text-sm sm:text-lg text-blue-100">Temukan lapangan terbaik, pesan slot waktu, dan nikmati permainan futsal tanpa ribet. Semua dalam satu aplikasi.</p>
                    
                    <div class="flex flex-col gap-3 sm:gap-4">
                        <x-button variant="primary" 
                            onclick="window.location.href='{{ route('schedule.index') }}'" 
                            size="lg"
                            class="!bg-white !text-blue-600 hover:!bg-gray-100 w-full sm:w-auto">
                            Mulai Booking Sekarang
                        </x-button>
                        <x-button variant="outline" 
                            onclick="window.location.href='#features'" 
                            size="lg"
                            class="!border-white !text-white hover:!bg-white/10 w-full sm:w-auto">
                            Pelajari Lebih Lanjut
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="mb-12 sm:mb-16">
        <div class="text-center mb-8 sm:mb-12">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2 sm:mb-4">Mengapa Pilih Futsal Neo S?</h2>
            <p class="text-gray-600 text-sm sm:text-lg max-w-2xl mx-auto px-2">Platform booking futsal terpercaya dengan fitur lengkap untuk kemudahan Anda.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
            <!-- Feature 1 -->
            <x-card class="hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Cepat & Mudah</h3>
                <p class="text-gray-600 text-sm sm:text-base">Booking lapangan dalam hitungan detik. Interface yang intuitif membuat siapa saja bisa menggunakan.</p>
            </x-card>

            <!-- Feature 2 -->
            <x-card class="hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 bg-emerald-100 rounded-lg mb-4">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Aman Terpercaya</h3>
                <p class="text-gray-600 text-sm sm:text-base">Data Anda dilindungi dengan enkripsi terkini. Transaksi pembayaran 100% aman dan terverifikasi.</p>
            </x-card>

            <!-- Feature 3 -->
            <x-card class="hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 bg-orange-100 rounded-lg mb-4">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Harga Terjangkau</h3>
                <p class="text-gray-600 text-sm sm:text-base">Harga kompetitif dengan berbagai pilihan lapangan. Promo dan diskon khusus untuk member setia.</p>
            </x-card>
        </div>
    </section>

    <!-- Popular Fields Section -->
    <section id="fields" class="mb-12 sm:mb-16">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0 mb-6 sm:mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Lapangan Populer</h2>
                <p class="text-gray-600 text-sm sm:text-base mt-1 sm:mt-2">Pilih dari berbagai lapangan berkualitas di sekitar Anda</p>
            </div>
            <a href="{{ route('schedule.index') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm hidden md:block">
                Lihat Semua →
            </a>
        </div>

        @if ($fields->isEmpty())
            <x-card class="text-center py-8 sm:py-12">
                <svg class="w-10 sm:w-12 h-10 sm:h-12 text-gray-400 mx-auto mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
                <p class="text-gray-500 text-base sm:text-lg font-medium">Belum ada lapangan yang tersedia saat ini</p>
                <p class="text-gray-400 text-sm sm:text-base mt-1 sm:mt-2">Coba kembali lagi nanti atau hubungi kami untuk informasi lebih lanjut</p>
            </x-card>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach ($fields->take(6) as $field)
                    <x-card class="flex flex-col hover:shadow-lg transition-all hover:-translate-y-1">
                        <!-- Header -->
                        <div class="pb-4 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900">{{ $field->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                {{ $field->location ?? 'Lokasi tidak tersedia' }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div class="py-4 flex-1">
                            <p class="text-gray-600 text-sm">{{ Str::limit($field->description, 100) }}</p>
                        </div>

                        <!-- Price -->
                        <div class="pb-4 border-b border-gray-200">
                            <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500 mt-1">per jam</p>
                        </div>

                        <!-- CTA -->
                        <div class="pt-4">
                            <a href="{{ route('bookings.create', ['field_id' => $field->id]) }}" class="inline-block w-full">
                                <x-button variant="primary" class="w-full">
                                    Booking Sekarang
                                </x-button>
                            </a>
                        </div>
                    </x-card>
                @endforeach
            </div>

            <!-- View All Button -->
            <div class="text-center mt-8 sm:mt-10 md:hidden">
                <a href="{{ route('schedule.index') }}" class="inline-block">
                    <x-button variant="outline" size="lg">
                        Lihat Semua Lapangan
                    </x-button>
                </a>
            </div>
        @endif
    </section>

    <!-- CTA Section -->
    <section class="relative overflow-hidden rounded-xl sm:rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 py-12 sm:px-6 sm:py-16 lg:px-12 lg:py-20 text-white">
        <div class="relative z-10 text-center">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 sm:mb-4">Siap Booking Lapangan Impian?</h2>
            <p class="text-emerald-100 mb-6 sm:mb-8 max-w-2xl mx-auto text-sm sm:text-lg">Bergabunglah dengan ribuan pengguna yang telah merasakan kemudahan booking di Futsal Neo S.</p>
            
            <div class="flex flex-col gap-3 sm:gap-4">
                @auth
                    <x-button variant="primary" 
                        onclick="window.location.href='{{ route('schedule.index') }}'" 
                        size="lg"
                        class="!bg-white !text-emerald-600 hover:!bg-gray-100 w-full sm:w-auto sm:mx-auto sm:block">
                        Mulai Booking
                    </x-button>
                @else
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                        <x-button variant="primary" 
                            onclick="window.location.href='{{ route('login') }}'" 
                            size="lg"
                            class="!bg-white !text-emerald-600 hover:!bg-gray-100 w-full sm:w-auto">
                            Login Sekarang
                        </x-button>
                        <x-button variant="outline" 
                            onclick="window.location.href='{{ route('register') }}'" 
                            size="lg"
                            class="!border-white !text-white hover:!bg-white/10 w-full sm:w-auto">
                            Daftar Gratis
                        </x-button>
                    </div>
                @endauth
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="mt-12 sm:mt-16 grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8">
        <div class="text-center p-4 sm:p-6">
            <p class="text-3xl sm:text-4xl font-bold text-blue-600">{{ $fields->count() }}+</p>
            <p class="text-gray-600 text-sm sm:text-base mt-2">Lapangan Tersedia</p>
        </div>
        <div class="text-center p-4 sm:p-6">
            <p class="text-3xl sm:text-4xl font-bold text-emerald-600">{{ \App\Models\Booking::count() }}+</p>
            <p class="text-gray-600 text-sm sm:text-base mt-2">Booking Berhasil</p>
        </div>
        <div class="text-center p-4 sm:p-6">
            <p class="text-3xl sm:text-4xl font-bold text-orange-600">{{ \App\Models\User::count() }}+</p>
            <p class="text-gray-600 text-sm sm:text-base mt-2">Pengguna Aktif</p>
        </div>
    </section>
@endsection
