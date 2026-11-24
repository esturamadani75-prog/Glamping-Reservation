{{-- [Anggota 3] Dashboard user: ringkasan, booking terdekat, riwayat sewa --}}
@extends('layouts.app')
@section('title', 'Dashboard Saya')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow">Akun Saya</p>
            <h1 class="section-title mt-2">Halo, {{ \Illuminate\Support\Str::words(auth()->user()->name, 1, '') }} 👋</h1>
        </div>
        <a href="{{ route('booking.create') }}" class="btn-accent">+ Reservasi Baru</a>
    </div>

    @include('user._nav')

    {{-- Ringkasan --}}
    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ([['Total Booking', $summary['total'], 'text-merapi'], ['Aktif', $summary['active'], 'text-harvest'], ['Selesai', $summary['completed'], 'text-sky-700'], ['Total Pengeluaran', rupiah($summary['spent']), 'text-paddy text-xl sm:text-2xl']] as [$label, $value, $color])
            <div class="card p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-bold {{ $color }}">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        {{-- Booking terdekat --}}
        <div class="card overflow-hidden lg:col-span-2">
            @if ($upcoming)
                <div class="grid sm:grid-cols-[180px_1fr]">
                    <div class="h-40 sm:h-full"><x-landscape variant="day" /></div>
                    <div class="p-6">
                        <p class="eyebrow">Perjalanan berikutnya</p>
                        <h2 class="mt-1 font-display text-xl font-semibold">{{ $upcoming->unit->name }}</h2>
                        <p class="mt-1 text-sm text-slate-600">{{ tanggal($upcoming->check_in_date, 'l, d M Y') }} · {{ $upcoming->nights() }} malam · {{ $upcoming->unit_qty }} unit</p>
                        <p class="mt-1 text-sm text-slate-500">Check-in {{ $upcoming->check_in_date->isToday() ? 'hari ini' : $upcoming->check_in_date->locale('id')->diffForHumans() }}</p>
                        <a href="{{ route('booking.show', $upcoming) }}" class="btn-primary btn-sm mt-4">Lihat Kartu Check-in</a>
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-slate-500">Belum ada perjalanan terkonfirmasi. <a href="{{ route('booking.create') }}" class="font-semibold text-paddy">Pesan sekarang</a></div>
            @endif
        </div>

        {{-- Grafik status booking pribadi --}}
        <div class="card p-6">
            <h2 class="text-sm font-semibold">Status booking saya</h2>
            <div class="relative mx-auto mt-4 h-44"><canvas id="myStatusChart"></canvas></div>
        </div>
    </div>

    {{-- Riwayat sewa --}}
    <div class="card mt-6">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-5">
            <h2 class="font-semibold">Riwayat sewa</h2>
            <div class="flex flex-wrap gap-1 text-xs font-semibold">
                <a href="{{ route('user.dashboard') }}" class="rounded-lg px-3 py-1.5 {{ ! request('status') ? 'bg-paddy text-white' : 'bg-canvas-dark text-slate-600' }}">Semua</a>
                @foreach (\App\Models\Booking::STATUSES as $s)
                    <a href="{{ route('user.dashboard', ['status' => $s]) }}" class="rounded-lg px-3 py-1.5 {{ request('status') === $s ? 'bg-paddy text-white' : 'bg-canvas-dark text-slate-600' }}">{{ status_label($s) }}</a>
                @endforeach
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($bookings as $b)
                <div class="grid gap-4 p-5 md:grid-cols-[1.4fr_1fr_1fr_auto] md:items-center">
                    <div>
                        <p class="font-mono text-xs font-bold tracking-wider text-slate-500">{{ $b->booking_code }}</p>
                        <p class="mt-0.5 font-semibold">{{ $b->unit->name }} <span class="font-normal text-slate-500">× {{ $b->unit_qty }}</span></p>
                        <p class="text-sm text-slate-500">{{ tanggal($b->check_in_date) }} → {{ tanggal($b->check_out_date) }} ({{ $b->nights() }} malam)</p>
                    </div>
                    <div class="text-sm">
                        <p class="font-bold text-merapi">{{ rupiah($b->total_price) }}</p>
                        <p class="text-xs text-slate-500">{{ $b->has_insurance ? '🛡 Berasuransi' : 'Tanpa asuransi' }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-status-badge :status="$b->status" />
                        @if ($b->latestCancellation)
                            <span class="inline-flex items-center gap-1 text-xs text-slate-500">Pembatalan: <x-status-badge :status="$b->latestCancellation->status" /></span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2 md:justify-end">
                        @if ($b->status === 'pending')
                            <a href="{{ route('booking.payment', $b) }}" class="btn-accent btn-sm">Bayar</a>
                        @endif
                        <a href="{{ route('booking.show', $b) }}" class="btn-outline btn-sm">Detail</a>
                        @if (\App\Services\RefundCalculator::canCancel($b))
                            <a href="{{ route('user.cancel.create', $b) }}" class="btn-outline btn-sm text-red-600">Ajukan Batal</a>
                        @endif
                    </div>
                    @if ($b->latestCancellation && $b->latestCancellation->status !== 'pending' && $b->latestCancellation->staff_note)
                        <p class="rounded-lg bg-canvas px-3 py-2 text-xs text-slate-600 md:col-span-4">
                            <b>Catatan staff:</b> {{ $b->latestCancellation->staff_note }}
                            @if ($b->latestCancellation->status === 'approved') · Refund {{ rupiah($b->latestCancellation->refund_amount) }} @endif
                        </p>
                    @endif
                </div>
            @empty
                <p class="p-10 text-center text-slate-500">Belum ada riwayat sewa.</p>
            @endforelse
        </div>
        <div class="border-t border-slate-100 p-4">{{ $bookings->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('myStatusChart'), {
        type: 'doughnut',
        data: {
            labels: @js(collect(\App\Models\Booking::STATUSES)->map(fn ($s) => status_label($s))),
            datasets: [{
                data: @js(collect(\App\Models\Booking::STATUSES)->map(fn ($s) => (int) ($statusCounts[$s] ?? 0))),
                backgroundColor: ['#D97706', '#2D6A4F', '#DC2626', '#0284C7'],
                borderWidth: 0,
            }],
        },
        options: { maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'right', labels: { boxWidth: 10, font: { size: 11 } } } } },
    });
</script>
@endpush
