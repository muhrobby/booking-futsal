@extends('layouts.app')

@section('content')
    <section class="hero-banner rounded-3xl px-6 py-24 text-white shadow-xl sm:px-12">
        <div class="mx-auto flex max-w-4xl flex-col items-start gap-6 text-left">
            <div>
                {{-- <p class="text-sm font-semibold uppercase tracking-[0.35em] text-white/80">Mardha Futsal</p> --}}
                <h1 class="mt-4 text-4xl font-extrabold leading-tight sm:text-5xl">Booking Lapangan Futsal Jadi Lebih Mudah!</h1>
            </div>
            <p class="text-lg text-white/90 sm:text-xl">Pilih Lapangan, Atur Jadwal, Main Tanpa Ribettt.</p>
            <a href="{{ route('schedule.index') }}" class="inline-flex items-center justify-center rounded-full border border-white/70 px-8 py-3 text-sm font-semibold uppercase tracking-[0.35em] text-white transition hover:bg-white/15">Booking Now</a>
        </div>
    </section>

    {{-- <section class="mt-14 space-y-6">
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
    </section> --}}
@endsection
