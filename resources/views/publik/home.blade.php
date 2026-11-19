{{-- [Anggota 2] Landing page Merapi Glamping Tengah Sawah --}}
@extends('layouts.app')
@section('title', 'Beranda')
@section('flush', '1')

@section('content')
{{-- ================= HERO ================= --}}
<section class="relative overflow-hidden">
    <div class="absolute inset-0"><x-landscape variant="dusk" /></div>
    <div class="absolute inset-0 bg-gradient-to-r from-merapi/85 via-merapi/50 to-transparent"></div>

    <div class="relative mx-auto max-w-7xl px-4 pb-28 pt-20 sm:px-6 lg:px-8 lg:pb-36 lg:pt-28">
        <p class="eyebrow !text-harvest-200">Cangkringan · Sleman · Yogyakarta</p>
        <h1 class="mt-4 max-w-2xl font-display text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl">
            Bangun Pagi Disapa Merapi, <span class="italic text-harvest-200">Malam Tidur</span> di Tengah Sawah
        </h1>
        <p class="mt-6 max-w-xl text-base leading-relaxed text-slate-200 sm:text-lg">
            Glamping nyaman berfasilitas lengkap di lereng Merapi — tenda ber-AC, sarapan, kolam renang, pemancingan, dan BBQ di tengah hamparan sawah Argomulyo.
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('booking.create') }}" class="btn-accent px-6 py-3 text-base">Reservasi Sekarang</a>
            <a href="#unit" class="btn border border-white/40 px-6 py-3 text-base text-white hover:bg-white/10">Lihat Unit</a>
        </div>
    </div>
</section>

