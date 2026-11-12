<?php

// [Anggota 1] Tabel pengajuan pembatalan

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->text('reason');
            $table->unsignedTinyInteger('refund_percentage')->default(0);
            $table->unsignedBigInteger('refund_amount')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->text('staff_note')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cancellations');
    }
};
