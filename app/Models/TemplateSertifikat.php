<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateSertifikat extends Model
{
    protected $table = 'template_sertifikats';
    protected $fillable = [
        'nama_batch',
        'tanggal_terbit',
        'image',
        'nama_ketua',
        'ttd_ketua',
        'nama_sekretaris',
        'ttd_sekretaris',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_terbit' => 'date',
        'tanggal_berlaku_sampai' => 'date',
    ];
}
