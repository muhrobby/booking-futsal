@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-10">
        <section class="hero-banner rounded-3xl bg-gradient-to-r from-emerald-500 via-cyan-500 to-blue-500 px-6 py-14 text-white shadow-xl sm:px-12">
            <div class="mx-auto max-w-3xl space-y-4 text-center sm:text-left">
                <p class="text-sm font-semibold uppercase tracking-[0.35em] text-white/90">Hubungi Kami</p>
                <h1 class="text-3xl font-extrabold leading-tight sm:text-4xl">Siap Membantu Booking Futsal Anda</h1>
                <p class="text-base text-white/90 sm:text-lg">Tim kami siap menjawab pertanyaan seputar jadwal, ketersediaan lapangan, dan kebutuhan event khusus.</p>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800">Alamat</h2>
                <p class="mt-2 text-sm text-slate-600">Jl. Raya Futsal No. 123, Kota Olahraga, Indonesia</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800">Telepon</h2>
                <p class="mt-2 text-sm text-slate-600">(+62) 812-3456-7890</p>
                <p class="text-sm text-slate-500">Senin - Minggu, 08.00 - 22.00 WIB</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800">Email</h2>
                <p class="mt-2 text-sm text-slate-600">halo@futsalneos.id</p>
                <p class="text-sm text-slate-500">Balasan dalam 1x24 jam kerja.</p>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-lg">
                <h2 class="text-lg font-semibold text-slate-800">Kirim Pesan</h2>
                <p class="mt-2 text-sm text-slate-500">Silakan tinggalkan pesan jika Anda membutuhkan informasi tambahan atau penawaran khusus.</p>
                <form class="mt-6 space-y-4">
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                        <input id="name" type="text" name="name" placeholder="Masukkan nama Anda" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
                    </div>
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                        <input id="email" type="email" name="email" placeholder="nama@email.com" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100">
                    </div>
                    <div class="space-y-2">
                        <label for="message" class="block text-sm font-medium text-slate-700">Pesan</label>
                        <textarea id="message" name="message" rows="4" placeholder="Tuliskan pertanyaan Anda" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-100"></textarea>
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Kirim Pesan</button>
                </form>
            </div>
            <div class="overflow-hidden rounded-3xl shadow-lg">
                <iframe title="Peta Lokasi" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.100929175102!2d112.75208867519467!3d-7.343725472180169!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7fa169404d4cb%3A0x4cfddc38183b6956!2sLapangan%20Futsal!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" width="100%" height="100%" style="border:0; min-height: 360px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </section>
    </div>
@endsection
