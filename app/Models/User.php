<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLES = ['admin', 'staff', 'user'];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ---------- Relasi ----------
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(StaffAuditLog::class);
    }

    // ---------- Helper role ----------
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /** Staff & admin sama-sama boleh membuka panel staff. */
    public function canAccessPanel(): bool
    {
        return $this->hasRole('staff', 'admin');
    }

    /** Halaman tujuan setelah login sesuai role. */
    public function homeRoute(): string
    {
        return $this->canAccessPanel() ? route('staff.dashboard') : route('user.dashboard');
    }
}
