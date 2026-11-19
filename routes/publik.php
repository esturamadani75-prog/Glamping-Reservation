<?php

// [Anggota 2] Route halaman publik: landing, artikel, reservasi, cek booking

use App\Http\Controllers\Publik\ArticleController;
use App\Http\Controllers\Publik\BookingController;
use App\Http\Controllers\Publik\BookingStatusController;
use App\Http\Controllers\Publik\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Artikel
Route::get('/artikel', [ArticleController::class, 'index'])->name('artikel.index');
Route::get('/artikel/{article}', [ArticleController::class, 'show'])->name('artikel.show');

// Reservasi
Route::get('/reservasi', [BookingController::class, 'create'])->name('booking.create');
Route::post('/reservasi', [BookingController::class, 'store'])->name('booking.store')->middleware('throttle:10,1');
Route::get('/reservasi/cek-ketersediaan', [BookingController::class, 'availability'])->name('booking.availability');
Route::get('/reservasi/{booking:booking_code}/pembayaran', [BookingController::class, 'payment'])->name('booking.payment');
Route::post('/reservasi/{booking:booking_code}/bayar', [BookingController::class, 'pay'])->name('booking.pay');

// Cek status booking
Route::get('/cek-booking', [BookingStatusController::class, 'form'])->name('booking.check');
Route::post('/cek-booking', [BookingStatusController::class, 'check'])->name('booking.check.submit')->middleware('throttle:15,1');
Route::get('/booking/{booking:booking_code}', [BookingStatusController::class, 'show'])->name('booking.show');
