<?php

// [Anggota 3] Halaman statistik publik + statistik pribadi (bila user login)

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(Request $request, StatisticsService $stats): View
    {
        $personal = null;
        if ($user = $request->user()) {
            $mine = DashboardController::userBookings($request)->get(['status', 'check_in_date', 'check_out_date', 'total_price']);
            $byStatus = [];
            foreach (Booking::STATUSES as $s) {
                $byStatus[status_label($s)] = $mine->where('status', $s)->count();
            }
            $personal = [
                'byStatus' => $byStatus,
                'nights' => $mine->whereIn('status', ['confirmed', 'completed'])->sum(fn ($b) => $b->check_in_date->diffInDays($b->check_out_date)),
                'total' => $mine->count(),
            ];
        }

        return view('statistik.index', [
            'summary' => $stats->summary(),
            'rentals' => $stats->rentalsPerPeriod(),
            'cancellations' => $stats->cancellationTrend(),
            'usage' => $stats->stockUsage(),
            'personal' => $personal,
        ]);
    }
}
