# 🛠️ Implementation Guide - Step by Step

## Quick Start Checklist

Panduan lengkap untuk mengimplementasikan refactor UI/UX dari awal hingga selesai.

---

## PHASE 1: Persiapan & Setup (Week 1)

### Step 1.1: Update Tailwind Config

**File**: `tailwind.config.js`

Tambahkan custom colors, fonts, dan utilities:

```javascript
import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/resources/views/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: "#f0f9ff",
                    100: "#e0f2fe",
                    200: "#bae6fd",
                    300: "#7dd3fc",
                    400: "#38bdf8",
                    500: "#0ea5e9",
                    600: "#0284c7",
                    700: "#0369a1",
                    800: "#075985",
                    900: "#0c3d66",
                },
                secondary: {
                    50: "#f0fdf4",
                    100: "#dcfce7",
                    200: "#bbf7d0",
                    300: "#86efac",
                    400: "#4ade80",
                    500: "#22c55e",
                    600: "#16a34a",
                    700: "#15803d",
                    800: "#166534",
                    900: "#14532d",
                },
            },
            spacing: {
                128: "32rem",
                144: "36rem",
            },
            animation: {
                "fade-in": "fadeIn 0.3s ease-in",
                "slide-in": "slideIn 0.3s ease-out",
            },
            keyframes: {
                fadeIn: {
                    "0%": { opacity: "0" },
                    "100%": { opacity: "1" },
                },
                slideIn: {
                    "0%": {
                        opacity: "0",
                        transform: "translateY(10px)",
                    },
                    "100%": {
                        opacity: "1",
                        transform: "translateY(0)",
                    },
                },
            },
        },
    },

    plugins: [forms],
};
```

✅ **Checklist**:

-   [ ] Update `tailwind.config.js`
-   [ ] Test dengan `npm run dev`

---

### Step 1.2: Create Base Layouts

**Files to create:**

#### A. Member Layout: `resources/views/layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'FutsalGO') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <!-- Navbar -->
        <x-navbar />

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-4 gap-8">
                    <div>
                        <h4 class="font-bold mb-4">About</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white">About Us</a></li>
                            <li><a href="#" class="hover:text-white">Blog</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4">Product</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white">Features</a></li>
                            <li><a href="#" class="hover:text-white">Pricing</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4">Support</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white">Help Center</a></li>
                            <li><a href="#" class="hover:text-white">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4">Legal</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white">Privacy</a></li>
                            <li><a href="#" class="hover:text-white">Terms</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; 2025 FutsalGO. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
```

#### B. Admin Layout: `resources/views/layouts/admin.blade.php`

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Admin Panel - {{ config('app.name', 'FutsalGO') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <!-- Sidebar -->
        <x-admin.sidebar />

        <!-- Main Content -->
        <div class="ml-64">
            <!-- Top Navbar -->
            <x-admin.navbar />

            <!-- Content -->
            <main class="p-8">
                <!-- Breadcrumb -->
                @if(isset($breadcrumbs))
                    <x-admin.breadcrumb :items="$breadcrumbs" />
                @endif

                @yield('content')
            </main>
        </div>
    </body>
</html>
```

✅ **Checklist**:

-   [ ] Buat `resources/views/layouts/app.blade.php`
-   [ ] Buat `resources/views/layouts/admin.blade.php`
-   [ ] Test routes dengan layouts

---

### Step 1.3: Create Base Components

#### A. Navbar Component: `resources/views/components/navbar.blade.php`

Lihat DESIGN-SYSTEM.md untuk kode lengkap.

#### B. Admin Sidebar: `resources/views/components/admin/sidebar.blade.php`

Lihat DESIGN-SYSTEM.md untuk kode lengkap.

#### C. Button Component: `resources/views/components/button.blade.php`

```blade
<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => 'inline-flex items-center px-4 py-2 rounded-lg font-medium transition duration-200 ' .
        match($variant ?? 'primary') {
            'primary' => 'bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800',
            'secondary' => 'bg-gray-200 text-gray-900 hover:bg-gray-300 active:bg-gray-400',
            'danger' => 'bg-red-600 text-white hover:bg-red-700 active:bg-red-800',
            'success' => 'bg-green-600 text-white hover:bg-green-700 active:bg-green-800',
            default => 'bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-800',
        } .
        ($size === 'sm' ? ' text-sm px-3 py-1' : '') .
        ($size === 'lg' ? ' text-lg px-6 py-3' : '') .
        ($disabled ?? false ? ' opacity-50 cursor-not-allowed' : '')
    ]) }}
>
    {{ $slot }}
</button>
```

#### D. Card Component: `resources/views/components/card.blade.php`

```blade
<div {{ $attributes->merge(['class' => 'bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition']) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">{{ $header }}</h3>
        </div>
    @endif

    @if(isset($body))
        <div class="px-6 py-4">
            {{ $body }}
        </div>
    @else
        <div class="px-6 py-4">
            {{ $slot }}
        </div>
    @endif

    @if(isset($footer))
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $footer }}
        </div>
    @endif
</div>
```

