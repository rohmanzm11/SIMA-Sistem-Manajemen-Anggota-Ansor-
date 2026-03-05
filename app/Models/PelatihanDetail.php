<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PelatihanDetail extends Model
{
    use HasFactory;

    protected $table = 'pelatihan_details';

    protected $fillable = [
        'pelatihan_id',
        'materi_id',
        'tempat',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'pengajar',
        'is_active',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Pelatihan
     */
    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(Pelatihan::class, 'pelatihan_id');
    }

    /**
     * Relasi ke Materi
     */
    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }

    /**
     * Relasi ke Anggota Pelatihan
     */
    public function anggotaPelatihan(): HasMany
    {
        return $this->hasMany(AnggotaPelatihan::class, 'pelatihan_detail_id');
    }

    /**
     * Relasi ke Print Log
     */
    public function printLogs(): HasMany
    {
        return $this->hasMany(PrintLog::class, 'pelatihan_detail_id');
    }
}
