<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Pc extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pc',
    ];

    /**
     * Relasi ke PAC
     */
    public function pacs(): HasMany
    {
        return $this->hasMany(Pac::class, 'pc_id');
    }

    /**
     * Polymorphic relation ke Level
     */
    public function levels(): MorphMany
    {
        return $this->morphMany(Level::class, 'levelable', 'level_type', 'reference_id');
    }
}
