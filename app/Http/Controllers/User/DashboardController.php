<?php

// [Anggota 3] Dashboard user: ringkasan & riwayat sewa

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Booking milik user: terhubung ke akun ATAU memakai email yang sama (reservasi sebelum login). */
    public static function userBookings(Request $request): Builder
    {
        $user = $request->user();

        return Booking::query()->where(fn ($q) => $q
            ->where('user_id', $user->id)
            ->orWhereRaw('LOWER(guest_email) = ?', [strtolower($user->email)]));
    }

    public function index(Request $request): View
    {
        $base = self::userBookings($request);

        $summary = [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->whereIn('status', ['pending', 'confirmed'])->count(),
            'completed' => (clone $base)->where('status', 'completed')->count(),
            'spent' => (clone $base)->whereIn('status', ['confirmed', 'completed'])->sum('total_price'),
        ];

        $statusCounts = (clone $base)->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $bookings = (clone $base)
            ->with(['unit', 'latestCancellation'])
            ->when(in_array($request->status, Booking::STATUSES, true), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $upcoming = (clone $base)->with('unit')->where('status', 'confirmed')
            ->whereDate('check_in_date', '>=', today())->orderBy('check_in_date')->first();

        return view('user.dashboard', compact('summary', 'statusCounts', 'bookings', 'upcoming'));
    }
}
