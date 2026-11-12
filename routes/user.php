<?php

// [Anggota 1 - SEMENTARA] Route sementara milik Anggota 3.
// File ini AKAN DITIMPA oleh routes/user.php versi asli saat Anggota 3 push.

use Illuminate\Support\Facades\Route;

Route::view('/statistik', 'placeholder', ['anggota' => 3, 'fitur' => 'Statistik'])->name('statistik');

Route::middleware(['auth', 'role:user'])->prefix('akun')->name('user.')->group(function () {
    Route::view('/', 'placeholder', ['anggota' => 3, 'fitur' => 'Dashboard User'])->name('dashboard');
    Route::view('/profil', 'placeholder', ['anggota' => 3, 'fitur' => 'Profil'])->name('profile.edit');
});
