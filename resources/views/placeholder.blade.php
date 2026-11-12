{{-- [Anggota 1] Halaman sementara untuk fitur yang belum di-push anggota lain.
     Dipakai oleh route sementara di tahap 1. Boleh dihapus Anggota 4 di akhir. --}}
@extends(($panel ?? false) ? 'layouts.panel' : 'layouts.app')
@section('title', $fitur ?? 'Segera hadir')

@section('content')
<div class="px-4">
    <div class="mx-auto max-w-xl rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
        <p class="eyebrow">Segera hadir</p>
        <h1 class="mt-2 font-display text-3xl font-semibold">{{ $fitur ?? 'Fitur ini' }}</h1>
        <p class="mt-3 text-slate-600">Bagian ini sedang dikerjakan oleh <b>Anggota {{ $anggota ?? '?' }}</b>.</p>
    </div>
</div>
@endsection
