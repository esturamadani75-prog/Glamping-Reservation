<?php

/*
|--------------------------------------------------------------------------
| [Anggota 1] File route utama
|--------------------------------------------------------------------------
| Route dipecah per anggota agar tidak bentrok saat merge Git:
|   routes/auth.php   -> Anggota 1 (login, register, logout)
|   routes/publik.php -> Anggota 2 (landing, artikel, reservasi, cek booking)
|   routes/user.php   -> Anggota 3 (dashboard user, profil, pembatalan, statistik)
|   routes/panel.php  -> Anggota 4 (panel staff & admin)
*/

use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';
require __DIR__.'/publik.php';
require __DIR__.'/user.php';
require __DIR__.'/panel.php';

// /dashboard -> arahkan ke dashboard sesuai role
Route::get('/dashboard', fn () => redirect(auth()->user()->homeRoute()))
    ->middleware('auth')
    ->name('dashboard');
