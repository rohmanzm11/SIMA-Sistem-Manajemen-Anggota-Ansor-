<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materi extends Model
{
    use HasFactory;

    protected $table = 'materis';

    protected $fillable = [
        'nama_materi',
        'deskripsi',
        'skor_maksimal',
    ];

    protected $casts = [
        'skor_maksimal' => 'decimal:2',
    ];

    /**
     * Relasi ke Pelatihan Detail
     */
    public function pelatihanDetails(): HasMany
    {
        return $this->hasMany(PelatihanDetail::class, 'materi_id');
    }
}
