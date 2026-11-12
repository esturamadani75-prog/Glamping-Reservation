<?php

return [
    'name' => 'Merapi Glamping Tengah Sawah',
    'tagline' => 'Bangun Pagi Disapa Merapi, Malam Tidur di Tengah Sawah',
    'address' => 'Jl. Panggung No. 86, Jaranan, Argomulyo, Kec. Cangkringan, Sleman, DI Yogyakarta 55584',
    'check_in_time' => '14:00',
    'check_out_time' => '12:00',

    // Kontak dummy untuk keperluan tugas — ganti dengan kontak resmi bila ada
    'phone' => env('GLAMPING_PHONE', '+62 812-0000-8686'),
    'email' => env('GLAMPING_EMAIL', 'reservasi@merapiglamping.test'),

    'maps_query' => 'Merapi Glamping Tengah Sawah, Argomulyo, Cangkringan, Sleman',

    // Aturan bisnis
    'insurance_fee' => 50000,        // biaya asuransi pembatalan per booking
    'max_nights' => 14,              // maksimal lama menginap per booking
];
