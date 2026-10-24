<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kta extends Model
{
    use HasFactory;

    protected $table = 'ktas';

    protected $fillable = [
        'nama_batch',
        'nama_ketua',
        'ttd_ketua',
        'nama_sekretaris',
        'ttd_sekretaris',
        'nomor_kta',
        'image',
        'tanggal_terbit',
        'tanggal_berlaku_sampai',
        'is_active',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_berlaku_sampai' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Anggota
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    /**
     * Relasi ke Print Log
     */
    public function printLogs(): HasMany
    {
        return $this->hasMany(PrintLog::class, 'kta_id');
    }

    /**
     * Scope untuk filter KTA aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk filter KTA yang sudah kadaluarsa
     */
    public function scopeExpired($query)
    {
        return $query->where('tanggal_berlaku_sampai', '<', now());
    }

    /**
     * Scope untuk filter KTA yang akan kadaluarsa dalam X hari
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereBetween('tanggal_berlaku_sampai', [
            now(),
            now()->addDays($days)
        ]);
    }

    /**
     * Check if KTA is expired
     */
    public function isExpired(): bool
    {
        return $this->tanggal_berlaku_sampai < now();
    }

    /**
     * Check if KTA is valid
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }
}
