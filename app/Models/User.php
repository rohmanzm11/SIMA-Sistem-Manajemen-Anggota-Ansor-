<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'anggota_id',
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Relasi ke Anggota
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    /**
     * Relasi ke Verifikasi Anggota (sebagai verifikator)
     */
    public function verifikasiDilakukan(): HasMany
    {
        return $this->hasMany(VerifikasiAnggota::class, 'verifikator_id');
    }

    /**
     * Relasi ke Print Log (sebagai pencetak)
     */
    public function printLogs(): HasMany
    {
        return $this->hasMany(PrintLog::class, 'dicetak_oleh');
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    /**
     * Check if user is verifikator
     */
    public function isVerifikator(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'verifikator']);
    }

    /**
     * Scope untuk filter user aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk filter berdasarkan role
     */
    // Menjadi ini (nama berbeda):
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
