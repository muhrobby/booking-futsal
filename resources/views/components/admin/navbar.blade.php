<!-- Admin Top Navbar -->
<header class="bg-white border-b border-gray-200 sticky top-0 z-40">
    <div class="px-3 sm:px-6 py-3 sm:py-4 flex items-center justify-between gap-3 sm:gap-4">
        <!-- Left side -->
        <div class="flex-1 min-w-0">
            @isset($title)
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ $title }}</h1>
            @endisset
        </div>

        <!-- Right side -->
        <div class="flex items-center space-x-2 sm:space-x-4 ml-2 sm:ml-6">
            <!-- Notifications -->
            <div class="relative group hidden sm:block">
                <button class="relative p-2 text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                
                <!-- Notifications Dropdown -->
                <div class="hidden group-hover:block absolute right-0 w-72 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-40">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <p class="text-xs sm:text-sm font-semibold text-gray-900">Notifikasi</p>
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        <a href="#" class="block px-3 sm:px-4 py-2 sm:py-3 hover:bg-gray-50 border-b border-gray-100 transition">
                            <p class="text-xs sm:text-sm font-medium text-gray-900">Booking Baru</p>
                            <p class="text-xs text-gray-500 mt-0.5">Booking ID #1234</p>
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="relative group">
                <button class="flex items-center space-x-1 sm:space-x-2 text-gray-700 hover:text-blue-600 transition">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs sm:text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="text-xs sm:text-sm font-medium hidden sm:inline truncate max-w-24">{{ auth()->user()->name }}</span>
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 hidden sm:inline" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div class="hidden group-hover:block absolute right-0 w-40 sm:w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-40">
                    <a href="{{ route('profile') }}" class="block px-3 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 hover:bg-gray-100 transition">
                        Profile
                    </a>
                    <div class="border-t border-gray-200 my-2"></div>
                    <a href="{{ route('home') }}" class="block px-3 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 hover:bg-gray-100 transition">
                        Kembali ke Member
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 sm:px-4 py-2 text-xs sm:text-sm text-red-600 hover:bg-red-50 transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
