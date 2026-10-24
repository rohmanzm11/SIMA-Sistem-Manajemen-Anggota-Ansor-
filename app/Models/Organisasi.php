<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisasi extends Model
{
    use HasFactory;

    protected $table = 'organisasis';

    protected $fillable = [
        'nama_organisasi',
        'jenis',
    ];

    /**
     * Relasi ke Anggota Organisasi
     */
    public function anggotaOrganisasi(): HasMany
    {
        return $this->hasMany(AnggotaOrganisasi::class, 'organisasi_id');
    }
}
