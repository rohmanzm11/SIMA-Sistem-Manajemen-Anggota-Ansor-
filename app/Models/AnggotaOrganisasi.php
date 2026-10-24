<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggotaOrganisasi extends Model
{
    use HasFactory;

    protected $table = 'anggota_organisasis';

    protected $fillable = [
        'anggota_id',
        'organisasi_id',
        'jabatan',
        'tahun_masuk',
        'tahun_keluar',
        'is_active',
    ];

    protected $casts = [
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
     * Relasi ke Organisasi
     */
    public function organisasi(): BelongsTo
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id');
    }
}
