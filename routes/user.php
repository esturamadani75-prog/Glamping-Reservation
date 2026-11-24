<?php

// [Anggota 3] Route area user: dashboard, profil, pembatalan, statistik

use App\Http\Controllers\User\CancellationController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\StatisticsController;
use Illuminate\Support\Facades\Route;

// Statistik bisa dibuka publik; jika login sebagai user tampil juga statistik pribadi
Route::get('/statistik', [StatisticsController::class, 'index'])->name('statistik');

Route::middleware(['auth', 'role:user'])->prefix('akun')->name('user.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');

    Route::get('/booking/{booking:booking_code}/batal', [CancellationController::class, 'create'])->name('cancel.create');
    Route::post('/booking/{booking:booking_code}/batal', [CancellationController::class, 'store'])->name('cancel.store');
});
