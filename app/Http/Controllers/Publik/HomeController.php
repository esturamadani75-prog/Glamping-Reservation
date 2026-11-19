<?php

// [Anggota 2] Landing page

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Booking;
use App\Models\GlampingUnit;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $units = GlampingUnit::active()->orderBy('price_per_night')->get();
        $articles = Article::published()->latest('published_at')->take(3)->get();

        $stats = [
            'units' => $units->sum('stock_total'),
            'guests' => Booking::whereIn('status', ['confirmed', 'completed'])->count(),
            'types' => $units->count(),
        ];

        return view('publik.home', compact('units', 'articles', 'stats'));
    }
}
