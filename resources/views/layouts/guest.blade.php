<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Futsal Neo S') }}</title>
    <x-favicon />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 font-sans antialiased min-h-screen">
    <div class="min-h-screen flex items-center justify-center px-3 sm:px-4 py-6 sm:py-10">
        <div class="w-full max-w-md">
            <!-- Logo/Branding -->
            <div class="text-center mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-blue-600 mb-1">Futsal Neo S</h1>
                <p class="text-gray-600 text-xs sm:text-sm">Platform Booking Lapangan Futsal Terbaik</p>
            </div>

            <!-- Card -->
            <div class="overflow-hidden rounded-xl sm:rounded-2xl bg-white shadow-xl border border-blue-100">
                <!-- Header -->
                <div class="px-4 sm:px-8 pt-6 sm:pt-8 pb-4 sm:pb-6 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                    <p class="text-xs sm:text-sm font-semibold uppercase tracking-widest text-blue-100">Selamat Datang</p>
                    <h2 class="text-xl sm:text-2xl font-bold mt-2">Masuk ke Akun Anda</h2>
                </div>

                <!-- Content -->
                <div class="px-4 sm:px-8 py-6 sm:py-8">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-4 sm:mt-6">
                <p class="text-xs sm:text-sm text-gray-600">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-semibold transition">Daftar di sini</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
