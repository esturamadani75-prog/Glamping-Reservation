<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cancellation extends Model
{
    public const STATUSES = ['pending', 'approved', 'rejected'];

    protected $fillable = [
        'booking_id',
        'reason',
        'refund_percentage',
        'refund_amount',
        'status',
        'staff_note',
        'processed_by',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'refund_percentage' => 'integer',
            'refund_amount' => 'integer',
            'processed_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
