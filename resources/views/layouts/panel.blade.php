{{-- [Anggota 1] Layout panel Staff & Admin (sidebar responsive) --}}
<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    @stack('head')
</head>
<body class="bg-canvas font-sans text-merapi antialiased" x-data="{ sidebar: false }">
@php
    $pendingCount = \App\Models\Cancellation::where('status', 'pending')->count();
    $menu = [
        ['Dashboard', 'staff.dashboard', 'M3 12l9-9 9 9M5 10v10h14V10', null],
        ['Transaksi', 'staff.transaksi.index', 'M4 6h16M4 12h16M4 18h10', null],
        ['Pembatalan', 'staff.pembatalan.index', 'M6 18 18 6M6 6l12 12', $pendingCount],
        ['Unit Glamping', 'staff.unit.index', 'M3 20 12 4l9 16H3Zm9-6v6', null],
        ['Artikel', 'staff.artikel.index', 'M7 4h10l3 3v13H4V4h3m0 6h10M7 14h10', null],
    ];
    $adminMenu = [
        ['Manajemen Akun', 'admin.akun.index', 'M16 14a4 4 0 1 0-8 0m8 0v1H8v-1m8 0a4 4 0 0 1 4 4v2H4v-2a4 4 0 0 1 4-4m4-4a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z'],
        ['Audit Log Staff', 'admin.audit.index', 'M9 5h10M9 12h10M9 19h10M5 5h.01M5 12h.01M5 19h.01'],
    ];
    // menu ".index" aktif juga untuk create/edit/show di grup yang sama
    $isActive = fn ($name) => str_ends_with($name, '.index')
        ? request()->routeIs(\Illuminate\Support\Str::beforeLast($name, '.').'.*')
        : request()->routeIs($name);
@endphp

{{-- overlay mobile --}}
<div x-cloak x-show="sidebar" @click="sidebar = false" class="fixed inset-0 z-30 bg-merapi/50 lg:hidden"></div>

{{-- ================= SIDEBAR ================= --}}
<aside :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-merapi text-slate-300 transition-transform lg:translate-x-0">
    <div class="flex h-16 items-center border-b border-white/10 px-5">
        <a href="{{ route('staff.dashboard') }}"><x-logo light /></a>
    </div>
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5">
        <p class="px-3 pb-2 text-[11px] font-bold uppercase tracking-widest text-slate-500">Operasional</p>
        @foreach ($menu as [$label, $route, $icon, $badge])
            <a href="{{ route($route) }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $isActive($route) ? 'bg-paddy text-white' : 'hover:bg-white/5 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                <span class="flex-1">{{ $label }}</span>
                @if ($badge)
                    <span class="rounded-full bg-harvest px-2 py-0.5 text-xs font-bold text-white">{{ $badge }}</span>
                @endif
            </a>
        @endforeach

        @if (auth()->user()->isAdmin())
            <p class="px-3 pb-2 pt-6 text-[11px] font-bold uppercase tracking-widest text-slate-500">Khusus Admin</p>
            @foreach ($adminMenu as [$label, $route, $icon])
                <a href="{{ route($route) }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $isActive($route) ? 'bg-harvest text-white' : 'hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                    {{ $label }}
                </a>
            @endforeach
        @endif
    </nav>
    <div class="border-t border-white/10 p-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2 rounded-lg px-2 py-2 text-sm hover:text-white">↗ Lihat Situs Publik</a>
    </div>
</aside>

{{-- ================= KONTEN ================= --}}
<div class="lg:pl-64">
    <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200/70 bg-canvas/90 px-4 backdrop-blur sm:px-6">
        <div class="flex items-center gap-3">
            <button @click="sidebar = true" class="rounded-lg p-2 lg:hidden" aria-label="Buka menu">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
            <h1 class="font-display text-xl font-semibold">@yield('title', 'Panel')</h1>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden text-right sm:block">
                <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                <p class="text-xs uppercase tracking-wider {{ auth()->user()->isAdmin() ? 'text-harvest' : 'text-paddy' }}">{{ auth()->user()->role }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="btn-outline btn-sm">Keluar</button>
            </form>
        </div>
    </header>

    <main class="min-w-0 px-4 py-6 sm:px-6 lg:px-8">
        @include('partials.flash', ['inner' => true])
        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
