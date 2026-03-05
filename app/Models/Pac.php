<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Pac extends Model
{
    use HasFactory;

    protected $fillable = [
        'pc_id',
        'nama_pac',
    ];

    /**
     * Relasi ke PC
     */
    public function pc(): BelongsTo
    {
        return $this->belongsTo(Pc::class, 'pc_id');
    }

    /**
     * Relasi ke Ranting
     */
    public function rantings(): HasMany
    {
        return $this->hasMany(Ranting::class, 'pac_id');
    }

    /**
     * Polymorphic relation ke Level
     */
    public function levels(): MorphMany
    {
        return $this->morphMany(Level::class, 'levelable', 'level_type', 'reference_id');
    }
}
