<?php

// [Anggota 2] Cek status booking (kode + email / no. telepon) & kartu check-in

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CheckBookingRequest;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookingStatusController extends Controller
{
    public function form(): View
    {
        return view('publik.reservasi.check');
    }

    public function check(CheckBookingRequest $request): RedirectResponse
    {
        $contact = $request->contact;
        $phone = preg_replace('/\D/', '', $contact);

        $booking = Booking::where('booking_code', $request->booking_code)
            ->where(function ($q) use ($contact, $phone) {
                $q->whereRaw('LOWER(guest_email) = ?', [strtolower($contact)]);
                if (strlen($phone) >= 8) {
                    $q->orWhere('guest_phone', $phone)->orWhere('guest_phone', $contact);
                }
            })
            ->first();

        if (! $booking) {
            return back()->withInput()->with('error', 'Booking tidak ditemukan. Pastikan kode booking dan email / no. telepon sesuai.');
        }

        BookingController::grantAccess($booking);

        return redirect()->route('booking.show', $booking);
    }

    public function show(Booking $booking): View
    {
        abort_unless(BookingController::canAccess($booking), 403, 'Silakan cek booking dengan kode & email/no. telepon terlebih dahulu.');

        $booking->load('unit', 'latestCancellation');

        // Checklist serah terima kamar: item umum + fasilitas khusus unit
        $checklist = array_merge(
            ['Kunci / kartu akses unit', 'Kasur, bantal & linen bersih', 'Handuk & perlengkapan mandi', 'AC & remote berfungsi', 'Password WiFi diterima'],
            array_map(fn ($f) => 'Fasilitas: '.$f, $booking->unit->facilities ?? []),
            ['Gelang akses kolam renang & area pemancingan', 'Kamar mandi bersih & air panas menyala']
        );

        return view('publik.reservasi.show', compact('booking', 'checklist'));
    }
}
