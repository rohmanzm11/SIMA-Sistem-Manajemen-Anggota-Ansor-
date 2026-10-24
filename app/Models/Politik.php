<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Politik extends Model
{
    use HasFactory;

    protected $fillable = [
        'partai_politik',
    ];

    /**
     * Relasi ke Anggota
     */
    public function anggotas(): HasMany
    {
        return $this->hasMany(Anggota::class, 'politik_id');
    }
}
