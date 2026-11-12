<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class StaffAuditLog extends Model
{
    public const UPDATED_AT = null;   

    public const ACTIONS = [
        'cancellation.approve' => 'Setujui Pembatalan',
        'cancellation.reject' => 'Tolak Pembatalan',
        'booking.complete' => 'Booking Selesai',
        'unit.create' => 'Tambah Unit',
        'unit.update' => 'Ubah Unit (Stok/Tarif)',
        'unit.delete' => 'Hapus Unit',
        'article.create' => 'Tambah Artikel',
        'article.update' => 'Ubah Artikel',
        'article.delete' => 'Hapus Artikel',
        'user.create' => 'Tambah Akun',
        'user.update' => 'Ubah Akun',
        'user.delete' => 'Hapus Akun',
    ];

    protected $fillable = ['user_id', 'action', 'description'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Catat aktivitas penting. Contoh: StaffAuditLog::record('unit.update', 'Ubah tarif ...'); */
    public static function record(string $action, string $description): self
    {
        return static::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
        ]);
    }

    public function actionLabel(): string
    {
        return self::ACTIONS[$this->action] ?? $this->action;
    }
}
