<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Booking extends Model
{
    public const STATUSES = ['pending', 'confirmed', 'cancelled', 'completed'];

    public const ACTIVE_STATUSES = ['pending', 'confirmed'];

    public const INSURANCE_FEE = 50000;

    protected $fillable = [
        'booking_code',
        'user_id',
        'glamping_unit_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'check_in_date',
        'check_out_date',
        'unit_qty',
        'room_total',
        'has_insurance',
        'insurance_fee',
        'total_price',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'has_insurance' => 'boolean',
            'paid_at' => 'datetime',
            'unit_qty' => 'integer',
            'room_total' => 'integer',
            'insurance_fee' => 'integer',
            'total_price' => 'integer',
        ];
    }

    // ---------- Relasi ----------
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(GlampingUnit::class, 'glamping_unit_id');
    }

    public function cancellations(): HasMany
    {
        return $this->hasMany(Cancellation::class);
    }

    /** Pengajuan pembatalan terakhir. */
    public function latestCancellation(): HasOne
    {
        return $this->hasOne(Cancellation::class)->latestOfMany();
    }

    // ---------- Scope ----------
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    /** Booking yang menempati unit pada rentang [checkIn, checkOut). */
    public function scopeOverlapping(Builder $query, $checkIn, $checkOut): Builder
    {
        return $query->whereDate('check_in_date', '<', $checkOut)
            ->whereDate('check_out_date', '>', $checkIn);
    }

    // ---------- Helper ----------
    public function nights(): int
    {
        return (int) $this->check_in_date->diffInDays($this->check_out_date);
    }

    public static function generateCode(): string
    {
        do {
            $code = 'MGTS-'.strtoupper(Str::random(6));
        } while (static::where('booking_code', $code)->exists());

        return $code;
    }

    /** Pemilik booking: user yang login atau email tamu yang sama. */
    public function isOwnedBy(?User $user): bool
    {
        return $user && ($this->user_id === $user->id || strcasecmp($this->guest_email, $user->email) === 0);
    }
}
