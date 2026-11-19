{{-- [Anggota 2] Detail artikel (konten Markdown dirender aman) --}}
@extends('layouts.app')
@section('title', $article->title)

@section('content')
<article class="mx-auto max-w-3xl px-4 sm:px-6">
    <a href="{{ route('artikel.index') }}" class="text-sm font-semibold text-paddy hover:underline">← Semua artikel</a>
    <p class="eyebrow mt-6">{{ $article->category }}</p>
    <h1 class="mt-2 font-display text-3xl font-semibold leading-tight sm:text-4xl">{{ $article->title }}</h1>
    <p class="mt-3 text-sm text-slate-500">
        {{ $article->author->name ?? 'Tim Merapi Glamping' }} · {{ tanggal($article->published_at, 'd F Y') }} · {{ $article->readingTime() }} menit baca
    </p>

    <div class="mt-8 h-64 overflow-hidden rounded-2xl sm:h-80">
        @if ($article->cover_image)
            <img src="{{ $article->cover_image }}" alt="{{ $article->title }}" class="h-full w-full object-cover">
        @else
            <x-landscape :variant="['day', 'dusk', 'night'][$article->id % 3]" />
        @endif
    </div>

    <div class="prose prose-slate mt-10 max-w-none prose-headings:font-display prose-a:text-paddy prose-strong:text-merapi">
        {!! \Illuminate\Support\Str::markdown($article->content, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
    </div>

    <div class="mt-12 rounded-2xl bg-paddy p-8 text-white">
        <h2 class="font-display text-2xl font-semibold">Siap merasakan sendiri?</h2>
        <p class="mt-2 text-paddy-100">Pesan unit glamping favoritmu sekarang — sisa stok tampil langsung.</p>
        <a href="{{ route('booking.create') }}" class="btn-accent mt-5">Reservasi Sekarang</a>
    </div>
</article>

@if ($related->isNotEmpty())
    <section class="mx-auto mt-16 max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="font-display text-2xl font-semibold">Artikel lainnya</h2>
        <div class="mt-6 grid gap-6 md:grid-cols-3">
            @foreach ($related as $article)
                @include('publik.artikel._card')
            @endforeach
        </div>
    </section>
@endif
@endsection
