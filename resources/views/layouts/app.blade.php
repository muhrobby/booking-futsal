<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Futsal Neo S') }} - @yield('title', 'Member')</title>
    <x-favicon />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Navbar Component -->
        <x-navbar />

        <!-- Main Content -->
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Status/Alert Messages -->
            @if (session('status'))
                <x-alert type="success" :message="session('status')" class="mb-6" />
            @endif

            @if (session('error'))
                <x-alert type="error" :message="session('error')" class="mb-6" />
            @endif

            @if ($errors->any())
                <x-alert type="error" :message="'Terjadi kesalahan. Silakan periksa kembali data Anda.'" class="mb-6" />
            @endif

            @yield('content')

            {{-- Render slot untuk Component-based layouts --}}
            @if (isset($slot) && $slot instanceof Illuminate\View\ComponentSlot)
                {{ $slot }}
            @endif
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white mt-16 border-t border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <h4 class="font-bold text-lg mb-4 text-gray-100">Tentang</h4>
                        <ul class="space-y-2 text-gray-400 text-sm">
                            <li><a href="#" class="hover:text-white transition">Tentang Kami</a></li>
                            <li><a href="#" class="hover:text-white transition">Blog</a></li>
                            <li><a href="#" class="hover:text-white transition">Karir</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg mb-4 text-gray-100">Produk</h4>
                        <ul class="space-y-2 text-gray-400 text-sm">
                            <li><a href="#" class="hover:text-white transition">Fitur</a></li>
                            <li><a href="#" class="hover:text-white transition">Harga</a></li>
                            <li><a href="#" class="hover:text-white transition">Unduh</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg mb-4 text-gray-100">Dukungan</h4>
                        <ul class="space-y-2 text-gray-400 text-sm">
                            <li><a href="#" class="hover:text-white transition">Pusat Bantuan</a></li>
                            <li><a href="{{ route('contact') }}" class="hover:text-white transition">Hubungi Kami</a></li>
                            <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg mb-4 text-gray-100">Legal</h4>
                        <ul class="space-y-2 text-gray-400 text-sm">
                            <li><a href="#" class="hover:text-white transition">Kebijakan Privasi</a></li>
                            <li><a href="#" class="hover:text-white transition">Syarat Layanan</a></li>
                            <li><a href="#" class="hover:text-white transition">Kebijakan Cookie</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-8 text-center text-gray-400 text-sm">
                    <p>&copy; {{ now()->year }} Futsal Neo S. Semua hak dilindungi.</p>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
