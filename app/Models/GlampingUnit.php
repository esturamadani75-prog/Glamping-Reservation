<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GlampingUnit extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'capacity',
        'stock_total',
        'price_per_night',
        'facilities',
        'description',
        'image_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'facilities' => 'array',
            'is_active' => 'boolean',
            'stock_total' => 'integer',
            'price_per_night' => 'integer',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Warna gradient kartu berdasarkan kategori (dipakai di tampilan). */
    public function themeGradient(): string
    {
        return match (true) {
            str_contains(strtolower($this->category), 'deluxe') => 'from-amber-600 to-orange-800',
            str_contains(strtolower($this->category), 'family') => 'from-slate-700 to-slate-900',
            default => 'from-emerald-600 to-emerald-900',
        };
    }
}
