<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    use HasFactory;

    protected $table = 'levels';

    protected $fillable = [
        'level_type',
        'reference_id',
    ];

    /**
     * Get the owning levelable model (PC, PAC, or Ranting).
     * Catatan: method ini mengembalikan Builder/Relation, bukan hasil query.
     * Untuk mendapatkan data, panggil $this->levelable (tanpa kurung).
     */
    public function levelable()
    {
        return match ($this->level_type) {
            'PC'      => $this->belongsTo(Pc::class, 'reference_id'),
            'PAC'     => $this->belongsTo(Pac::class, 'reference_id'),
            'RANTING' => $this->belongsTo(Ranting::class, 'reference_id'),
            default   => null,
        };
    }

    /**
     * Relasi ke Struktur Organisasi
     */
    public function strukturOrganisasi(): HasMany
    {
        return $this->hasMany(StrukturOrganisasi::class, 'level_id');
    }

    /**
     * Accessor untuk mendapatkan nama level.
     * FIX: Gunakan $this->levelable (property magic, bukan method call)
     * agar Eloquent me-load relasi dengan benar.
     */
    public function getNamaLevelAttribute(): ?string
    {
        // levelable() bisa return null jika level_type tidak dikenal
        if ($this->levelable() === null) {
            return null;
        }

        // Akses via property magic ($this->levelable) agar relasi di-load
        $related = $this->levelable;

        return match ($this->level_type) {
            'PC'      => $related?->nama_pc,
            'PAC'     => $related?->nama_pac,
            'RANTING' => $related?->nama_ranting,
            default   => null,
        };
    }
}
