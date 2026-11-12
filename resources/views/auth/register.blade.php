{{-- [Anggota 1] Halaman registrasi user --}}
@extends('layouts.app')
@section('title', 'Daftar Akun')

@section('content')
<div class="px-4">
<div class="mx-auto max-w-xl rounded-3xl border border-slate-200/70 bg-white p-8 shadow-sm sm:p-10">
    <p class="eyebrow">Akun baru</p>
    <h1 class="mt-2 font-display text-3xl font-semibold">Daftar & simpan riwayat sewa</h1>
    <p class="mt-2 text-sm text-slate-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-paddy hover:underline">Masuk</a></p>

    <form method="POST" action="{{ route('register.store') }}" class="mt-8 grid gap-5 sm:grid-cols-2">
        @csrf
        <div class="sm:col-span-2">
            <label class="label" for="name">Nama lengkap</label>
            <input id="name" name="name" value="{{ old('name') }}" required class="input @error('name') input-error @enderror">
            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label" for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="input @error('email') input-error @enderror">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label" for="phone">No. Telepon / WhatsApp</label>
            <input id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required class="input @error('phone') input-error @enderror">
            @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label" for="password">Kata sandi</label>
            <input id="password" type="password" name="password" required class="input @error('password') input-error @enderror">
            @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label" for="password_confirmation">Ulangi kata sandi</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="input">
        </div>
        <p class="text-xs text-slate-500 sm:col-span-2">Minimal 8 karakter, kombinasi huruf dan angka.</p>
        <button class="btn-primary sm:col-span-2">Buat Akun</button>
    </form>
</div>
</div>
@endsection
