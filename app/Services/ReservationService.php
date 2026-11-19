<?php

// [Anggota 2] Logika inti reservasi: hitung tarif & validasi stok per rentang tanggal

namespace App\Services;

use App\Models\Booking;
use App\Models\GlampingUnit;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    /** Jumlah malam antara check-in dan check-out. */
    public function nights($checkIn, $checkOut): int
    {
        return (int) Carbon::parse($checkIn)->startOfDay()->diffInDays(Carbon::parse($checkOut)->startOfDay());
    }

    /**
     * Rincian biaya.
     * Total Biaya Kamar = price_per_night * unit_qty * jumlah_malam
     * Asuransi pembatalan = Rp 50.000 per booking (opsional)
     */
    public function quote(GlampingUnit $unit, $checkIn, $checkOut, int $qty, bool $insurance): array
    {
        $nights = $this->nights($checkIn, $checkOut);
        $roomTotal = $unit->price_per_night * $qty * $nights;
        $insuranceFee = $insurance ? (int) config('glamping.insurance_fee', Booking::INSURANCE_FEE) : 0;

        return [
            'nights' => $nights,
            'room_total' => $roomTotal,
            'insurance_fee' => $insuranceFee,
            'total_price' => $roomTotal + $insuranceFee,
        ];
    }

    /**
     * Jumlah unit terpakai untuk SETIAP malam dalam rentang [checkIn, checkOut).
     * Hanya booking aktif (pending / confirmed) yang dihitung,
     * sehingga booking yang dibatalkan otomatis mengembalikan stok.
     *
     * @return array<string,int>  ['2026-11-20' => 3, ...]
     */
    public function usagePerNight(GlampingUnit $unit, $checkIn, $checkOut): array
    {
        $bookings = Booking::where('glamping_unit_id', $unit->id)
            ->active()
            ->overlapping(Carbon::parse($checkIn)->toDateString(), Carbon::parse($checkOut)->toDateString())
            ->get(['check_in_date', 'check_out_date', 'unit_qty']);

        $usage = [];
        $period = CarbonPeriod::create(Carbon::parse($checkIn), Carbon::parse($checkOut)->subDay());
        foreach ($period as $night) {
            $usage[$night->toDateString()] = (int) $bookings
                ->filter(fn ($b) => $b->check_in_date->lte($night) && $b->check_out_date->gt($night))
                ->sum('unit_qty');
        }

        return $usage;
    }

    /** Sisa stok = stok total - pemakaian tertinggi pada salah satu malam. */
    public function remainingStock(GlampingUnit $unit, $checkIn, $checkOut): int
    {
        $usage = $this->usagePerNight($unit, $checkIn, $checkOut);

        return max(0, $unit->stock_total - (empty($usage) ? 0 : max($usage)));
    }

    /** Buat booking baru (status pending) dengan penguncian baris agar tidak overbooking. */
    public function createBooking(array $data, ?User $user = null): Booking
    {
        return DB::transaction(function () use ($data, $user) {
            // lockForUpdate: request lain yang memesan unit sama akan menunggu transaksi ini selesai
            $unit = GlampingUnit::whereKey($data['glamping_unit_id'])->lockForUpdate()->firstOrFail();

            $remaining = $this->remainingStock($unit, $data['check_in_date'], $data['check_out_date']);
            if ($data['unit_qty'] > $remaining) {
                throw ValidationException::withMessages([
                    'unit_qty' => $remaining > 0
                        ? "Stok {$unit->name} pada tanggal tersebut hanya tersisa {$remaining} unit."
                        : "{$unit->name} sudah penuh pada tanggal tersebut. Silakan pilih tanggal atau unit lain.",
                ]);
            }

            $quote = $this->quote($unit, $data['check_in_date'], $data['check_out_date'], (int) $data['unit_qty'], (bool) $data['has_insurance']);

            return Booking::create([
                'booking_code' => Booking::generateCode(),
                'user_id' => $user?->id,
                'glamping_unit_id' => $unit->id,
                'guest_name' => $data['guest_name'],
                'guest_email' => strtolower($data['guest_email']),
                'guest_phone' => $data['guest_phone'],
                'check_in_date' => $data['check_in_date'],
                'check_out_date' => $data['check_out_date'],
                'unit_qty' => $data['unit_qty'],
                'room_total' => $quote['room_total'],
                'has_insurance' => (bool) $data['has_insurance'],
                'insurance_fee' => $quote['insurance_fee'],
                'total_price' => $quote['total_price'],
                'status' => 'pending',
            ]);
        });
    }
}
