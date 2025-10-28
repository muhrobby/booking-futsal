<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Futsal Neo S') }} - @yield('title', 'Admin Panel')</title>
    <x-favicon />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <x-admin.sidebar />

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navbar -->
            <x-admin.navbar />

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <!-- Breadcrumb -->
                    @if (isset($breadcrumbs))
                        <x-admin.breadcrumb :items="$breadcrumbs" class="mb-6" />
                    @endif

                    <!-- Status/Alert Messages -->
                    @if (session('success'))
                        <x-alert type="success" :message="session('success')" class="mb-6" />
                    @endif

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
                </div>
            </main>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>
