{{-- [Anggota 2] Kartu artikel (dipakai di landing & daftar artikel) --}}
<a href="{{ route('artikel.show', $article) }}" class="card group flex flex-col overflow-hidden transition hover:-translate-y-1 hover:shadow-lg">
    <div class="relative h-44 overflow-hidden bg-paddy-100">
        @if ($article->cover_image)
            <img src="{{ $article->cover_image }}" alt="{{ $article->title }}" class="h-full w-full object-cover transition group-hover:scale-105">
        @else
            <x-landscape :variant="['day', 'dusk', 'night'][$article->id % 3]" class="transition group-hover:scale-105" />
        @endif
        <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-paddy-700">{{ $article->category }}</span>
    </div>
    <div class="flex flex-1 flex-col p-6">
        <p class="text-xs text-slate-500">{{ tanggal($article->published_at) }} · {{ $article->readingTime() }} menit baca</p>
        <h3 class="mt-2 font-display text-lg font-semibold leading-snug group-hover:text-paddy">{{ $article->title }}</h3>
        <p class="mt-2 line-clamp-3 text-sm text-slate-600">{{ $article->summary() }}</p>
        <span class="mt-auto pt-4 text-sm font-semibold text-harvest">Baca selengkapnya →</span>
    </div>
</a>
