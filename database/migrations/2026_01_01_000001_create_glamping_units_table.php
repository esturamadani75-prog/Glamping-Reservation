<?php

// [Anggota 1] Tabel jenis kamar / tenda glamping

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('glamping_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category');                       // Couple/Standard, Deluxe, Family/Rombongan
            $table->string('capacity');                       // contoh: "2 Dewasa", "3-4 Dewasa"
            $table->unsignedInteger('stock_total');           // jumlah unit fisik
            $table->unsignedBigInteger('price_per_night');    // tarif harian (Rupiah)
            $table->json('facilities')->nullable();           // daftar fasilitas
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('glamping_units');
    }
};
