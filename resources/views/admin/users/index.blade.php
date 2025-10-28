@extends('layouts.admin')

@section('title', 'Kelola User')

@section('content')
    <!-- Header Section -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kelola User</h1>
            <p class="text-gray-600 mt-2">Tambah, ubah, atau hapus data pengguna sistem</p>
        </div>
        <a href="{{ route('admin.users.create') }}">
            <x-button variant="primary" size="lg">
                + Tambah User Baru
            </x-button>
        </a>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari User</label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari berdasarkan nama, email, atau telepon..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
            <div class="sm:w-48">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Filter Role</label>
                <select 
                    id="role" 
                    name="role"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Filter
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Nama</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Email</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Telepon</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Role</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Total Booking</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Terdaftar</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $user->phone }}
                        </td>
                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                                <span class="inline-block px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">Admin</span>
                            @else
                                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">User</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span class="font-semibold">{{ $user->bookings()->count() }}</span> booking
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $user->created_at->locale('id')->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="px-3 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    Edit
                                </a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                        onsubmit="return confirm('Yakin ingin menghapus user ini?')" 
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <p class="text-gray-600 font-medium mb-2">Tidak ada user ditemukan</p>
                            <p class="text-gray-500 text-sm mb-4">Coba ubah filter atau tambahkan user baru</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($users->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $users->links() }}
        </div>
    @endif
@endsection
