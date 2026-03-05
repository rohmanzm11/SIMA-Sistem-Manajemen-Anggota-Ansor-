<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggotaPelatihan extends Model
{
    use HasFactory;

    protected $table = 'anggota_pelatihans';

    protected $fillable = [
        'anggota_id',
        'pelatihan_detail_id',
        'status_kehadiran',
        'skor',
        'keterangan',
        'sertifikat_nomor',
        'sertifikat_path',
        'tanggal_terbit_sertifikat',
    ];

    protected $casts = [
        'skor' => 'decimal:2',
        'tanggal_terbit_sertifikat' => 'date',
    ];

    /**
     * Relasi ke Anggota
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    /**
     * Relasi ke Pelatihan Detail
     */
    public function pelatihanDetail(): BelongsTo
    {
        return $this->belongsTo(PelatihanDetail::class, 'pelatihan_detail_id');
    }

    /**
     * Scope untuk filter yang hadir
     */
    public function scopeHadir($query)
    {
        return $query->where('status_kehadiran', 'Hadir');
    }

    /**
     * Scope untuk filter yang tidak hadir
     */
    public function scopeTidakHadir($query)
    {
        return $query->where('status_kehadiran', 'Tidak Hadir');
    }

    /**
     * Scope untuk filter yang sudah dapat sertifikat
     */
    public function scopeHasSertifikat($query)
    {
        return $query->whereNotNull('sertifikat_nomor');
    }

    /**
     * Check if passed the training
     */
    public function isPassed(): bool
    {
        if (!$this->skor) {
            return false;
        }

        // Get skor maksimal from materi
        $skorMaksimal = $this->pelatihanDetail->materi->skor_maksimal ?? 100;
        $skorLulus = $skorMaksimal * 0.6; // 60% untuk lulus

        return $this->skor >= $skorLulus;
    }
}
