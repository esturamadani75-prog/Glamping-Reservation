<?php

// [Anggota 3] Penyusun data grafik Chart.js
// Dihitung di PHP (bukan fungsi tanggal SQL) agar jalan di SQLite maupun MySQL.

namespace App\Services;

use App\Models\Booking;
use App\Models\Cancellation;
use App\Models\GlampingUnit;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class StatisticsService
{
    private Collection $bookings;

    public function __construct()
    {
        // data 12 bulan terakhir s/d 30 hari ke depan
        $this->bookings = Booking::query()
            ->whereDate('check_in_date', '>=', today()->subMonths(12)->startOfMonth())
            ->whereDate('check_in_date', '<=', today()->addDays(30))
            ->get(['id', 'glamping_unit_id', 'check_in_date', 'check_out_date', 'unit_qty', 'status', 'total_price']);
    }

    /** 1. Jumlah penyewaan per periode (harian / mingguan / bulanan) berdasarkan tanggal check-in. */
    public function rentalsPerPeriod(): array
    {
        $valid = $this->bookings->where('status', '!=', 'cancelled');

        $build = function (array $buckets, callable $keyOf) use ($valid) {
            $counts = array_fill_keys(array_keys($buckets), 0);
            foreach ($valid as $b) {
                $k = $keyOf($b->check_in_date);
                if (isset($counts[$k])) {
                    $counts[$k]++;
                }
            }

            return ['labels' => array_values($buckets), 'data' => array_values($counts)];
        };

        $daily = [];
        foreach (CarbonPeriod::create(today()->subDays(29), today()) as $d) {
            $daily[$d->toDateString()] = $d->locale('id')->translatedFormat('d M');
        }
        $weekly = [];
        for ($i = 11; $i >= 0; $i--) {
            $start = today()->startOfWeek()->subWeeks($i);
            $weekly[$start->toDateString()] = 'Mg '.$start->locale('id')->translatedFormat('d M');
        }
        $monthly = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = today()->startOfMonth()->subMonths($i);
            $monthly[$m->format('Y-m')] = $m->locale('id')->translatedFormat('M Y');
        }

        return [
            'harian' => $build($daily, fn (Carbon $d) => $d->toDateString()),
            'mingguan' => $build($weekly, fn (Carbon $d) => $d->copy()->startOfWeek()->toDateString()),
            'bulanan' => $build($monthly, fn (Carbon $d) => $d->format('Y-m')),
        ];
    }

    /** 2. Tren pembatalan per bulan (6 bulan) + persentase pembatalan. */
    public function cancellationTrend(int $months = 6): array
    {
        $cancellations = Cancellation::where('created_at', '>=', today()->subMonths($months - 1)->startOfMonth())
            ->get(['status', 'created_at']);

        $labels = $approved = $rejected = $pending = $rate = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $m = today()->startOfMonth()->subMonths($i);
            $key = $m->format('Y-m');
            $labels[] = $m->locale('id')->translatedFormat('M Y');

            $inMonth = $cancellations->filter(fn ($c) => $c->created_at->format('Y-m') === $key);
            $approved[] = $inMonth->where('status', 'approved')->count();
            $rejected[] = $inMonth->where('status', 'rejected')->count();
            $pending[] = $inMonth->where('status', 'pending')->count();

            $monthBookings = $this->bookings->filter(fn ($b) => $b->check_in_date->format('Y-m') === $key);
            $total = $monthBookings->count();
            $rate[] = $total ? round($monthBookings->where('status', 'cancelled')->count() / $total * 100, 1) : 0;
        }

        return compact('labels', 'approved', 'rejected', 'pending', 'rate');
    }

    /** 3. Tingkat pemakaian stok (okupansi %) tiap tipe glamping. */
    public function stockUsage(): array
    {
        $units = GlampingUnit::orderBy('price_per_night')->get();
        $result = ['labels' => [], 'past30' => [], 'next30' => [], 'todayUsed' => [], 'todayFree' => []];

        foreach ($units as $unit) {
            $result['labels'][] = $unit->name;
            $result['past30'][] = $this->occupancy($unit, today()->subDays(30), today());
            $result['next30'][] = $this->occupancy($unit, today(), today()->addDays(30));

            $usedToday = $this->bookings
                ->where('glamping_unit_id', $unit->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->filter(fn ($b) => $b->check_in_date->lte(today()) && $b->check_out_date->gt(today()))
                ->sum('unit_qty');
            $result['todayUsed'][] = (int) $usedToday;
            $result['todayFree'][] = max(0, $unit->stock_total - $usedToday);
        }

        return $result;
    }

    /** Okupansi = unit-malam terjual / (stok × jumlah malam) × 100 */
    private function occupancy(GlampingUnit $unit, Carbon $from, Carbon $to): float
    {
        $nights = $from->diffInDays($to);
        if ($nights <= 0 || $unit->stock_total <= 0) {
            return 0;
        }

        $sold = 0;
        $rows = $this->bookings->where('glamping_unit_id', $unit->id)->whereIn('status', ['pending', 'confirmed', 'completed']);
        foreach ($rows as $b) {
            $start = $b->check_in_date->max($from);
            $end = $b->check_out_date->min($to);
            if ($end->gt($start)) {
                $sold += $start->diffInDays($end) * $b->unit_qty;
            }
        }

        return round(min(100, $sold / ($unit->stock_total * $nights) * 100), 1);
    }

    /** Angka ringkasan di atas grafik. */
    public function summary(): array
    {
        $total = $this->bookings->count();
        $cancelled = $this->bookings->where('status', 'cancelled')->count();
        $usage = $this->stockUsage();

        return [
            'total' => $total,
            'active' => $this->bookings->whereIn('status', ['pending', 'confirmed'])->count(),
            'cancel_rate' => $total ? round($cancelled / $total * 100, 1) : 0,
            'occupancy' => count($usage['past30']) ? round(array_sum($usage['past30']) / count($usage['past30']), 1) : 0,
        ];
    }
}
