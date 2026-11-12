<?php

// [Anggota 1] Data dummy reservasi, pembatalan & audit log
// Tujuannya agar dashboard staff, riwayat user dan grafik statistik langsung terisi.

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Cancellation;
use App\Models\GlampingUnit;
use App\Models\StaffAuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    private array $units;

    public function run(): void
    {
        mt_srand(2026);   // hasil acak selalu sama setiap seeding

        $this->units = GlampingUnit::all()->keyBy('slug')->all();
        $user = User::where('email', 'user@demo.test')->first();
        $staff = User::where('email', 'staff@demo.test')->first();
        $today = Carbon::today();

        // ---------- 1. Booking demo milik user@demo.test (kode tetap, mudah diuji) ----------
        $this->make('MGTS-DEMO01', 'deluxe-merapi-view-balkon', $today->copy()->addDays(10), 2, 1, true, 'confirmed', $user);
        $demo2 = $this->make('MGTS-DEMO02', 'standard-river-view-tent', $today->copy()->addDays(2), 1, 1, true, 'confirmed', $user);
        $this->make('MGTS-DEMO03', 'family-glamping-suite', $today->copy()->subDays(20), 2, 1, false, 'completed', $user);
        $this->make('MGTS-DEMO04', 'standard-river-view-tent', $today->copy(), 1, 1, false, 'confirmed', $user);
        $this->make('MGTS-DEMO05', 'deluxe-merapi-view-balkon', $today->copy()->addDays(21), 3, 2, false, 'pending', $user);

        // Pengajuan pembatalan pending (<= 3 hari sebelum check-in -> refund 50%)
        Cancellation::create([
            'booking_id' => $demo2->id,
            'reason' => 'Ada acara keluarga mendadak sehingga tidak bisa berangkat.',
            'refund_percentage' => 50,
            'refund_amount' => (int) round($demo2->room_total * 0.5),
            'status' => 'pending',
        ]);

        // ---------- 2. Booking tamu acak ±11 bulan ke belakang + 1 bulan ke depan ----------
        $names = ['Budi Santoso', 'Siti Aminah', 'Andi Pratama', 'Dewi Lestari', 'Agus Wibowo', 'Putri Ayu', 'Rizky Maulana',
            'Nur Hidayah', 'Fajar Nugroho', 'Intan Permata', 'Yoga Saputra', 'Larasati', 'Hendra Gunawan', 'Maya Sari', 'Bayu Aji'];
        $slugs = array_keys($this->units);
        $created = 0;

        for ($i = 0; $i < 320 && $created < 220; $i++) {
            // separuh data dipadatkan di 3 bulan terakhir agar grafik terbaru lebih "hidup"
            $daysAgo = mt_rand(1, 100) <= 50 ? mt_rand(-30, 90) : mt_rand(91, 330);
            $checkIn = $today->copy()->subDays($daysAgo);
            $nights = mt_rand(1, 3);
            $slug = $slugs[mt_rand(0, 2)];
            $qty = mt_rand(1, 2);
            $insurance = mt_rand(0, 1) === 1;

            if ($checkIn->lt($today)) {
                // masa lalu: mayoritas selesai, sebagian dibatalkan
                $status = mt_rand(1, 100) <= 82 ? 'completed' : 'cancelled';
            } else {
                $status = mt_rand(1, 100) <= 80 ? 'confirmed' : 'pending';
            }

            if (! $this->hasStock($slug, $checkIn, $checkIn->copy()->addDays($nights), $qty, $status)) {
                continue;
            }

            $name = $names[array_rand($names)];
            $booking = $this->make(null, $slug, $checkIn, $nights, $qty, $insurance, $status, null, $name);
            $booking->created_at = $checkIn->copy()->subDays(mt_rand(3, 25))->setTime(mt_rand(8, 21), mt_rand(0, 59));
            $booking->save();
            $created++;

            // booking yang dibatalkan -> buat pengajuan yang disetujui / ditolak
            if ($status === 'cancelled') {
                $pct = $insurance ? 100 : 0;
                Cancellation::create([
                    'booking_id' => $booking->id,
                    'reason' => 'Perubahan jadwal perjalanan.',
                    'refund_percentage' => $pct,
                    'refund_amount' => (int) round($booking->room_total * $pct / 100),
                    'status' => 'approved',
                    'staff_note' => $pct ? 'Refund ditransfer ke rekening tamu.' : 'Tanpa asuransi, deposit hangus.',
                    'processed_by' => $staff->id,
                    'processed_at' => $booking->created_at->copy()->addDay(),
                    'created_at' => $booking->created_at->copy()->addHours(5),
                ]);
            } elseif ($status === 'completed' && mt_rand(1, 100) <= 10) {
                Cancellation::create([
                    'booking_id' => $booking->id,
                    'reason' => 'Ingin membatalkan karena cuaca.',
                    'refund_percentage' => 0,
                    'refund_amount' => 0,
                    'status' => 'rejected',
                    'staff_note' => 'Pengajuan melewati batas waktu, tamu tetap menginap.',
                    'processed_by' => $staff->id,
                    'processed_at' => $booking->created_at->copy()->addDay(),
                    'created_at' => $booking->created_at->copy()->addHours(3),
                ]);
            }
        }

        // ---------- 3. Contoh audit log ----------
        $logs = [
            ['unit.update', 'Mengubah tarif "Deluxe Merapi View (Balkon)" dari Rp 600.000 menjadi Rp 650.000.', 14],
            ['article.create', 'Menambahkan artikel "Glamping Minimalis: Menikmati Tenangnya Pedesaan Cangkringan".', 4],
            ['cancellation.approve', 'Menyetujui pembatalan booking contoh, refund 100%.', 2],
        ];
        foreach ($logs as [$action, $desc, $daysAgo]) {
            $log = StaffAuditLog::create(['user_id' => $staff->id, 'action' => $action, 'description' => $desc]);
            $log->created_at = now()->subDays($daysAgo);
            $log->save();
        }
    }

    private function make(?string $code, string $slug, Carbon $checkIn, int $nights, int $qty, bool $insurance,
        string $status, ?User $user = null, ?string $guestName = null): Booking
    {
        $unit = $this->units[$slug];
        $roomTotal = $unit->price_per_night * $qty * $nights;
        $fee = $insurance ? Booking::INSURANCE_FEE : 0;
        $guestName ??= $user?->name ?? 'Tamu';

        return Booking::create([
            'booking_code' => $code ?? Booking::generateCode(),
            'user_id' => $user?->id,
            'glamping_unit_id' => $unit->id,
            'guest_name' => $guestName,
            'guest_email' => $user?->email ?? strtolower(str_replace(' ', '.', $guestName)).'@contoh.test',
            'guest_phone' => $user?->phone ?? '08'.mt_rand(1000000000, 9999999999),
            'check_in_date' => $checkIn->toDateString(),
            'check_out_date' => $checkIn->copy()->addDays($nights)->toDateString(),
            'unit_qty' => $qty,
            'room_total' => $roomTotal,
            'has_insurance' => $insurance,
            'insurance_fee' => $fee,
            'total_price' => $roomTotal + $fee,
            'status' => $status,
            'paid_at' => in_array($status, ['confirmed', 'completed', 'cancelled']) ? $checkIn->copy()->subDays(2) : null,
        ]);
    }

    /** Cek kasar agar data dummy tidak melebihi stok per malam. */
    private function hasStock(string $slug, Carbon $in, Carbon $out, int $qty, string $status): bool
    {
        if ($status === 'cancelled') {
            return true;
        }
        $unit = $this->units[$slug];
        for ($d = $in->copy(); $d->lt($out); $d->addDay()) {
            $used = Booking::where('glamping_unit_id', $unit->id)
                ->whereIn('status', ['pending', 'confirmed', 'completed'])
                ->whereDate('check_in_date', '<=', $d)
                ->whereDate('check_out_date', '>', $d)
                ->sum('unit_qty');
            if ($used + $qty > $unit->stock_total) {
                return false;
            }
        }

        return true;
    }
}
