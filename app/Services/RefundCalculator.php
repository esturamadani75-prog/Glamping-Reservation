<?php

// [Anggota 3] Aturan refund pembatalan
//  - Asuransi = ya  & pembatalan > 3 hari sebelum check-in  -> refund 100% biaya kamar
//  - Asuransi = ya  & pembatalan <= 3 hari sebelum check-in -> refund 50% biaya kamar
//  - Asuransi = tidak                                         -> refund 0%

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Carbon;

class RefundCalculator
{
    /** Selisih hari dari tanggal pengajuan ke tanggal check-in. */
    public static function daysBeforeCheckIn(Booking $booking, ?Carbon $at = null): int
    {
        $at = ($at ?? now())->copy()->startOfDay();

        return (int) $at->diffInDays($booking->check_in_date->copy()->startOfDay(), false);
    }

    public static function percentage(Booking $booking, ?Carbon $at = null): int
    {
        if (! $booking->has_insurance) {
            return 0;
        }

        return self::daysBeforeCheckIn($booking, $at) > 3 ? 100 : 50;
    }

    /** Estimasi lengkap untuk ditampilkan di form & disimpan ke tabel cancellations. */
    public static function estimate(Booking $booking, ?Carbon $at = null): array
    {
        $days = self::daysBeforeCheckIn($booking, $at);
        $paid = $booking->paid_at !== null;
        $pct = $paid ? self::percentage($booking, $at) : 0;

        $rule = match (true) {
            ! $paid => 'Booking belum dibayar, sehingga tidak ada dana yang perlu dikembalikan.',
            ! $booking->has_insurance => 'Tanpa asuransi pembatalan: biaya kamar tidak dapat dikembalikan (refund 0%).',
            $days > 3 => "Berasuransi & dibatalkan {$days} hari sebelum check-in (> 3 hari): refund 100% biaya kamar.",
            default => "Berasuransi & dibatalkan {$days} hari sebelum check-in (≤ 3 hari): refund 50% biaya kamar.",
        };

        return [
            'days' => $days,
            'paid' => $paid,
            'room_cost' => $booking->room_total,
            'percentage' => $pct,
            'amount' => (int) round($booking->room_total * $pct / 100),
            'rule' => $rule,
        ];
    }

    /** Booking boleh diajukan batal jika masih aktif, belum lewat check-in, dan tidak ada pengajuan pending. */
    public static function canCancel(Booking $booking): bool
    {
        return in_array($booking->status, Booking::ACTIVE_STATUSES, true)
            && $booking->check_in_date->gte(today())
            && ! $booking->cancellations()->whereIn('status', ['pending', 'approved'])->exists();
    }
}
