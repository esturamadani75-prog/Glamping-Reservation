{{-- [Anggota 3] Tab navigasi area akun user --}}
<nav class="mt-6 flex gap-1 overflow-x-auto rounded-xl bg-canvas-dark p-1 text-sm font-semibold">
    @foreach ([['Riwayat Sewa', 'user.dashboard'], ['Profil', 'user.profile.edit'], ['Statistik', 'statistik']] as [$label, $route])
        <a href="{{ route($route) }}" class="whitespace-nowrap rounded-lg px-4 py-2 {{ request()->routeIs($route) ? 'bg-white text-paddy shadow-sm' : 'text-slate-600 hover:text-merapi' }}">{{ $label }}</a>
    @endforeach
</nav>
