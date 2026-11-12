{{-- [Anggota 1] Halaman login --}}
@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="px-4">
<div class="mx-auto grid max-w-5xl overflow-hidden rounded-3xl border border-slate-200/70 bg-white shadow-sm md:grid-cols-2">
    <div class="relative hidden md:block">
        <x-landscape variant="dusk" class="absolute inset-0" />
        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-merapi/80 to-transparent p-8 text-white">
            <p class="font-display text-2xl leading-snug">Bangun pagi disapa Merapi, malam tidur di tengah sawah.</p>
        </div>
    </div>

    <div class="p-8 sm:p-10">
        <p class="eyebrow">Selamat datang</p>
        <h1 class="mt-2 font-display text-3xl font-semibold">Masuk ke akun</h1>
        <p class="mt-2 text-sm text-slate-500">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-paddy hover:underline">Daftar di sini</a></p>

        <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="input @error('email') input-error @enderror">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div x-data="{ show: false }">
                <label for="password" class="label">Kata sandi</label>
                <div class="relative">
                    <input id="password" name="password" :type="show ? 'text' : 'password'" required class="input pr-16">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 text-xs font-semibold text-slate-500" x-text="show ? 'Sembunyi' : 'Lihat'"></button>
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-paddy focus:ring-paddy"> Ingat saya
            </label>
            <button class="btn-primary w-full">Masuk</button>
        </form>

        {{-- Akun demo (memudahkan penguji / dosen) --}}
        <div x-data class="mt-8 rounded-2xl bg-canvas p-4 text-xs">
            <p class="mb-2 font-semibold text-slate-600">Akun demo — klik untuk mengisi otomatis:</p>
            <div class="grid gap-2 sm:grid-cols-3">
                @foreach ([['Admin', 'admin@demo.test', 'Admin#2026'], ['Staff', 'staff@demo.test', 'Staff#2026'], ['User', 'user@demo.test', 'User#2026']] as [$role, $mail, $pass])
                    <button type="button" class="rounded-lg border border-slate-200 bg-white px-2 py-2 text-left hover:border-paddy"
                            @click="document.getElementById('email').value='{{ $mail }}'; document.getElementById('password').value='{{ $pass }}'">
                        <span class="block font-bold text-paddy">{{ $role }}</span>
                        <span class="block truncate text-slate-500">{{ $mail }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>
</div>
@endsection
