{{-- [Anggota 1] Ilustrasi SVG Gunung Merapi + terasering sawah (pengganti foto) --}}
@props(['variant' => 'day'])
@php
    $sky = $variant === 'dusk' ? ['#FDEBC8', '#F59E0B'] : ($variant === 'night' ? ['#1E293B', '#334155'] : ['#FFF8EB', '#D5EADF']);
@endphp
<svg {{ $attributes->merge(['class' => 'h-full w-full']) }} viewBox="0 0 800 500" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
    <defs>
        <linearGradient id="sky-{{ $variant }}" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="{{ $sky[0] }}"/>
            <stop offset="1" stop-color="{{ $sky[1] }}"/>
        </linearGradient>
    </defs>
    <rect width="800" height="500" fill="url(#sky-{{ $variant }})"/>
    <circle cx="610" cy="120" r="46" fill="#D97706" opacity="{{ $variant === 'night' ? '0.25' : '0.85' }}"/>
    {{-- Merapi --}}
    <path d="M90 330 L330 110 Q350 96 370 110 L420 150 L470 120 L700 330 Z" fill="#334155"/>
    <path d="M300 140 L330 110 Q350 96 370 110 L395 132 L372 150 L352 136 L330 156 Z" fill="#E2E8F0" opacity=".85"/>
    <path d="M345 92 q8 -30 26 -40 q-4 20 8 30" fill="none" stroke="#CBD5E1" stroke-width="6" stroke-linecap="round" opacity=".6"/>
    {{-- Bukit & sawah berundak --}}
    <path d="M0 330 Q200 270 420 320 T800 300 L800 500 L0 500 Z" fill="#40916C"/>
    <path d="M0 370 Q220 320 440 365 T800 350 L800 500 L0 500 Z" fill="#2D6A4F"/>
    <path d="M0 420 Q240 380 460 418 T800 405 L800 500 L0 500 Z" fill="#24563F"/>
    <g stroke="#A9D3BD" stroke-width="2" opacity=".55" fill="none">
        <path d="M0 395 Q220 352 440 392 T800 378"/>
        <path d="M0 450 Q240 410 460 446 T800 432"/>
    </g>
    {{-- Tenda glamping --}}
    <g transform="translate(520 360)">
        <path d="M0 50 L45 0 L90 50 Z" fill="#FAF9F5"/>
        <path d="M45 0 L58 50 L32 50 Z" fill="#D97706"/>
        <rect x="-6" y="50" width="102" height="5" rx="2" fill="#1B4332"/>
    </g>
    <g transform="translate(150 395) scale(.7)">
        <path d="M0 50 L45 0 L90 50 Z" fill="#FAF9F5"/>
        <path d="M45 0 L58 50 L32 50 Z" fill="#D97706"/>
    </g>
</svg>
