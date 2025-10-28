<!-- Admin Sidebar Navigation -->
<aside class="w-64 bg-gray-900 text-gray-100 flex flex-col h-screen overflow-y-auto sticky top-0">
    <!-- Brand -->
    <div class="p-6 border-b border-gray-800">
        <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-white">
            Futsal Neo S Admin
        </a>
        <p class="text-gray-400 text-xs mt-1">v1.0</p>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-lg font-medium transition
           {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-4m0 0l7-4 7 4M5 8v10a1 1 0 001 1h12a1 1 0 001-1V8m-9 4v4m4-4v4" />
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- Fields Management -->
        <div class="py-2">
            <p class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Manajemen</p>
            
            <a href="{{ route('admin.fields.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg font-medium transition
               {{ request()->routeIs('admin.fields.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
                <span>Lapangan</span>
            </a>

            <a href="{{ route('admin.bookings.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg font-medium transition
               {{ request()->routeIs('admin.bookings.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span>Booking</span>
            </a>

            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg font-medium transition
               {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>User</span>
            </a>
        </div>
    </nav>

    <!-- Logout -->
    <div class="border-t border-gray-800 p-4">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="flex items-center space-x-3 w-full px-4 py-3 rounded-lg font-medium text-gray-300 hover:bg-gray-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
