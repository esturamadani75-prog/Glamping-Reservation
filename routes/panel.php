<?php

// [Anggota 1 - SEMENTARA] Route sementara milik Anggota 4.
// File ini AKAN DITIMPA oleh routes/panel.php versi asli saat Anggota 4 push.

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:staff,admin'])->prefix('staff')->name('staff.')->group(function () {
    Route::view('/', 'placeholder', ['anggota' => 4, 'fitur' => 'Dashboard Staff', 'panel' => true])->name('dashboard');
    Route::view('/transaksi', 'placeholder', ['anggota' => 4, 'fitur' => 'Transaksi', 'panel' => true])->name('transaksi.index');
    Route::view('/pembatalan', 'placeholder', ['anggota' => 4, 'fitur' => 'Manajemen Pembatalan', 'panel' => true])->name('pembatalan.index');
    Route::view('/unit', 'placeholder', ['anggota' => 4, 'fitur' => 'Unit Glamping', 'panel' => true])->name('unit.index');
    Route::view('/artikel', 'placeholder', ['anggota' => 4, 'fitur' => 'Kelola Artikel', 'panel' => true])->name('artikel.index');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/akun', 'placeholder', ['anggota' => 4, 'fitur' => 'Manajemen Akun', 'panel' => true])->name('akun.index');
    Route::view('/audit-log', 'placeholder', ['anggota' => 4, 'fitur' => 'Audit Log Staff', 'panel' => true])->name('audit.index');
});
