<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintLog extends Model
{
    use HasFactory;

    protected $table = 'print';

    protected $fillable = [
        'jenis_cetakan',
        'anggota_id',
        'pelatihan_detail_id',
        'kta_id',
        'template_sertifikat_id',
        'tanggal_cetak',
        'dicetak_oleh',
    ];

    protected $casts = [
        'tanggal_cetak' => 'datetime',
    ];

    /**
     * Relasi ke Anggota
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    /**
     * Relasi ke Pelatihan Detail (untuk sertifikat)
     */
    public function pelatihanDetail(): BelongsTo
    {
        return $this->belongsTo(PelatihanDetail::class, 'pelatihan_detail_id');
    }
    /**
     * Relasi ke Template Sertifikat
     */
    public function templateSertifikat(): BelongsTo
    {
        return $this->belongsTo(TemplateSertifikat::class, 'template_sertifikat_id');
    }

    /**
     * Relasi ke KTA
     */
    public function kta(): BelongsTo
    {
        return $this->belongsTo(Kta::class, 'kta_id');
    }

    /**
     * Relasi ke User yang mencetak
     */
    public function pencetak(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicetak_oleh');
    }
    // public function dicetakOleh(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'dicetak_oleh');
    // }

    /**
     * Scope untuk filter berdasarkan jenis cetakan
     */
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_cetakan', $jenis);
    }

    /**
     * Scope untuk filter cetakan KTA
     */
    public function scopeKta($query)
    {
        return $query->where('jenis_cetakan', 'KTA');
    }

    /**
     * Scope untuk filter cetakan Sertifikat
     */
    public function scopeSertifikat($query)
    {
        return $query->where('jenis_cetakan', 'Sertifikat');
    }

    /**
     * Scope untuk filter cetakan Kartu Anggota
     */
    public function scopeKartuAnggota($query)
    {
        return $query->where('jenis_cetakan', 'Kartu Anggota');
    }
}
