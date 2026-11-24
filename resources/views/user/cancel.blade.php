{{-- [Anggota 3] Form pengajuan pembatalan + estimasi refund otomatis --}}
@extends('layouts.app')
@section('title', 'Ajukan Pembatalan')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6">
    <a href="{{ route('user.dashboard') }}" class="text-sm font-semibold text-paddy hover:underline">← Kembali ke riwayat</a>
    <h1 class="section-title mt-4">Ajukan pembatalan</h1>
    <p class="mt-2 text-slate-600">Booking <span class="font-mono font-bold">{{ $booking->booking_code }}</span> · {{ $booking->unit->name }} × {{ $booking->unit_qty }}</p>

    <div class="mt-8 grid gap-6 md:grid-cols-5">
        {{-- Estimasi refund --}}
        <div class="card overflow-hidden md:col-span-2">
            <div class="p-6 {{ $estimate['percentage'] === 100 ? 'bg-paddy text-white' : ($estimate['percentage'] === 50 ? 'bg-harvest text-white' : 'bg-merapi text-white') }}">
                <p class="text-xs uppercase tracking-widest opacity-80">Estimasi Refund</p>
                <p class="mt-1 font-display text-5xl font-semibold">{{ $estimate['percentage'] }}%</p>
                <p class="mt-1 text-lg font-semibold">{{ rupiah($estimate['amount']) }}</p>
            </div>
            <dl class="space-y-2 p-6 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Check-in</dt><dd>{{ tanggal($booking->check_in_date) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Selisih hari</dt><dd>{{ $estimate['days'] }} hari lagi</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Asuransi</dt><dd>{{ $booking->has_insurance ? 'Ya' : 'Tidak' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Biaya kamar</dt><dd>{{ rupiah($estimate['room_cost']) }}</dd></div>
                <p class="rounded-lg bg-canvas p-3 text-xs leading-relaxed text-slate-600">{{ $estimate['rule'] }}</p>
                <p class="text-xs text-slate-400">Biaya asuransi ({{ rupiah($booking->insurance_fee) }}) tidak ikut dikembalikan.</p>
            </dl>
        </div>

        {{-- Form alasan --}}
        <form method="POST" action="{{ route('user.cancel.store', $booking) }}" class="card space-y-5 p-6 md:col-span-3">
            @csrf
            <div>
                <label class="label" for="reason">Alasan pembatalan</label>
                <textarea id="reason" name="reason" rows="6" class="input @error('reason') input-error @enderror" placeholder="Ceritakan alasan Anda membatalkan reservasi (min. 10 karakter)…" required>{{ old('reason') }}</textarea>
                @error('reason')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-start gap-3 text-sm text-slate-600">
                <input type="checkbox" name="agree" value="1" class="mt-0.5 rounded border-slate-300 text-paddy focus:ring-paddy" required>
                Saya memahami bahwa estimasi refund dihitung otomatis dan pembatalan baru berlaku setelah disetujui staff.
            </label>
            @error('agree')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

            <div class="rounded-xl border border-slate-200 p-4 text-xs text-slate-600">
                <p class="font-semibold text-merapi">Ketentuan refund</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    <li>Berasuransi & batal &gt; 3 hari sebelum check-in → refund 100% biaya kamar</li>
                    <li>Berasuransi & batal ≤ 3 hari sebelum check-in → refund 50% biaya kamar</li>
                    <li>Tanpa asuransi → refund 0%</li>
                </ul>
            </div>
            <button class="btn-danger w-full">Kirim Pengajuan Pembatalan</button>
        </form>
    </div>
</div>
@endsection
