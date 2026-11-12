{{-- [Anggota 1] Logo Merapi Glamping --}}
@props(['light' => false])
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <svg viewBox="0 0 40 40" class="h-9 w-9 shrink-0" aria-hidden="true">
        <rect width="40" height="40" rx="11" fill="{{ $light ? '#FFFFFF' : '#2D6A4F' }}"/>
        <path d="M6 30 L17 12 L22 19 L25 15 L34 30 Z" fill="{{ $light ? '#2D6A4F' : '#FAF9F5' }}"/>
        <path d="M6 30 Q20 25 34 30 L34 33 L6 33 Z" fill="#D97706"/>
        <circle cx="29" cy="10" r="3" fill="#D97706"/>
    </svg>
    <span class="leading-tight">
        <span class="block font-display text-lg font-semibold {{ $light ? 'text-white' : 'text-merapi' }}">Merapi Glamping</span>
        <span class="block text-[10px] font-semibold uppercase tracking-[0.25em] {{ $light ? 'text-paddy-100' : 'text-paddy' }}">Tengah Sawah</span>
    </span>
</span>
