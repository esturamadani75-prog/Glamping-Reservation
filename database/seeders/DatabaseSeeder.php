<?php

// [Anggota 1] Seeder utama — urutan pemanggilan penting (user & unit dulu)

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            GlampingUnitSeeder::class,
            ArticleSeeder::class,
            BookingSeeder::class,   // data dummy agar dashboard & grafik terisi
        ]);
    }
}
