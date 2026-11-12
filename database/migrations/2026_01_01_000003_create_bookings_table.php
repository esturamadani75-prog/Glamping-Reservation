<?php

// [Anggota 1] Tabel reservasi

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 20)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();   // null = tamu tanpa akun
            $table->foreignId('glamping_unit_id')->constrained()->restrictOnDelete();
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone', 20);
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedInteger('unit_qty')->default(1);
            $table->unsignedBigInteger('room_total')->default(0);    // price_per_night * unit_qty * malam
            $table->boolean('has_insurance')->default(false);
            $table->unsignedBigInteger('insurance_fee')->default(0);
            $table->unsignedBigInteger('total_price');               // room_total + insurance_fee
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending')->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['glamping_unit_id', 'check_in_date', 'check_out_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
