<?php

// [Anggota 1] Pesan validasi Bahasa Indonesia (APP_LOCALE=id)

return [
    'accepted' => ':Attribute harus disetujui.',
    'after' => ':Attribute harus tanggal setelah :date.',
    'after_or_equal' => ':Attribute harus tanggal setelah atau sama dengan :date.',
    'array' => ':Attribute harus berupa daftar.',
    'before' => ':Attribute harus tanggal sebelum :date.',
    'boolean' => ':Attribute harus bernilai benar atau salah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Kata sandi saat ini salah.',
    'date' => ':Attribute bukan tanggal yang valid.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'exists' => ':Attribute yang dipilih tidak valid.',
    'image' => ':Attribute harus berupa gambar.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'integer' => ':Attribute harus berupa bilangan bulat.',
    'max' => [
        'numeric' => ':Attribute maksimal :max.',
        'string' => ':Attribute maksimal :max karakter.',
        'array' => ':Attribute maksimal :max item.',
        'file' => ':Attribute maksimal :max kilobyte.',
    ],
    'min' => [
        'numeric' => ':Attribute minimal :min.',
        'string' => ':Attribute minimal :min karakter.',
        'array' => ':Attribute minimal :min item.',
        'file' => ':Attribute minimal :min kilobyte.',
    ],
    'numeric' => ':Attribute harus berupa angka.',
    'password' => [
        'letters' => ':Attribute harus mengandung minimal satu huruf.',
        'mixed' => ':Attribute harus mengandung huruf besar dan kecil.',
        'numbers' => ':Attribute harus mengandung minimal satu angka.',
        'symbols' => ':Attribute harus mengandung minimal satu simbol.',
        'uncompromised' => ':Attribute ini pernah bocor di internet, gunakan yang lain.',
    ],
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':Attribute wajib diisi.',
    'required_if' => ':Attribute wajib diisi bila :other adalah :value.',
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah digunakan.',
    'url' => 'Format :attribute tidak valid.',

    'attributes' => [
        'name' => 'nama',
        'phone' => 'nomor telepon',
        'password' => 'kata sandi',
        'current_password' => 'kata sandi saat ini',
        'reason' => 'alasan',
    ],
];
