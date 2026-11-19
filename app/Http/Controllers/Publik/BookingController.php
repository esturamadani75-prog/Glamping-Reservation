<?php

// [Anggota 2] Form reservasi, cek ketersediaan (AJAX) & pembayaran sandbox

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Models\Booking;
use App\Models\GlampingUnit;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(private ReservationService $reservation) {}

    public function create(Request $request): View
    {
        $units = GlampingUnit::active()->orderBy('price_per_night')->get();
        $selected = $units->firstWhere('slug', $request->unit) ?? $units->first();

        return view('publik.reservasi.create', [
            'units' => $units,
            'selected' => $selected,
            'checkIn' => $request->check_in ?? today()->addDay()->toDateString(),
            'checkOut' => $request->check_out ?? today()->addDays(2)->toDateString(),
        ]);
    }

    /** Endpoint JSON untuk Alpine.js: sisa stok + simulasi harga secara real-time. */
    public function availability(Request $request): JsonResponse
    {
        $data = $request->validate([
            'unit' => ['required', 'exists:glamping_units,id'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'qty' => ['nullable', 'integer', 'min:1'],
            'insurance' => ['nullable', 'boolean'],
        ]);

        $unit = GlampingUnit::findOrFail($data['unit']);
        $remaining = $this->reservation->remainingStock($unit, $data['check_in'], $data['check_out']);
        $quote = $this->reservation->quote($unit, $data['check_in'], $data['check_out'], (int) ($data['qty'] ?? 1), (bool) ($data['insurance'] ?? false));

        return response()->json([
            'remaining' => $remaining,
            'stock_total' => $unit->stock_total,
            'past' => Carbon::parse($data['check_in'])->lt(today()),
        ] + $quote);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $booking = $this->reservation->createBooking($request->validated(), $request->user());

        $this->grantAccess($booking);

        return redirect()->route('booking.payment', $booking)
            ->with('success', 'Reservasi dibuat dengan kode '.$booking->booking_code.'. Selesaikan pembayaran untuk mengonfirmasi.');
    }

    public function payment(Booking $booking): View|RedirectResponse
    {
        $this->authorizeAccess($booking);

        if ($booking->status !== 'pending') {
            return redirect()->route('booking.show', $booking);
        }

        return view('publik.reservasi.payment', ['booking' => $booking->load('unit')]);
    }

    /** Simulasi pembayaran sandbox: status langsung "confirmed". */
    public function pay(Booking $booking): RedirectResponse
    {
        $this->authorizeAccess($booking);

        if ($booking->status !== 'pending') {
            return redirect()->route('booking.show', $booking)->with('error', 'Booking ini sudah tidak menunggu pembayaran.');
        }

        $booking->update(['status' => 'confirmed', 'paid_at' => now()]);

        return redirect()->route('booking.show', $booking)
            ->with('success', 'Pembayaran sandbox berhasil! Booking Anda sudah terkonfirmasi.');
    }

    // ----------------------------------------------------------------
    // Hak akses halaman booking: kode tersimpan di session (setelah reservasi / cek booking),
    // pemilik akun, atau staff/admin.
    // ----------------------------------------------------------------
    public static function grantAccess(Booking $booking): void
    {
        session()->push('booking_access', $booking->booking_code);
    }

    public static function canAccess(Booking $booking): bool
    {
        $user = auth()->user();

        return in_array($booking->booking_code, session('booking_access', []), true)
            || $booking->isOwnedBy($user)
            || ($user && $user->canAccessPanel());
    }

    private function authorizeAccess(Booking $booking): void
    {
        abort_unless(self::canAccess($booking), 403, 'Silakan cek booking dengan kode & email/no. telepon terlebih dahulu.');
    }
}