✅ **Checklist**:

-   [ ] Buat semua base components
-   [ ] Test components rendering

---

## PHASE 2: Landing Page Redesign (Week 1-2)

### Step 2.1: Update Home View

**File**: `resources/views/home.blade.php`

```blade
@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="min-h-[600px] bg-gradient-to-br from-blue-600 via-blue-500 to-emerald-400 rounded-3xl px-6 py-24 text-white shadow-2xl mb-20">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Text Content -->
                <div class="space-y-6">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-widest text-blue-100 mb-2">Welcome to FutsalGO</p>
                        <h1 class="text-5xl lg:text-6xl font-extrabold leading-tight">Booking Lapangan Futsal Jadi Lebih Mudah!</h1>
                    </div>
                    <p class="text-xl text-blue-50 leading-relaxed">Pilih Lapangan, Atur Jadwal, Main Tanpa Ribet. Sistem booking futsal terbaik yang pernah ada.</p>

                    <!-- CTA Buttons -->
                    <div class="flex gap-4 pt-4">
                        @auth
                            <a href="{{ route('schedule.index') }}" class="px-8 py-3 bg-white text-blue-600 rounded-full font-bold hover:bg-blue-50 transition shadow-lg">
                                Booking Sekarang
                            </a>
                            <a href="{{ route('bookings.my') }}" class="px-8 py-3 border-2 border-white text-white rounded-full font-bold hover:bg-white/10 transition">
                                My Bookings
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-8 py-3 bg-white text-blue-600 rounded-full font-bold hover:bg-blue-50 transition shadow-lg">
                                Login & Booking
                            </a>
                            <a href="{{ route('register') }}" class="px-8 py-3 border-2 border-white text-white rounded-full font-bold hover:bg-white/10 transition">
                                Daftar Gratis
                            </a>
                        @endauth
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 pt-8">
                        <div>
                            <p class="text-3xl font-bold">50+</p>
                            <p class="text-blue-100 text-sm">Lapangan</p>
                        </div>
                        <div>
                            <p class="text-3xl font-bold">1000+</p>
                            <p class="text-blue-100 text-sm">Bookings</p>
                        </div>
                        <div>
                            <p class="text-3xl font-bold">4.9★</p>
                            <p class="text-blue-100 text-sm">Rating</p>
                        </div>
                    </div>
                </div>

                <!-- Illustration / Image -->
                <div class="hidden lg:flex items-center justify-center">
                    <div class="w-full h-96 bg-white/20 rounded-2xl backdrop-blur-sm flex items-center justify-center">
                        <svg class="w-48 h-48 text-white/40" fill="currentColor" viewBox="0 0 24 24">
                            <!-- Futsal court icon placeholder -->
                            <rect x="2" y="2" width="20" height="20" rx="2" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 px-6 mb-20">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Kenapa Pilih FutsalGO?</h2>
                <p class="text-xl text-gray-600">Platform booking futsal paling lengkap dan terpercaya</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <x-card>
                    <x-slot:body>
                        <div class="text-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Cepat & Mudah</h3>
                            <p class="text-gray-600">Booking dalam hitungan detik, tanpa ribet dan berbelit-belit.</p>
                        </div>
                    </x-slot:body>
                </x-card>

                <!-- Feature 2 -->
                <x-card>
                    <x-slot:body>
                        <div class="text-center">
                            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Harga Terbaik</h3>
                            <p class="text-gray-600">Bandingkan harga lapangan dan pilih yang paling sesuai budget.</p>
                        </div>
                    </x-slot:body>
                </x-card>

                <!-- Feature 3 -->
                <x-card>
                    <x-slot:body>
                        <div class="text-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Terpercaya</h3>
                            <p class="text-gray-600">Lapangan verifikasi dengan rating dan review nyata dari member.</p>
                        </div>
                    </x-slot:body>
                </x-card>
            </div>
        </div>
    </section>

    <!-- Popular Fields Section -->
    <section class="py-16 px-6 mb-20 bg-gray-50">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-12">
                <div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-2">Lapangan Populer</h2>
                    <p class="text-gray-600">Lapangan futsal paling diminati oleh member</p>
                </div>
                <a href="{{ route('fields.index') }}" class="text-blue-600 font-semibold hover:text-blue-700">
                    Lihat Semua →
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($fields as $field)
                    <x-card>
                        <x-slot:body>
                            <div class="aspect-video bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg mb-4 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-13c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $field->name }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ $field->description }}</p>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-2xl font-bold text-gray-900">
                                    Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}
                                    <span class="text-sm font-normal text-gray-500">/jam</span>
                                </span>
                                <span class="text-yellow-400 font-semibold">★ 4.8</span>
                            </div>
                            <a href="{{ route('schedule.index', ['field_id' => $field->id]) }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                                Booking Sekarang
                            </a>
                        </x-slot:body>
                    </x-card>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <p class="text-gray-600">Belum ada lapangan yang tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 px-6 mb-20">
        <div class="max-w-4xl mx-auto bg-gradient-to-r from-blue-600 to-emerald-400 rounded-2xl p-12 text-white text-center">
            <h2 class="text-4xl font-bold mb-4">Siap Bermain Futsal?</h2>
            <p class="text-lg text-blue-50 mb-8">Daftar sekarang dan dapatkan diskon 20% untuk booking pertama Anda!</p>
            @auth
                <a href="{{ route('schedule.index') }}" class="inline-block px-8 py-3 bg-white text-blue-600 rounded-lg font-bold hover:bg-blue-50 transition">
                    Mulai Booking
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-block px-8 py-3 bg-white text-blue-600 rounded-lg font-bold hover:bg-blue-50 transition">
                    Daftar Gratis
                </a>
            @endauth
        </div>
    </section>
@endsection
```

