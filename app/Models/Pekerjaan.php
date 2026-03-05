<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pekerjaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pekerjaan',
    ];

    /**
     * Relasi ke Anggota
     */
    public function anggotas(): HasMany
    {
        return $this->hasMany(Anggota::class, 'pekerjaan_id');
    }
}
