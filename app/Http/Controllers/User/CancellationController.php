<?php

// [Anggota 3] Pengajuan pembatalan oleh user (estimasi refund otomatis)

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreCancellationRequest;
use App\Models\Booking;
use App\Services\RefundCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CancellationController extends Controller
{
    public function create(Request $request, Booking $booking): View|RedirectResponse
    {
        abort_unless($booking->isOwnedBy($request->user()), 403);

        if (! RefundCalculator::canCancel($booking)) {
            return redirect()->route('user.dashboard')->with('error', 'Booking '.$booking->booking_code.' tidak dapat diajukan pembatalan.');
        }

        return view('user.cancel', [
            'booking' => $booking->load('unit'),
            'estimate' => RefundCalculator::estimate($booking),
        ]);
    }

    public function store(StoreCancellationRequest $request, Booking $booking): RedirectResponse
    {
        if (! RefundCalculator::canCancel($booking)) {
            return redirect()->route('user.dashboard')->with('error', 'Booking ini tidak dapat diajukan pembatalan.');
        }

        $estimate = RefundCalculator::estimate($booking);

        $booking->cancellations()->create([
            'reason' => $request->reason,
            'refund_percentage' => $estimate['percentage'],
            'refund_amount' => $estimate['amount'],
            'status' => 'pending',
        ]);

        // Hubungkan booking tamu ke akun bila sebelumnya dibuat tanpa login
        if (! $booking->user_id) {
            $booking->update(['user_id' => $request->user()->id]);
        }

        return redirect()->route('user.dashboard')
            ->with('success', 'Pengajuan pembatalan '.$booking->booking_code.' terkirim. Estimasi refund '.$estimate['percentage'].'% ('.rupiah($estimate['amount']).') menunggu persetujuan staff.');
    }
}
