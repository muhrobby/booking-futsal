<!-- Admin Top Navbar -->
<header class="bg-white border-b border-gray-200 sticky top-0 z-40">
    <div class="px-6 py-4 flex items-center justify-between">
        <!-- Left side - Title/Breadcrumb area -->
        <div class="flex-1">
            @isset($title)
                <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
            @endisset
            
            @isset($breadcrumb)
                <nav class="flex items-center space-x-2 text-sm mt-2">
                    @foreach($breadcrumb as $item)
                        <a href="{{ $item['url'] }}" class="text-blue-600 hover:text-blue-700 hover:underline">
                            {{ $item['label'] }}
                        </a>
                        @if(!$loop->last)
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    @endforeach
                </nav>
            @endisset
        </div>

        <!-- Right side - User menu & actions -->
        <div class="flex items-center space-x-6 ml-6">
            <!-- Search Bar (Optional) -->
            <div class="hidden lg:flex items-center bg-gray-100 rounded-lg px-3 py-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" placeholder="Cari..." class="bg-gray-100 border-0 px-2 py-1 text-sm text-gray-700 placeholder-gray-500 focus:outline-none" />
            </div>

            <!-- Notifications -->
            <div class="relative group">
                <button class="relative p-2 text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute top-1 right-1 w-3 h-3 bg-red-500 rounded-full"></span>
                </button>

                <!-- Notifications Dropdown -->
                <div class="hidden group-hover:block absolute right-0 w-80 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-40">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <p class="text-sm font-semibold text-gray-900">Notifikasi</p>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition">
                            <p class="text-sm font-medium text-gray-900">Booking Baru</p>
                            <p class="text-xs text-gray-500 mt-1">Booking ID #1234 - Lapangan A</p>
                            <p class="text-xs text-gray-400 mt-1">5 menit yang lalu</p>
                        </a>
                        <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition">
                            <p class="text-sm font-medium text-gray-900">Pembayaran Diterima</p>
                            <p class="text-xs text-gray-500 mt-1">User: John Doe - Rp 150.000</p>
                            <p class="text-xs text-gray-400 mt-1">15 menit yang lalu</p>
                        </a>
                        <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition">
                            <p class="text-sm font-medium text-gray-900">Lapangan Maintenance</p>
                            <p class="text-xs text-gray-500 mt-1">Lapangan C - Maintenance Started</p>
                            <p class="text-xs text-gray-400 mt-1">1 jam yang lalu</p>
                        </a>
                    </div>
                    <div class="px-4 py-3 border-t border-gray-200 text-center">
                        <a href="#" class="text-xs font-medium text-blue-600 hover:text-blue-700">
                            Lihat Semua Notifikasi
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="relative group">
                <button class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 transition">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="text-sm font-medium hidden sm:inline">{{ auth()->user()->name }}</span>
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div class="hidden group-hover:block absolute right-0 w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-40">
                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                        Profile
                    </a>
                    <div class="border-t border-gray-200 my-2"></div>
                    <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                        Kembali ke Member
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
