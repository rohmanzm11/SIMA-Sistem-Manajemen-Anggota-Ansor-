<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelatihan extends Model
{
    use HasFactory;

    protected $table = 'pelatihans';

    protected $fillable = [
        'nama_pelatihan',
        'deskripsi',
    ];

    /**
     * Relasi ke Pelatihan Detail
     */
    public function pelatihanDetails(): HasMany
    {
        return $this->hasMany(PelatihanDetail::class, 'pelatihan_id');
    }
}
