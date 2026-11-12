<?php

// [Anggota 1] Akun demo wajib

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'Admin Merapi', 'email' => 'admin@demo.test', 'password' => 'Admin#2026', 'role' => 'admin', 'phone' => '081200000001'],
            ['name' => 'Staff Resepsionis', 'email' => 'staff@demo.test', 'password' => 'Staff#2026', 'role' => 'staff', 'phone' => '081200000002'],
            ['name' => 'Rina Wisatawan', 'email' => 'user@demo.test', 'password' => 'User#2026', 'role' => 'user', 'phone' => '081200000003'],
        ];

        foreach ($accounts as $acc) {
            User::updateOrCreate(
                ['email' => $acc['email']],
                [
                    'name' => $acc['name'],
                    'phone' => $acc['phone'],
                    'role' => $acc['role'],
                    'password' => Hash::make($acc['password']),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
