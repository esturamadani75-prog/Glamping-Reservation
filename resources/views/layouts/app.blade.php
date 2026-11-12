{{-- [Anggota 1] Layout halaman publik & user (navbar + footer) --}}
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('partials.head')
    @stack('head')
</head>
<body class="flex min-h-screen flex-col bg-canvas font-sans text-merapi antialiased">

{{-- ================= NAVBAR ================= --}}
<header x-data="{ open: false }" class="print:hidden sticky top-0 z-40 border-b border-slate-200/70 bg-canvas/90 backdrop-blur">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}"><x-logo /></a>

        @php
            $links = [
                ['Beranda', route('home'), request()->routeIs('home')],
                ['Unit Glamping', route('home').'#unit', false],
                ['Artikel', route('artikel.index'), request()->routeIs('artikel.*')],
                ['Statistik', route('statistik'), request()->routeIs('statistik')],
                ['Cek Booking', route('booking.check'), request()->routeIs('booking.check*')],
            ];
        @endphp

        <div class="hidden items-center gap-1 lg:flex">
            @foreach ($links as [$label, $url, $active])
                <a href="{{ $url }}" class="rounded-lg px-3 py-2 text-sm font-medium transition {{ $active ? 'bg-paddy-50 text-paddy-700' : 'text-slate-600 hover:text-paddy' }}">{{ $label }}</a>
            @endforeach
        </div>

        <div class="hidden items-center gap-2 lg:flex">
            @auth
                <div x-data="{ menu: false }" class="relative">
                    <button @click="menu = !menu" @click.outside="menu = false" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white py-1.5 pl-1.5 pr-3 text-sm font-medium hover:border-paddy-200">
                        <span class="grid h-7 w-7 place-items-center rounded-lg bg-paddy text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        {{ \Illuminate\Support\Str::words(auth()->user()->name, 1, '') }}
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div x-cloak x-show="menu" x-transition class="absolute right-0 mt-2 w-52 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-lg">
                        @if (auth()->user()->canAccessPanel())
                            <a href="{{ route('staff.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-canvas">Panel {{ ucfirst(auth()->user()->role) }}</a>
                        @else
                            <a href="{{ route('user.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-canvas">Dashboard Saya</a>
                            <a href="{{ route('user.profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-canvas">Profil</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">Keluar</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-outline">Masuk</a>
            @endauth
            <a href="{{ route('booking.create') }}" class="btn-accent">Reservasi</a>
        </div>

        <button @click="open = !open" class="rounded-lg p-2 text-merapi lg:hidden" aria-label="Menu">
            <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/></svg>
            <svg x-cloak x-show="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
    </nav>

    {{-- Menu mobile --}}
    <div x-cloak x-show="open" x-transition class="border-t border-slate-200 bg-canvas px-4 pb-4 lg:hidden">
        @foreach ($links as [$label, $url, $active])
            <a href="{{ $url }}" @click="open = false" class="block rounded-lg px-3 py-2.5 text-sm font-medium {{ $active ? 'bg-paddy-50 text-paddy-700' : 'text-slate-700' }}">{{ $label }}</a>
        @endforeach
        <div class="mt-3 grid grid-cols-2 gap-2">
            @auth
                <a href="{{ auth()->user()->homeRoute() }}" class="btn-outline">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn-outline w-full text-red-600">Keluar</button></form>
            @else
                <a href="{{ route('login') }}" class="btn-outline">Masuk</a>
                <a href="{{ route('register') }}" class="btn-outline">Daftar</a>
            @endauth
            <a href="{{ route('booking.create') }}" class="btn-accent col-span-2">Reservasi Sekarang</a>
        </div>
    </div>
</header>

<main class="flex-1 {{ trim($__env->yieldContent('flush')) ? '' : 'pt-8 pb-16' }}">
    @include('partials.flash')
    @yield('content')
</main>

{{-- ================= FOOTER ================= --}}
<footer class="bg-merapi text-slate-300 print:hidden">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8">
        <div class="lg:col-span-2">
            <x-logo light />
            <p class="mt-4 max-w-md font-display text-lg italic text-paddy-100">“Bangun Pagi Disapa Merapi, Malam Tidur di Tengah Sawah”</p>
            <p class="mt-3 max-w-md text-sm leading-relaxed text-slate-400">Jl. Panggung No. 86, Jaranan, Argomulyo, Kec. Cangkringan, Sleman, DI Yogyakarta 55584</p>
        </div>
        <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider text-white">Jam Operasional</h4>
            <ul class="mt-4 space-y-2 text-sm">
                <li>Check-in &nbsp;<span class="font-semibold text-harvest-200">14:00 WIB</span></li>
                <li>Check-out <span class="font-semibold text-harvest-200">12:00 WIB</span></li>
            </ul>
        </div>
        <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider text-white">Tautan</h4>
            <ul class="mt-4 space-y-2 text-sm">
                <li><a href="{{ route('booking.create') }}" class="hover:text-white">Reservasi</a></li>
                <li><a href="{{ route('booking.check') }}" class="hover:text-white">Cek Status Booking</a></li>
                <li><a href="{{ route('artikel.index') }}" class="hover:text-white">Artikel & Tips</a></li>
                <li><a href="{{ route('statistik') }}" class="hover:text-white">Statistik</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10 py-5 text-center text-xs text-slate-500">
        © {{ date('Y') }} Merapi Glamping Tengah Sawah · Proyek UAS Pemrograman Web
    </div>
</footer>

@stack('scripts')
</body>
</html>
