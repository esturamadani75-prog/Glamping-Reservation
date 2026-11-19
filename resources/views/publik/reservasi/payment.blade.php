{{-- [Anggota 2] Simulasi pembayaran sandbox --}}
@extends('layouts.app')
@section('title', 'Pembayaran '.$booking->booking_code)

@section('content')
<div class="mx-auto max-w-3xl px-4 sm:px-6">
    <p class="eyebrow">Langkah terakhir</p>
    <h1 class="section-title mt-2">Selesaikan pembayaran</h1>

    <div class="card mt-8 overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-canvas-dark px-6 py-4">
            <div>
                <p class="text-xs uppercase tracking-widest text-slate-500">Kode Booking</p>
                <p class="font-mono text-xl font-bold tracking-wider">{{ $booking->booking_code }}</p>
            </div>
            <x-status-badge :status="$booking->status" />
        </div>
        <dl class="grid gap-4 p-6 text-sm sm:grid-cols-2">
            <div><dt class="text-slate-500">Unit</dt><dd class="font-semibold">{{ $booking->unit->name }} × {{ $booking->unit_qty }}</dd></div>
            <div><dt class="text-slate-500">Pemesan</dt><dd class="font-semibold">{{ $booking->guest_name }}</dd></div>
            <div><dt class="text-slate-500">Check-in</dt><dd class="font-semibold">{{ tanggal($booking->check_in_date, 'l, d M Y') }} · 14:00</dd></div>
            <div><dt class="text-slate-500">Check-out</dt><dd class="font-semibold">{{ tanggal($booking->check_out_date, 'l, d M Y') }} · 12:00</dd></div>
        </dl>
        <dl class="space-y-2 border-t border-slate-100 p-6 text-sm">
            <div class="flex justify-between"><dt>{{ rupiah($booking->unit->price_per_night) }} × {{ $booking->unit_qty }} unit × {{ $booking->nights() }} malam</dt><dd>{{ rupiah($booking->room_total) }}</dd></div>
            <div class="flex justify-between"><dt>Asuransi pembatalan</dt><dd>{{ $booking->has_insurance ? rupiah($booking->insurance_fee) : '-' }}</dd></div>
            <div class="flex items-baseline justify-between border-t pt-3"><dt class="font-semibold">Total Pembayaran</dt><dd class="text-2xl font-bold text-paddy">{{ rupiah($booking->total_price) }}</dd></div>
        </dl>
    </div>

    {{-- Simulasi gateway --}}
    <div x-data="{ method: 'qris', processing: false }" class="card mt-6 p-6">
        <div class="flex items-center gap-2">
            <span class="rounded-md bg-harvest-100 px-2 py-0.5 text-xs font-bold uppercase text-harvest-700">Sandbox</span>
            <p class="text-sm text-slate-600">Mode uji coba — tidak ada uang sungguhan yang ditarik.</p>
        </div>
        <div class="mt-5 grid gap-3 sm:grid-cols-3">
            @foreach (['qris' => 'QRIS', 'va' => 'Virtual Account', 'ewallet' => 'E-Wallet'] as $key => $label)
                <label class="cursor-pointer rounded-xl border-2 px-4 py-3 text-center text-sm font-semibold"
                       :class="method === '{{ $key }}' ? 'border-paddy bg-paddy-50 text-paddy-700' : 'border-slate-200'">
                    <input type="radio" class="sr-only" value="{{ $key }}" x-model="method">{{ $label }}
                </label>
            @endforeach
        </div>
        <form method="POST" action="{{ route('booking.pay', $booking) }}" @submit="processing = true" class="mt-6">
            @csrf
            <button class="btn-accent w-full py-3 text-base" :disabled="processing">
                <span x-show="!processing">Bayar Sekarang (Sandbox)</span>
                <span x-cloak x-show="processing">Memproses pembayaran…</span>
            </button>
        </form>
        <p class="mt-3 text-center text-xs text-slate-500">Simpan kode booking Anda. Booking yang belum dibayar tetap menahan stok.</p>
    </div>
</div>
@endsection
