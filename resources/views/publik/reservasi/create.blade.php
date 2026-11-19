{{-- [Anggota 2] Form reservasi + simulasi harga & sisa stok real-time (Alpine.js) --}}
@extends('layouts.app')
@section('title', 'Reservasi')

@php
    $unitData = $units->map(fn ($u) => [
        'id' => $u->id, 'name' => $u->name, 'category' => $u->category, 'capacity' => $u->capacity,
        'price' => $u->price_per_night, 'stock' => $u->stock_total, 'facilities' => $u->facilities ?? [],
    ])->values();
    $user = auth()->user();
@endphp

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"
     x-data="reservationForm({
        units: @js($unitData),
        unitId: {{ old('glamping_unit_id', $selected?->id ?? 0) }},
        checkIn: '{{ old('check_in_date', $checkIn) }}',
        checkOut: '{{ old('check_out_date', $checkOut) }}',
        qty: {{ (int) old('unit_qty', 1) }},
        insurance: {{ old('has_insurance') ? 'true' : 'false' }},
        endpoint: '{{ route('booking.availability') }}',
     })" x-init="refresh()">

    <p class="eyebrow">Reservasi</p>
    <h1 class="section-title mt-2">Pesan unit glamping</h1>
    <p class="mt-2 text-slate-600">Check-in mulai {{ config('glamping.check_in_time') }} WIB · Check-out paling lambat {{ config('glamping.check_out_time') }} WIB</p>

    <form method="POST" action="{{ route('booking.store') }}" class="mt-8 grid gap-8 lg:grid-cols-3">
        @csrf
        <div class="space-y-6 lg:col-span-2">
            {{-- 1. Pilih unit --}}
            <section class="card p-6">
                <h2 class="font-semibold"><span class="text-harvest">01</span> · Pilih unit glamping</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    <template x-for="u in units" :key="u.id">
                        <label class="cursor-pointer rounded-xl border-2 p-4 transition"
                               :class="unitId == u.id ? 'border-paddy bg-paddy-50' : 'border-slate-200 hover:border-paddy-200'">
                            <input type="radio" name="glamping_unit_id" :value="u.id" x-model.number="unitId" @change="refresh()" class="sr-only">
                            <span class="block text-xs font-semibold uppercase tracking-wide text-slate-500" x-text="u.category"></span>
                            <span class="mt-1 block font-semibold leading-snug" x-text="u.name"></span>
                            <span class="mt-2 block text-sm text-slate-500" x-text="'👥 ' + u.capacity"></span>
                            <span class="mt-2 block font-bold text-paddy" x-text="rupiah(u.price) + '/malam'"></span>
                        </label>
                    </template>
                </div>
                <ul class="mt-4 flex flex-wrap gap-1.5" x-show="unit">
                    <template x-for="f in unit?.facilities ?? []"><li class="rounded-lg bg-canvas-dark px-2 py-1 text-xs" x-text="f"></li></template>
                </ul>
                @error('glamping_unit_id')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
            </section>

            {{-- 2. Tanggal & jumlah --}}
            <section class="card p-6">
                <h2 class="font-semibold"><span class="text-harvest">02</span> · Tanggal & jumlah unit</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="label">Check-in</label>
                        <input type="date" name="check_in_date" x-model="checkIn" @change="fixDates(); refresh()" min="{{ today()->toDateString() }}" class="input @error('check_in_date') input-error @enderror" required>
                        @error('check_in_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Check-out</label>
                        <input type="date" name="check_out_date" x-model="checkOut" @change="refresh()" :min="minCheckOut" class="input @error('check_out_date') input-error @enderror" required>
                        @error('check_out_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Jumlah unit</label>
                        <input type="number" name="unit_qty" x-model.number="qty" @input.debounce.300ms="refresh()" min="1" :max="unit?.stock" class="input @error('unit_qty') input-error @enderror" required>
                        @error('unit_qty')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- indikator stok --}}
                <div class="mt-4 rounded-xl px-4 py-3 text-sm" :class="stockClass">
                    <template x-if="loading"><span>Mengecek ketersediaan…</span></template>
                    <template x-if="!loading && quote">
                        <span>
                            Sisa stok <b x-text="unit?.name"></b> untuk tanggal ini:
                            <b x-text="quote.remaining + ' dari ' + quote.stock_total + ' unit'"></b>
                            <span x-show="quote.remaining < qty"> — stok tidak mencukupi.</span>
                        </span>
                    </template>
                    <template x-if="!loading && !quote"><span>Pilih tanggal check-out setelah check-in.</span></template>
                </div>
            </section>

            {{-- 3. Data kontak --}}
            <section class="card p-6">
                <h2 class="font-semibold"><span class="text-harvest">03</span> · Data pemesan</h2>
                @guest
                    <p class="mt-1 text-sm text-slate-500"><a href="{{ route('login') }}" class="font-semibold text-paddy">Masuk</a> agar reservasi tersimpan di riwayat akun Anda.</p>
                @endguest
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="label">Nama lengkap</label>
                        <input name="guest_name" value="{{ old('guest_name', $user?->name) }}" class="input @error('guest_name') input-error @enderror" required>
                        @error('guest_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Email</label>
                        <input type="email" name="guest_email" value="{{ old('guest_email', $user?->email) }}" class="input @error('guest_email') input-error @enderror" required>
                        @error('guest_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">No. Telepon / WhatsApp</label>
                        <input name="guest_phone" value="{{ old('guest_phone', $user?->phone) }}" placeholder="08xxxxxxxxxx" class="input @error('guest_phone') input-error @enderror" required>
                        @error('guest_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            {{-- 4. Asuransi --}}
            <label class="card flex cursor-pointer gap-4 p-6 transition" :class="insurance ? 'ring-2 ring-harvest' : ''">
                <input type="checkbox" name="has_insurance" value="1" x-model="insurance" @change="refresh()" class="mt-1 h-5 w-5 rounded border-slate-300 text-harvest focus:ring-harvest">
                <span>
                    <span class="block font-semibold">Tambah Asuransi Pembatalan <span class="text-harvest">+ Rp 50.000</span></span>
                    <span class="mt-1 block text-sm text-slate-600">
                        Batal &gt; 3 hari sebelum check-in: <b>refund 100%</b> biaya kamar. Batal ≤ 3 hari: <b>refund 50%</b>.
                        Tanpa asuransi, biaya kamar tidak dapat dikembalikan.
                    </span>
                </span>
            </label>
        </div>

        {{-- Ringkasan biaya --}}
        <aside class="lg:sticky lg:top-24 lg:self-start">
            <div class="card overflow-hidden">
                <div class="bg-merapi p-6 text-white">
                    <p class="text-xs uppercase tracking-widest text-slate-400">Ringkasan</p>
                    <p class="mt-1 font-display text-xl" x-text="unit?.name ?? '-'"></p>
                    <p class="mt-1 text-sm text-slate-300"><span x-text="formatDate(checkIn)"></span> → <span x-text="formatDate(checkOut)"></span></p>
                </div>
                <dl class="space-y-3 p-6 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Tarif / malam</dt><dd x-text="rupiah(unit?.price ?? 0)"></dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Jumlah malam</dt><dd x-text="(quote?.nights ?? 0) + ' malam'"></dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Jumlah unit</dt><dd x-text="qty + ' unit'"></dd></div>
                    <div class="flex justify-between border-t border-dashed pt-3"><dt class="text-slate-500">Biaya kamar</dt><dd x-text="rupiah(quote?.room_total ?? 0)"></dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Asuransi pembatalan</dt><dd x-text="rupiah(quote?.insurance_fee ?? 0)"></dd></div>
                    <div class="flex items-baseline justify-between border-t pt-3"><dt class="font-semibold">Total</dt><dd class="text-2xl font-bold text-paddy" x-text="rupiah(quote?.total_price ?? 0)"></dd></div>
                </dl>
                <div class="px-6 pb-6">
                    <button class="btn-accent w-full py-3 text-base" :disabled="loading || !quote || quote.remaining < qty">Lanjut ke Pembayaran</button>
                    <p class="mt-3 text-center text-xs text-slate-500">Rumus: tarif × jumlah unit × jumlah malam</p>
                </div>
            </div>
        </aside>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function reservationForm(cfg) {
        return {
            ...cfg,
            quote: null,
            loading: false,
            get unit() { return this.units.find(u => u.id == this.unitId); },
            get minCheckOut() {
                const d = new Date(this.checkIn); d.setDate(d.getDate() + 1);
                return isNaN(d) ? '' : d.toISOString().slice(0, 10);
            },
            get stockClass() {
                if (this.loading || !this.quote) return 'bg-slate-50 text-slate-600';
                return this.quote.remaining >= this.qty ? 'bg-paddy-50 text-paddy-800' : 'bg-red-50 text-red-700';
            },
            fixDates() {
                if (!this.checkOut || this.checkOut <= this.checkIn) this.checkOut = this.minCheckOut;
            },
            async refresh() {
                if (!this.unitId || !this.checkIn || !this.checkOut || this.checkOut <= this.checkIn) { this.quote = null; return; }
                this.loading = true;
                const params = new URLSearchParams({ unit: this.unitId, check_in: this.checkIn, check_out: this.checkOut, qty: this.qty || 1, insurance: this.insurance ? 1 : 0 });
                try {
                    const res = await fetch(this.endpoint + '?' + params, { headers: { 'Accept': 'application/json' } });
                    this.quote = res.ok ? await res.json() : null;
                } catch (e) { this.quote = null; }
                this.loading = false;
            },
            rupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); },
            formatDate(s) {
                if (!s) return '-';
                return new Date(s).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            },
        };
    }
</script>
@endpush
