<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialMedia extends Model
{
    use HasFactory;

    protected $table = 'social_media';

    protected $fillable = [
        'platform_name',
    ];

    /**
     * Relasi ke Anggota Social Media
     */
    public function anggotaSocialMedia(): HasMany
    {
        return $this->hasMany(AnggotaSocialMedia::class, 'social_media_id');
    }
}
