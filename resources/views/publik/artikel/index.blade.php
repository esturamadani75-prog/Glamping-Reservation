{{-- [Anggota 2] Daftar artikel --}}
@extends('layouts.app')
@section('title', 'Artikel')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow">Tips & Inspirasi</p>
            <h1 class="section-title mt-2">Artikel Glamping & Wisata</h1>
        </div>
        <form method="GET" class="flex w-full gap-2 sm:w-auto">
            <input name="q" value="{{ request('q') }}" placeholder="Cari judul artikel…" class="input sm:w-64">
            <button class="btn-primary">Cari</button>
        </form>
    </div>

    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($articles as $article)
            @include('publik.artikel._card')
        @empty
            <p class="col-span-full rounded-2xl bg-white p-10 text-center text-slate-500">Belum ada artikel yang cocok.</p>
        @endforelse
    </div>

    <div class="mt-10">{{ $articles->links() }}</div>
</div>
@endsection
