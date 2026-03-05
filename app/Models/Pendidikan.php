<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendidikan extends Model
{
    use HasFactory;

    protected $fillable = [
        'anggota_id',
        'jenjang',
        'nama_institusi',
        'jurusan',
        'tahun_masuk',
        'tahun_lulus',
        'status',
    ];

    /**
     * Relasi ke Anggota
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    /**
     * Scope untuk filter pendidikan tertinggi
     */
    public function scopeHighestEducation($query, $anggotaId)
    {
        $hierarchy = ['S3', 'S2', 'S1', 'D4', 'D3', 'D2', 'D1', 'MA', 'SMK', 'SMA', 'SMP', 'SD', 'Pesantren', 'Diniyyah'];

        return $query->where('anggota_id', $anggotaId)
            ->orderByRaw('FIELD(jenjang, "' . implode('","', $hierarchy) . '")')
            ->first();
    }
}
