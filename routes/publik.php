<?php

// [Anggota 1 - SEMENTARA] Route sementara milik Anggota 2.
// File ini AKAN DITIMPA oleh routes/publik.php versi asli saat Anggota 2 push.

use Illuminate\Support\Facades\Route;

Route::view('/', 'placeholder', ['anggota' => 2, 'fitur' => 'Landing Page'])->name('home');
Route::view('/artikel', 'placeholder', ['anggota' => 2, 'fitur' => 'Artikel'])->name('artikel.index');
Route::view('/reservasi', 'placeholder', ['anggota' => 2, 'fitur' => 'Form Reservasi'])->name('booking.create');
Route::view('/cek-booking', 'placeholder', ['anggota' => 2, 'fitur' => 'Cek Status Booking'])->name('booking.check');