{{-- Form pencarian cepat --}}
<section class="relative z-10 mx-auto -mt-16 max-w-5xl px-4 sm:px-6">
    <form action="{{ route('booking.create') }}" method="GET" x-data="{ ci: '{{ today()->addDay()->toDateString() }}', co: '{{ today()->addDays(2)->toDateString() }}' }"
          class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-xl sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <label class="label">Tipe unit</label>
            <select name="unit" class="input">
                @foreach ($units as $u)
                    <option value="{{ $u->slug }}">{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Check-in <span class="text-xs font-normal text-slate-400">(14:00)</span></label>
            <input type="date" name="check_in" x-model="ci" min="{{ today()->toDateString() }}" class="input">
        </div>
        <div>
            <label class="label">Check-out <span class="text-xs font-normal text-slate-400">(12:00)</span></label>
            <input type="date" name="check_out" x-model="co" :min="ci" class="input">
        </div>
        <div class="flex items-end">
            <button class="btn-primary w-full py-3">Cek Ketersediaan</button>
        </div>
    </form>
</section>

{{-- Angka singkat --}}
<section class="mx-auto mt-14 grid max-w-5xl grid-cols-3 gap-4 px-4 text-center sm:px-6">
    @foreach ([[$stats['types'], 'Tipe Unit'], [$stats['units'], 'Unit Glamping'], [$stats['guests'].'+', 'Reservasi Tamu']] as [$n, $l])
        <div>
            <p class="font-display text-3xl font-semibold text-paddy sm:text-4xl">{{ $n }}</p>
            <p class="mt-1 text-xs font-medium uppercase tracking-wider text-slate-500 sm:text-sm">{{ $l }}</p>
        </div>
    @endforeach
</section>

{{-- ================= KATALOG UNIT ================= --}}
<section id="unit" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-20 sm:px-6 lg:px-8">
    <div class="max-w-2xl">
        <p class="eyebrow">Pilihan Menginap</p>
        <h2 class="section-title mt-2">Katalog unit glamping</h2>
        <p class="mt-3 text-slate-600">Semua unit sudah termasuk sarapan. Harga per unit per malam.</p>
    </div>

    <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($units as $unit)
            <article class="card group flex flex-col overflow-hidden transition hover:-translate-y-1 hover:shadow-lg">
                <div class="relative h-48 bg-gradient-to-br {{ $unit->themeGradient() }}">
                    @if ($unit->image_url)
                        <img src="{{ $unit->image_url }}" alt="{{ $unit->name }}" class="h-full w-full object-cover">
                    @else
                        <x-landscape :variant="$loop->index === 2 ? 'night' : ($loop->index === 1 ? 'dusk' : 'day')" class="opacity-90" />
                    @endif
                    <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-merapi">{{ $unit->category }}</span>
                </div>
                <div class="flex flex-1 flex-col p-6">
                    <h3 class="font-display text-xl font-semibold">{{ $unit->name }}</h3>
                    <p class="mt-1 text-sm text-slate-500">👥 {{ $unit->capacity }} · {{ $unit->stock_total }} unit</p>
                    <p class="mt-3 line-clamp-2 text-sm text-slate-600">{{ $unit->description }}</p>
                    <ul class="mt-4 flex flex-wrap gap-1.5">
                        @foreach ($unit->facilities ?? [] as $f)
                            <li class="rounded-lg bg-paddy-50 px-2 py-1 text-xs font-medium text-paddy-700">{{ $f }}</li>
                        @endforeach
                    </ul>
                    <div class="mt-auto flex items-end justify-between pt-6">
                        <div>
                            <p class="text-xs text-slate-500">Mulai</p>
                            <p class="text-xl font-bold text-merapi">{{ rupiah($unit->price_per_night) }}<span class="text-sm font-normal text-slate-500">/malam</span></p>
                        </div>
                        <a href="{{ route('booking.create', ['unit' => $unit->slug]) }}" class="btn-primary">Pesan</a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>

{{-- ================= FASILITAS ================= --}}
<section class="bg-paddy-800 py-20 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <p class="eyebrow !text-harvest-200">Fasilitas Bersama</p>
        <h2 class="mt-2 font-display text-3xl font-semibold sm:text-4xl">Lebih dari sekadar menginap</h2>
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['🏊', 'Kolam Renang', 'Berenang santai dengan latar sawah hijau dan udara sejuk lereng Merapi.'],
                ['🎣', 'Pemancingan', 'Kolam pemancingan untuk mengisi sore yang tenang bersama keluarga.'],
                ['🔥', 'Area BBQ', 'Perlengkapan panggang tersedia — nikmati makan malam di bawah bintang.'],
                ['🌄', 'View Sawah & Merapi', 'Matahari terbit di balik Merapi, hamparan sawah di depan tenda.'],
            ] as [$icon, $name, $desc])
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <span class="text-3xl">{{ $icon }}</span>
                    <h3 class="mt-4 font-semibold">{{ $name }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-paddy-100/80">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= ALUR RESERVASI ================= --}}
<section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
    <div class="text-center">
        <p class="eyebrow">Mudah & Cepat</p>
        <h2 class="section-title mt-2">Alur cara reservasi</h2>
    </div>
    <ol class="mt-12 grid gap-6 md:grid-cols-4">
        @foreach ([
            ['Pilih unit & tanggal', 'Tentukan tipe glamping, tanggal check-in/out, dan jumlah unit. Sisa stok tampil otomatis.'],
            ['Isi data & asuransi', 'Lengkapi data kontak. Tambahkan asuransi pembatalan Rp 50.000 bila perlu.'],
            ['Bayar (Sandbox)', 'Klik "Bayar Sekarang (Sandbox)" — status booking langsung terkonfirmasi.'],
            ['Tunjukkan kartu check-in', 'Buka menu Cek Booking, tunjukkan QR Code ke resepsionis pukul 14:00 WIB.'],
        ] as $i => [$step, $desc])
            <li class="relative rounded-2xl border border-slate-200 bg-white p-6">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-harvest font-display text-lg font-semibold text-white">{{ $i + 1 }}</span>
                <h3 class="mt-4 font-semibold">{{ $step }}</h3>
                <p class="mt-2 text-sm text-slate-600">{{ $desc }}</p>
            </li>
        @endforeach
    </ol>
</section>

{{-- ================= ARTIKEL ================= --}}
<section class="bg-canvas-dark py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Tips & Inspirasi</p>
                <h2 class="section-title mt-2">Artikel terbaru</h2>
            </div>
            <a href="{{ route('artikel.index') }}" class="text-sm font-semibold text-paddy hover:underline">Semua artikel →</a>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @foreach ($articles as $article)
                @include('publik.artikel._card', ['article' => $article])
            @endforeach
        </div>
    </div>
</section>

{{-- ================= LOKASI & KONTAK ================= --}}
<section class="mx-auto grid max-w-7xl gap-10 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:px-8">
    <div>
        <p class="eyebrow">Lokasi</p>
        <h2 class="section-title mt-2">Di kaki Merapi, Cangkringan</h2>
        <p class="mt-4 text-slate-600">{{ config('glamping.address') }}</p>
        <dl class="mt-8 grid gap-4 sm:grid-cols-2">
            <div class="card p-5"><dt class="text-xs font-semibold uppercase text-slate-500">Check-in</dt><dd class="mt-1 text-2xl font-bold text-paddy">{{ config('glamping.check_in_time') }} WIB</dd></div>
            <div class="card p-5"><dt class="text-xs font-semibold uppercase text-slate-500">Check-out</dt><dd class="mt-1 text-2xl font-bold text-harvest">{{ config('glamping.check_out_time') }} WIB</dd></div>
            <div class="card p-5"><dt class="text-xs font-semibold uppercase text-slate-500">Telepon / WhatsApp</dt><dd class="mt-1 font-semibold">{{ config('glamping.phone') }}</dd></div>
            <div class="card p-5"><dt class="text-xs font-semibold uppercase text-slate-500">Email</dt><dd class="mt-1 break-all font-semibold">{{ config('glamping.email') }}</dd></div>
        </dl>
    </div>
    <div class="min-h-[320px] overflow-hidden rounded-2xl border border-slate-200 shadow-sm">
        <iframe title="Peta lokasi" class="h-full min-h-[320px] w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                src="https://maps.google.com/maps?q={{ urlencode(config('glamping.maps_query')) }}&z=14&output=embed"></iframe>
    </div>
</section>
@endsection
