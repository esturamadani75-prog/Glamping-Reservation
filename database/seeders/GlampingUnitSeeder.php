<?php

// [Anggota 1] Data 3 tipe unit glamping sesuai profil bisnis

namespace Database\Seeders;

use App\Models\GlampingUnit;
use Illuminate\Database\Seeder;

class GlampingUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            [
                'name' => 'Standard River View Tent',
                'slug' => 'standard-river-view-tent',
                'category' => 'Couple/Standard',
                'capacity' => '2 Dewasa',
                'stock_total' => 8,
                'price_per_night' => 500000,
                'facilities' => ['King Bed', 'AC', 'Smart TV', 'WiFi', 'View Sungai & Sawah', 'Sarapan 2 Orang'],
                'description' => 'Tenda glamping romantis untuk berdua, menghadap aliran sungai dan hamparan sawah hijau. Cocok untuk pasangan yang ingin rehat dari hiruk-pikuk kota.',
            ],
            [
                'name' => 'Deluxe Merapi View (Balkon)',
                'slug' => 'deluxe-merapi-view-balkon',
                'category' => 'Deluxe',
                'capacity' => '3-4 Dewasa',
                'stock_total' => 6,
                'price_per_night' => 650000,
                'facilities' => ['1 King + 1 Single Bed', 'Balkon View Gunung Merapi', 'AC', 'Smart TV', 'Pembuat Kopi/Teh', 'Sarapan'],
                'description' => 'Bangun pagi langsung disapa Gunung Merapi dari balkon pribadi. Ruang lebih lega untuk keluarga kecil atau sahabat.',
            ],
            [
                'name' => 'Family Glamping Suite',
                'slug' => 'family-glamping-suite',
                'category' => 'Family/Rombongan',
                'capacity' => 'hingga 6 Dewasa',
                'stock_total' => 4,
                'price_per_night' => 950000,
                'facilities' => ['Luas 36m²', 'Ruang Keluarga', 'Teras Sawah', 'Akses Kolam Renang & Pemancingan', 'Perlengkapan BBQ', 'Sarapan'],
                'description' => 'Suite terluas untuk keluarga besar atau rombongan, lengkap dengan teras sawah, perlengkapan BBQ, serta akses kolam renang dan pemancingan.',
            ],
        ];

        foreach ($units as $unit) {
            GlampingUnit::updateOrCreate(['slug' => $unit['slug']], $unit + ['is_active' => true]);
        }
    }
}