✅ **Checklist**:

-   [ ] Update `resources/views/home.blade.php`
-   [ ] Pastikan fields data tersedia dari HomeController
-   [ ] Test responsive design

---

### Step 2.2: Update HomeController

**File**: `app/Http/Controllers/HomeController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        // Get popular fields (bisa diurutkan by booking count, rating, etc)
        $fields = Field::where('is_active', true)
            ->withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->limit(6)
            ->get();

        return view('home', compact('fields'));
    }
}
```

✅ **Checklist**:

-   [ ] Update HomeController
-   [ ] Test dengan data fields

---

## PHASE 3: Member Dashboard (Week 2-3)

### Step 3.1: Update Dashboard View

**File**: `resources/views/dashboard.blade.php`

```blade
@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-blue-600 to-emerald-400 rounded-2xl p-8 text-white">
            <h1 class="text-4xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-blue-50">Kelola booking futsal Anda dengan mudah di sini</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <x-stats-card
                title="Total Bookings"
                :value="auth()->user()->bookings->count()"
                color="blue"
            />
            <x-stats-card
                title="Upcoming"
                :value="auth()->user()->bookings->where('status', 'confirmed')->where('date', '>=', now())->count()"
                color="emerald"
            />
            <x-stats-card
                title="Completed"
                :value="auth()->user()->bookings->where('status', 'completed')->count()"
                color="purple"
            />
            <x-stats-card
                title="Total Spent"
                value="Rp {{ number_format(auth()->user()->bookings->sum('total_price'), 0, ',', '.') }}"
                color="orange"
            />
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-card>
                <x-slot:body>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Booking Baru</h3>
                        <p class="text-sm text-gray-600 mb-4">Mulai booking lapangan untuk main futsal</p>
                        <a href="{{ route('schedule.index') }}" class="inline-block text-blue-600 font-semibold hover:text-blue-700">
                            Booking Sekarang →
                        </a>
                    </div>
                </x-slot:body>
            </x-card>

            <x-card>
                <x-slot:body>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">My Bookings</h3>
                        <p class="text-sm text-gray-600 mb-4">Lihat semua booking dan riwayat Anda</p>
                        <a href="{{ route('bookings.my') }}" class="inline-block text-emerald-600 font-semibold hover:text-emerald-700">
                            Lihat Semua →
                        </a>
                    </div>
                </x-slot:body>
            </x-card>

            <x-card>
                <x-slot:body>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Profile</h3>
                        <p class="text-sm text-gray-600 mb-4">Kelola profil dan pengaturan akun</p>
                        <a href="{{ route('profile') }}" class="inline-block text-purple-600 font-semibold hover:text-purple-700">
                            Edit Profile →
                        </a>
                    </div>
                </x-slot:body>
            </x-card>
        </div>

        <!-- Recent Bookings -->
        <div>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Booking Terbaru</h2>
                <a href="{{ route('bookings.my') }}" class="text-blue-600 font-semibold hover:text-blue-700">Lihat Semua</a>
            </div>

            @php
                $recentBookings = auth()->user()->bookings()->latest()->limit(3)->get();
            @endphp

            @if($recentBookings->isEmpty())
                <x-card>
                    <x-slot:body>
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Booking</h3>
                            <p class="text-gray-600 mb-6">Mulai booking lapangan futsal sekarang juga!</p>
                            <a href="{{ route('schedule.index') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                                Booking Sekarang
                            </a>
                        </div>
                    </x-slot:body>
                </x-card>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($recentBookings as $booking)
                        @include('bookings.components.booking-card', ['booking' => $booking])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
```

✅ **Checklist**:

-   [ ] Update `resources/views/dashboard.blade.php`
-   [ ] Buat `x-stats-card` component
-   [ ] Test dengan data user

---

## Continue di File Berikutnya...

Document ini menjadi bagian dari 3-file phase guide. Lanjutkan dengan:

1. **IMPLEMENTATION-PHASE-2.md** - Admin Dashboard & Advanced Components
2. **IMPLEMENTATION-PHASE-3.md** - Testing, Deployment & Optimization

**Status**: ✅ Phase 1 Complete  
**Next**: PHASE 2 - Admin Dashboard

---

**Last Updated**: 28 Oktober 2025
