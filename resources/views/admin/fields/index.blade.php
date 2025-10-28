@extends('layouts.admin')

@section('title', 'Kelola Lapangan')

@section('content')
    <!-- Header Section -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kelola Lapangan</h1>
            <p class="text-gray-600 mt-2">Tambah, ubah, atau hapus informasi lapangan futsal</p>
        </div>
        <a href="{{ route('admin.fields.create') }}">
            <x-button variant="primary" size="lg">
                + Tambah Lapangan Baru
            </x-button>
        </a>
    </div>

    <!-- Fields Table -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Nama Lapangan</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Lokasi</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Harga/Jam</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Dibuat</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($fields as $field)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $field->name }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ Str::limit($field->description, 50) }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $field->location ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($field->is_active)
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Aktif</span>
                            @else
                                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $field->created_at->locale('id')->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.fields.edit', $field) }}" class="px-3 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.fields.destroy', $field) }}" 
                                    onsubmit="return confirm('Yakin ingin menghapus lapangan ini?')" 
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                            <p class="text-gray-600 font-medium mb-2">Belum ada lapangan</p>
                            <p class="text-gray-500 text-sm mb-4">Tambahkan lapangan pertama Anda sekarang</p>
                            <a href="{{ route('admin.fields.create') }}">
                                <x-button variant="primary" size="md">
                                    Tambah Lapangan
                                </x-button>
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($fields->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $fields->links() }}
        </div>
    @endif
@endsection
