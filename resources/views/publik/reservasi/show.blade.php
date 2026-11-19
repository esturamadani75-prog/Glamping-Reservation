{{-- [Anggota 2] Hasil cek booking: detail, Kartu Check-in + QR dummy, checklist serah terima --}}
@extends('layouts.app')
@section('title', 'Booking '.$booking->booking_code)

@section('content')
<div class="mx-auto max-w-5xl px-4 sm:px-6">
    <div class="flex flex-wrap items-center justify-between gap-3 print:hidden">
        <a href="{{ route('booking.check') }}" class="text-sm font-semibold text-paddy hover:underline">← Cek booking lain</a>
        @if ($booking->status === 'confirmed')
            <button onclick="window.print()" class="btn-outline btn-sm">🖨 Cetak Kartu</button>
        @endif
    </div>

    @if ($booking->status === 'confirmed')
        {{-- ================= KARTU CHECK-IN ================= --}}
        <div class="mt-6 grid gap-6 lg:grid-cols-5">
            <div class="self-start overflow-hidden rounded-3xl bg-paddy text-white shadow-xl lg:col-span-3">
                <div class="relative h-28">
                    <x-landscape variant="dusk" class="absolute inset-0 opacity-60" />
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-paddy"></div>
                    <div class="absolute left-6 top-5"><x-logo light /></div>
                </div>
                <div class="grid gap-6 px-6 pb-6 sm:grid-cols-[1fr_auto]">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-paddy-100">Kartu Check-in Glamping</p>
                        <p class="mt-1 font-mono text-2xl font-bold tracking-wider">{{ $booking->booking_code }}</p>
                        <dl class="mt-5 grid grid-cols-2 gap-4 text-sm">
                            <div><dt class="text-paddy-100">Nama Tamu</dt><dd class="font-semibold">{{ $booking->guest_name }}</dd></div>
                            <div><dt class="text-paddy-100">Unit</dt><dd class="font-semibold">{{ $booking->unit->name }} × {{ $booking->unit_qty }}</dd></div>
                            <div><dt class="text-paddy-100">Check-in</dt><dd class="font-semibold">{{ tanggal($booking->check_in_date, 'd M Y') }} · 14:00</dd></div>
                            <div><dt class="text-paddy-100">Check-out</dt><dd class="font-semibold">{{ tanggal($booking->check_out_date, 'd M Y') }} · 12:00</dd></div>
                            <div><dt class="text-paddy-100">Asuransi</dt><dd class="font-semibold">{{ $booking->has_insurance ? 'Ya' : 'Tidak' }}</dd></div>
                            <div><dt class="text-paddy-100">Status</dt><dd class="font-semibold text-harvest-200">LUNAS · Terkonfirmasi</dd></div>
                        </dl>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <div id="qrcode" class="rounded-xl bg-white p-3"></div>
                        <p class="mt-2 text-[10px] uppercase tracking-widest text-paddy-100">QR Dummy</p>
                    </div>
                </div>
                <p class="border-t border-white/10 bg-paddy-800 px-6 py-3 text-xs text-paddy-100">Tunjukkan kartu ini ke resepsionis beserta kartu identitas. {{ config('glamping.address') }}</p>
            </div>

            {{-- ================= CHECKLIST SERAH TERIMA ================= --}}
            <div class="card p-6 lg:col-span-2" x-data="{ done: [] }">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold">Checklist Serah Terima</h2>
                    <span class="text-xs font-semibold text-paddy" x-text="done.length + '/{{ count($checklist) }}'"></span>
                </div>
                <p class="mt-1 text-xs text-slate-500">Centang bersama staff saat menerima kamar & fasilitas.</p>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full bg-paddy transition-all" :style="`width: ${done.length / {{ count($checklist) }} * 100}%`"></div>
                </div>
                <ul class="mt-4 space-y-2">
                    @foreach ($checklist as $i => $item)
                        <li>
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg px-2 py-1.5 text-sm hover:bg-canvas">
                                <input type="checkbox" value="{{ $i }}" x-model="done" class="mt-0.5 rounded border-slate-300 text-paddy focus:ring-paddy">
                                <span :class="done.includes('{{ $i }}') ? 'text-slate-400 line-through' : ''">{{ $item }}</span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @else
        {{-- ================= STATUS SELAIN CONFIRMED ================= --}}
        <div class="card mt-6 p-8 text-center">
            <p class="text-xs uppercase tracking-widest text-slate-500">Kode Booking</p>
            <p class="mt-1 font-mono text-2xl font-bold tracking-wider">{{ $booking->booking_code }}</p>
            <div class="mt-3"><x-status-badge :status="$booking->status" /></div>
            <p class="mx-auto mt-4 max-w-md text-slate-600">
                @switch($booking->status)
                    @case('pending') Booking belum dibayar. Kartu check-in akan muncul setelah pembayaran berhasil. @break
                    @case('cancelled') Booking ini telah dibatalkan. @break
                    @case('completed') Terima kasih telah menginap! Booking ini sudah selesai. @break
                @endswitch
            </p>
            @if ($booking->status === 'pending')
                <a href="{{ route('booking.payment', $booking) }}" class="btn-accent mt-6">Lanjutkan Pembayaran</a>
            @endif
        </div>
    @endif

    {{-- ================= DETAIL BOOKING ================= --}}
    <div class="card mt-6 p-6 print:hidden">
        <h2 class="font-semibold">Rincian reservasi</h2>
        <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-3">
            <div><dt class="text-slate-500">Email</dt><dd>{{ $booking->guest_email }}</dd></div>
            <div><dt class="text-slate-500">Telepon</dt><dd>{{ $booking->guest_phone }}</dd></div>
            <div><dt class="text-slate-500">Lama menginap</dt><dd>{{ $booking->nights() }} malam</dd></div>
            <div><dt class="text-slate-500">Biaya kamar</dt><dd>{{ rupiah($booking->room_total) }}</dd></div>
            <div><dt class="text-slate-500">Asuransi</dt><dd>{{ $booking->has_insurance ? rupiah($booking->insurance_fee) : 'Tidak' }}</dd></div>
            <div><dt class="text-slate-500">Total</dt><dd class="font-bold text-paddy">{{ rupiah($booking->total_price) }}</dd></div>
        </dl>

        @if ($booking->latestCancellation)
            <div class="mt-6 flex flex-wrap items-center gap-3 rounded-xl bg-canvas p-4 text-sm">
                <span>Pengajuan pembatalan:</span>
                <x-status-badge :status="$booking->latestCancellation->status" />
                <span class="text-slate-500">Estimasi refund {{ $booking->latestCancellation->refund_percentage }}% ({{ rupiah($booking->latestCancellation->refund_amount) }})</span>
            </div>
        @endif

        {{-- Tombol pembatalan milik Anggota 3 (muncul otomatis setelah tahap 3 di-push) --}}
        @includeIf('user._cancel-button', ['booking' => $booking])
    </div>
</div>
@endsection

@if ($booking->status === 'confirmed')
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
        <script>
            new QRCode(document.getElementById('qrcode'), {
                text: @js('MGTS-CHECKIN|'.$booking->booking_code.'|'.$booking->guest_name.'|'.$booking->check_in_date->toDateString()),
                width: 120, height: 120, colorDark: '#1E293B', colorLight: '#FFFFFF',
            });
        </script>
    @endpush
@endif
