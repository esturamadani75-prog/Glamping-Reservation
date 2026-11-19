{{-- [Anggota 2] Form cek status booking --}}
@extends('layouts.app')
@section('title', 'Cek Status Booking')

@section('content')
<div class="px-4">
    <div class="mx-auto max-w-lg">
        <div class="text-center">
            <p class="eyebrow">Cek Booking</p>
            <h1 class="section-title mt-2">Lihat status & kartu check-in</h1>
            <p class="mt-3 text-slate-600">Masukkan kode booking dan email atau nomor telepon yang dipakai saat reservasi.</p>
        </div>

        <form method="POST" action="{{ route('booking.check.submit') }}" class="card mt-8 space-y-5 p-8">
            @csrf
            <div>
                <label class="label" for="booking_code">Kode Booking</label>
                <input id="booking_code" name="booking_code" value="{{ old('booking_code') }}" placeholder="MGTS-XXXXXX" class="input font-mono uppercase tracking-wider @error('booking_code') input-error @enderror" required>
                @error('booking_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="contact">Email / No. Telepon</label>
                <input id="contact" name="contact" value="{{ old('contact') }}" placeholder="nama@email.com atau 08xxxxxxxxxx" class="input @error('contact') input-error @enderror" required>
                @error('contact')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <button class="btn-primary w-full py-3">Cek Status</button>
            <p class="text-center text-xs text-slate-500">Contoh data demo: <span class="font-mono">MGTS-DEMO01</span> + <span class="font-mono">user@demo.test</span></p>
        </form>
    </div>
</div>
@endsection
