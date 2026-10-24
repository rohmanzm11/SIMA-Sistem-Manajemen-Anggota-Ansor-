<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggotaSocialMedia extends Model
{
    use HasFactory;

    protected $table = 'anggota_social_media';

    protected $fillable = [
        'anggota_id',
        'social_media_id',
        'username',
        'url',
    ];

    /**
     * Relasi ke Anggota
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    /**
     * Relasi ke Social Media
     */
    public function socialMedia(): BelongsTo
    {
        return $this->belongsTo(SocialMedia::class, 'social_media_id');
    }
}
