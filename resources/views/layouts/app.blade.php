<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Futsal Booking') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <nav class="bg-slate-900 text-white">
            <div class="mx-auto flex h-16 w-full max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-lg font-semibold tracking-tight">Futsal Booking</a>
                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        <a href="{{ route('fields.index') }}" class="transition hover:text-slate-300">Lapangan</a>
                        <a href="{{ route('schedule.index') }}" class="transition hover:text-slate-300">Jadwal</a>
                        @auth
                            <a href="{{ route('bookings.my') }}" class="transition hover:text-slate-300">Booking Saya</a>
                            @can('access-admin')
                                <a href="{{ route('admin.fields.index') }}" class="transition hover:text-slate-300">Kelola Lapangan</a>
                                <a href="{{ route('admin.bookings.index') }}" class="transition hover:text-slate-300">Kelola Booking</a>
                            @endcan
                        @endauth
                    </div>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    @auth
                        <span class="hidden text-slate-200 sm:inline">Halo, {{ \Illuminate\Support\Str::of(Auth::user()->name)->words(2, '') }}</span>
                        <a href="{{ route('profile') }}" class="rounded-md border border-slate-700 px-3 py-1.5 text-slate-100 transition hover:bg-slate-800">Profil</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-md bg-slate-100 px-3 py-1.5 font-medium text-slate-900 transition hover:bg-slate-200">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md border border-slate-700 px-3 py-1.5 transition hover:bg-slate-800">Login</a>
                        <a href="{{ route('register') }}" class="rounded-md bg-white px-3 py-1.5 font-medium text-slate-900 transition hover:bg-slate-200">Register</a>
                    @endauth
                </div>
            </div>
        </nav>

        @auth
            <div class="hidden" aria-hidden="true">
                <livewire:layout.navigation />
            </div>
        @endauth

        <main class="flex-1 py-6">
            <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')

                {{-- Render slot hanya jika layout dipakai oleh komponen yang mengirimkan slot resmi. --}}
                @if (isset($slot) && $slot instanceof Illuminate\View\ComponentSlot)
                    {{ $slot }}
                @endif
            </div>
        </main>

        <footer class="bg-slate-900 py-6 text-center text-xs text-slate-400">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                &copy; {{ now()->year }} Futsal Booking. All rights reserved.
            </div>
        </footer>
    </div>
</body>
</html>
