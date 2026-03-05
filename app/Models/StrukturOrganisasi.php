<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StrukturOrganisasi extends Model
{
    use HasFactory;

    protected $table = 'struktur_organisasis';

    protected $fillable = [
        'anggota_id',
        'level_id',
        'jabatan_id',
        'organisasi_id',
        'nama_organisasi',
        'masa_khidmat_mulai',
        'masa_khidmat_selesai',
        'is_active',
    ];

    protected $casts = [
        'masa_khidmat_mulai' => 'date',
        'masa_khidmat_selesai' => 'date',
        'sk_tanggal' => 'date',
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
     * Relasi ke Level
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    /**
     * Relasi ke Jabatan
     */
    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    /**
     * Scope untuk filter jabatan aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk filter berdasarkan level type
     */
    public function scopeByLevelType($query, $levelType)
    {
        return $query->whereHas('level', function ($q) use ($levelType) {
            $q->where('level_type', $levelType);
        });
    }
    public function organisasi(): BelongsTo
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id');
    }
}
