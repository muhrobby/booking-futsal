@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-xl font-semibold text-slate-800">Daftar Lapangan</h1>
            @can('access-admin')
                <a href="{{ route('admin.fields.create') }}" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">Tambah Lapangan</a>
            @endcan
        </div>

        @if ($fields->isEmpty())
            <div class="rounded-xl border border-slate-200 bg-white px-6 py-10 text-center text-slate-500 shadow-sm">
                Belum ada data lapangan.
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($fields as $field)
                    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <h2 class="text-lg font-semibold text-slate-800">{{ $field->name }}</h2>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $field->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $field->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                        <p class="mt-3 text-sm text-slate-500">{{ $field->description }}</p>
                        <p class="mt-4 text-base font-semibold text-slate-800">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }} <span class="text-sm font-normal text-slate-500">/ jam</span></p>
                        <a href="{{ route('bookings.create', ['field_id' => $field->id]) }}" class="mt-6 inline-flex w-full items-center justify-center rounded-xl border border-blue-600 px-4 py-2 text-sm font-medium text-blue-600 transition hover:bg-blue-50">Booking</a>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection
