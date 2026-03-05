<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kecamatan',
    ];

    /**
     * Relasi ke Desa
     */
    public function desas(): HasMany
    {
        return $this->hasMany(Desa::class, 'kecamatan_id');
    }

    /**
     * Relasi ke Anggota
     */
    public function anggotas(): HasMany
    {
        return $this->hasMany(Anggota::class, 'kecamatan_id');
    }
}
