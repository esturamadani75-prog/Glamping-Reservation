{{-- [Anggota 3] Edit data pribadi & kata sandi --}}
@extends('layouts.app')
@section('title', 'Profil')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <p class="eyebrow">Akun Saya</p>
    <h1 class="section-title mt-2">Profil & keamanan</h1>
    @include('user._nav')

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <form method="POST" action="{{ route('user.profile.update') }}" class="card space-y-5 p-6">
            @csrf @method('PUT')
            <h2 class="font-semibold">Data pribadi</h2>
            <div>
                <label class="label">Nama lengkap</label>
                <input name="name" value="{{ old('name', $user->name) }}" class="input @error('name') input-error @enderror" required>
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input @error('email') input-error @enderror" required>
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label">No. Telepon</label>
                <input name="phone" value="{{ old('phone', $user->phone) }}" class="input @error('phone') input-error @enderror">
                @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <p class="text-xs text-slate-500">Terdaftar sejak {{ tanggal($user->created_at, 'd F Y') }}</p>
            <button class="btn-primary">Simpan Perubahan</button>
        </form>

        <form method="POST" action="{{ route('user.password.update') }}" class="card space-y-5 p-6">
            @csrf @method('PUT')
            <h2 class="font-semibold">Ganti kata sandi</h2>
            <div>
                <label class="label">Kata sandi saat ini</label>
                <input type="password" name="current_password" class="input @error('current_password') input-error @enderror" required>
                @error('current_password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label">Kata sandi baru</label>
                <input type="password" name="password" class="input @error('password') input-error @enderror" required>
                @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label">Ulangi kata sandi baru</label>
                <input type="password" name="password_confirmation" class="input" required>
            </div>
            <button class="btn-outline">Ganti Kata Sandi</button>
        </form>
    </div>
</div>
@endsection
