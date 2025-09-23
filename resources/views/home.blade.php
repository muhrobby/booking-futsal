@extends('layouts.app')

@section('content')
    <section class="hero-gradient rounded-3xl px-6 py-16 text-center shadow-lg sm:px-10">
        <div class="mx-auto max-w-3xl space-y-6">
            <h1 class="text-3xl font-semibold leading-tight sm:text-4xl">Booking Lapangan Futsal Jadi Mudah</h1>
            <p class="text-base text-slate-100/90 sm:text-lg">Pilih lapangan favorit, cek ketersediaan jadwal, dan lakukan reservasi hanya dengan beberapa klik.</p>
            <a href="{{ route('schedule.index') }}" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 font-semibold text-blue-600 shadow-md transition hover:shadow-lg">Cari Jadwal</a>
        </div>
    </section>

    <section class="mt-10 space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-700">Lapangan Populer</h2>
        </div>
        @if ($fields->isEmpty())
            <div class="rounded-xl border border-slate-200 bg-white px-6 py-10 text-center text-slate-500 shadow-sm">
                Belum ada lapangan yang tersedia saat ini.
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($fields as $field)
                    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <h3 class="text-lg font-semibold text-slate-800">{{ $field->name }}</h3>
                        <p class="mt-2 text-sm text-slate-500">{{ $field->description }}</p>
                        <p class="mt-4 text-base font-semibold text-slate-800">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }} <span class="text-sm font-normal text-slate-500">/ jam</span></p>
                        <a href="{{ route('bookings.create', ['field_id' => $field->id]) }}" class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">Booking Sekarang</a>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
