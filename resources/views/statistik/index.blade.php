{{-- [Anggota 3] Statistik publik & user (Chart.js) --}}
@extends('layouts.app')
@section('title', 'Statistik')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <p class="eyebrow">Transparansi Data</p>
    <h1 class="section-title mt-2">Statistik penyewaan glamping</h1>
    <p class="mt-2 max-w-2xl text-slate-600">Data diperbarui otomatis dari sistem reservasi (12 bulan terakhir hingga 30 hari ke depan).</p>
    @auth @if (auth()->user()->role === 'user') @include('user._nav') @endif @endauth

    {{-- Angka ringkasan --}}
    <div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ([
            ['Total reservasi', number_format($summary['total'], 0, ',', '.'), 'dalam rentang data'],
            ['Booking aktif', $summary['active'], 'pending + terkonfirmasi'],
            ['Tingkat pembatalan', $summary['cancel_rate'].'%', 'dari seluruh reservasi'],
            ['Okupansi rata-rata', $summary['occupancy'].'%', '30 hari terakhir'],
        ] as [$label, $value, $hint])
            <div class="card p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-3xl font-bold text-merapi">{{ $value }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
            </div>
        @endforeach
    </div>

    {{-- 1. Jumlah penyewaan per periode --}}
    <section class="card mt-6 p-6" x-data="{ period: 'bulanan' }" x-init="$watch('period', p => window.updateRentals(p))">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold">Jumlah penyewaan per periode</h2>
                <p class="text-xs text-slate-500">Berdasarkan tanggal check-in, tidak termasuk booking dibatalkan</p>
            </div>
            <div class="flex rounded-lg bg-canvas-dark p-1 text-xs font-semibold">
                @foreach (['harian' => '30 Hari', 'mingguan' => '12 Minggu', 'bulanan' => '12 Bulan'] as $key => $label)
                    <button type="button" @click="period = '{{ $key }}'" class="rounded-md px-3 py-1.5" :class="period === '{{ $key }}' ? 'bg-white text-paddy shadow-sm' : 'text-slate-600'">{{ $label }}</button>
                @endforeach
            </div>
        </div>
        <div class="relative mt-4 h-72"><canvas id="rentalChart" aria-label="Grafik jumlah penyewaan per periode"></canvas></div>
        <details class="mt-3 text-sm">
            <summary class="cursor-pointer text-xs font-semibold text-slate-500">Lihat tabel (bulanan)</summary>
            <div class="mt-2 overflow-x-auto"><table class="min-w-full text-xs"><tr>@foreach ($rentals['bulanan']['labels'] as $l)<th class="px-2 py-1 text-left font-medium text-slate-500">{{ $l }}</th>@endforeach</tr>
            <tr>@foreach ($rentals['bulanan']['data'] as $v)<td class="px-2 py-1">{{ $v }}</td>@endforeach</tr></table></div>
        </details>
    </section>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        {{-- 2. Tren pembatalan --}}
        <section class="card p-6">
            <h2 class="font-semibold">Tren pengajuan pembatalan</h2>
            <p class="text-xs text-slate-500">Jumlah pengajuan per bulan menurut hasil keputusan staff</p>
            <div class="relative mt-4 h-64"><canvas id="cancelChart" aria-label="Grafik tren pembatalan"></canvas></div>
            <h3 class="mt-6 text-sm font-semibold">Persentase booking dibatalkan</h3>
            <div class="relative mt-2 h-36"><canvas id="cancelRateChart" aria-label="Grafik persentase pembatalan"></canvas></div>
            <details class="mt-3 text-sm">
                <summary class="cursor-pointer text-xs font-semibold text-slate-500">Lihat tabel</summary>
                <table class="mt-2 min-w-full text-xs">
                    <tr class="text-left text-slate-500"><th class="py-1">Bulan</th><th>Disetujui</th><th>Ditolak</th><th>Menunggu</th><th>% Batal</th></tr>
                    @foreach ($cancellations['labels'] as $i => $l)
                        <tr><td class="py-1">{{ $l }}</td><td>{{ $cancellations['approved'][$i] }}</td><td>{{ $cancellations['rejected'][$i] }}</td><td>{{ $cancellations['pending'][$i] }}</td><td>{{ $cancellations['rate'][$i] }}%</td></tr>
                    @endforeach
                </table>
            </details>
        </section>

        {{-- 3. Pemakaian stok --}}
        <section class="card p-6">
            <h2 class="font-semibold">Tingkat pemakaian stok per tipe glamping</h2>
            <p class="text-xs text-slate-500">Okupansi = unit-malam terisi ÷ (stok × 30 malam)</p>
            <div class="relative mt-4 h-64"><canvas id="usageChart" aria-label="Grafik okupansi per tipe"></canvas></div>
            <h3 class="mt-6 text-sm font-semibold">Pemakaian unit hari ini</h3>
            <div class="relative mt-2 h-36"><canvas id="todayChart" aria-label="Grafik pemakaian unit hari ini"></canvas></div>
            <details class="mt-3 text-sm">
                <summary class="cursor-pointer text-xs font-semibold text-slate-500">Lihat tabel</summary>
                <table class="mt-2 min-w-full text-xs">
                    <tr class="text-left text-slate-500"><th class="py-1">Tipe</th><th>30 hr lalu</th><th>30 hr ke depan</th><th>Terpakai hari ini</th><th>Tersedia</th></tr>
                    @foreach ($usage['labels'] as $i => $l)
                        <tr><td class="py-1">{{ $l }}</td><td>{{ $usage['past30'][$i] }}%</td><td>{{ $usage['next30'][$i] }}%</td><td>{{ $usage['todayUsed'][$i] }}</td><td>{{ $usage['todayFree'][$i] }}</td></tr>
                    @endforeach
                </table>
            </details>
        </section>
    </div>

    {{-- Statistik pribadi (user login) --}}
    @if ($personal)
        <section class="card mt-6 grid gap-6 p-6 md:grid-cols-3">
            <div>
                <p class="eyebrow">Statistik Saya</p>
                <p class="mt-3 text-4xl font-bold text-paddy">{{ $personal['total'] }}</p>
                <p class="text-sm text-slate-500">total reservasi</p>
                <p class="mt-4 text-4xl font-bold text-harvest">{{ $personal['nights'] }}</p>
                <p class="text-sm text-slate-500">malam menginap (terkonfirmasi/selesai)</p>
            </div>
            <div class="md:col-span-2">
                <div class="relative h-56"><canvas id="personalChart" aria-label="Status booking saya"></canvas></div>
            </div>
        </section>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    // Palet kategorikal tervalidasi (urutan tetap, aman buta warna)
    const C = { green: '#059669', blue: '#2563EB', amber: '#D97706', neutral: '#CBD5E1', paddy: '#2D6A4F' };
    Chart.defaults.font.family = '"Plus Jakarta Sans", sans-serif';
    Chart.defaults.color = '#64748B';
    Chart.defaults.borderColor = '#E2E8F0';
    const barStyle = { borderRadius: 4, borderSkipped: 'start', maxBarThickness: 32, borderColor: '#FFFFFF', borderWidth: { top: 0, right: 1, bottom: 0, left: 1 } };
    const noGridX = { x: { grid: { display: false } } };

    // 1. Penyewaan per periode
    const rentals = @js($rentals);
    const rentalChart = new Chart(document.getElementById('rentalChart'), {
        type: 'line',
        data: { labels: rentals.bulanan.labels, datasets: [{ label: 'Penyewaan', data: rentals.bulanan.data, borderColor: C.paddy, backgroundColor: 'rgba(45,106,79,.10)', fill: true, tension: .35, borderWidth: 2, pointRadius: 4, pointHoverRadius: 6, pointBackgroundColor: C.paddy }] },
        options: { maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { display: false } }, scales: { ...noGridX, y: { beginAtZero: true, ticks: { precision: 0 } } } },
    });
    window.updateRentals = (p) => {
        rentalChart.data.labels = rentals[p].labels;
        rentalChart.data.datasets[0].data = rentals[p].data;
        rentalChart.update();
    };

    // 2. Tren pembatalan (stacked bar) + persentase (grafik terpisah, tanpa dual-axis)
    const cancel = @js($cancellations);
    new Chart(document.getElementById('cancelChart'), {
        type: 'bar',
        data: { labels: cancel.labels, datasets: [
            { label: 'Disetujui', data: cancel.approved, backgroundColor: C.green, ...barStyle },
            { label: 'Ditolak', data: cancel.rejected, backgroundColor: C.blue, ...barStyle },
            { label: 'Menunggu', data: cancel.pending, backgroundColor: C.amber, ...barStyle },
        ] },
        options: { maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10 } } },
            scales: { x: { stacked: true, grid: { display: false } }, y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } } } },
    });
    new Chart(document.getElementById('cancelRateChart'), {
        type: 'line',
        data: { labels: cancel.labels, datasets: [{ label: '% dibatalkan', data: cancel.rate, borderColor: C.amber, borderWidth: 2, pointRadius: 4, tension: .3 }] },
        options: { maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ` ${c.parsed.y}% booking dibatalkan` } } },
            scales: { ...noGridX, y: { beginAtZero: true, ticks: { callback: v => v + '%' } } } },
    });

    // 3. Pemakaian stok
    const usage = @js($usage);
    new Chart(document.getElementById('usageChart'), {
        type: 'bar',
        data: { labels: usage.labels, datasets: [
            { label: '30 hari terakhir', data: usage.past30, backgroundColor: C.green, ...barStyle },
            { label: '30 hari ke depan', data: usage.next30, backgroundColor: C.blue, ...barStyle },
        ] },
        options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10 } }, tooltip: { callbacks: { label: c => ` ${c.dataset.label}: ${c.parsed.y}%` } } },
            scales: { ...noGridX, y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } } } },
    });
    new Chart(document.getElementById('todayChart'), {
        type: 'bar',
        data: { labels: usage.labels, datasets: [
            { label: 'Terpakai', data: usage.todayUsed, backgroundColor: C.green, ...barStyle },
            { label: 'Tersedia', data: usage.todayFree, backgroundColor: C.neutral, ...barStyle },
        ] },
        options: { indexAxis: 'y', maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10 } } },
            scales: { x: { stacked: true, beginAtZero: true, ticks: { precision: 0 } }, y: { stacked: true, grid: { display: false } } } },
    });

    @if ($personal)
    // Statistik pribadi
    const personal = @js($personal['byStatus']);
    new Chart(document.getElementById('personalChart'), {
        type: 'bar',
        data: { labels: Object.keys(personal), datasets: [{ label: 'Booking saya', data: Object.values(personal), backgroundColor: C.paddy, ...barStyle }] },
        options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { ...noGridX, y: { beginAtZero: true, ticks: { precision: 0 } } } },
    });
    @endif
</script>
@endpush
