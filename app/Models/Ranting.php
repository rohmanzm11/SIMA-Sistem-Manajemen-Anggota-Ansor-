<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Ranting extends Model
{
    use HasFactory;

    protected $fillable = [
        'pac_id',
        'nama_ranting',
    ];

    /**
     * Relasi ke PAC
     */
    public function pac(): BelongsTo
    {
        return $this->belongsTo(Pac::class, 'pac_id');
    }

    /**
     * Polymorphic relation ke Level
     */
    public function levels(): MorphMany
    {
        return $this->morphMany(Level::class, 'levelable', 'level_type', 'reference_id');
    }
}
