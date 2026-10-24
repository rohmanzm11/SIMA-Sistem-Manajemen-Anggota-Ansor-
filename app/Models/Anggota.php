<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggotas';

    protected $fillable = [
        'nama_lengkap',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'kecamatan_id',
        'desa_id',
        'rt',
        'rw',
        'alamat_lengkap',
        'golongan_darah',
        'tinggi_badan',
        'berat_badan',
        'status_pernikahan',
        'npwp_status',
        'npwp_nomor',
        'bpjs_status',
        'bpjs_nomor',
        'alamat_email',
        'nomor_hp',
        'pekerjaan_id',
        'politik_id',
        'status_verifikasi',
        'tanggal_verifikasi',
        'catatan_verifikasi',
        'foto',
        'ktp',
        'nia',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'npwp_status' => 'boolean',
        'bpjs_status' => 'boolean',
        'tanggal_verifikasi' => 'datetime',
    ];

    /**
     * Relasi ke Kecamatan
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    /**
     * Relasi ke Desa
     */
    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class, 'desa_id');
    }

    /**
     * Relasi ke Pekerjaan
     */
    public function pekerjaan(): BelongsTo
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_id');
    }

    /**
     * Relasi ke Politik
     */
    public function politik(): BelongsTo
    {
        return $this->belongsTo(Politik::class, 'politik_id');
    }

    /**
     * Relasi ke User (One to One)
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'anggota_id');
    }

    /**
     * Relasi ke Verifikasi Anggota
     */
    public function verifikasiHistory(): HasMany
    {
        return $this->hasMany(VerifikasiAnggota::class, 'anggota_id');
    }

    /**
     * Relasi ke Pendidikan
     */
    public function pendidikans(): HasMany
    {
        return $this->hasMany(Pendidikan::class, 'anggota_id');
    }

    /**
     * Relasi ke Anggota Social Media
     */
    public function socialMediaAccounts(): HasMany
    {
        return $this->hasMany(AnggotaSocialMedia::class, 'anggota_id');
    }

    /**
     * Relasi Many-to-Many ke Social Media melalui pivot
     */
    public function socialMedias(): BelongsToMany
    {
        return $this->belongsToMany(SocialMedia::class, 'anggota_social_media', 'anggota_id', 'social_media_id')
            ->withPivot('username', 'url')
            ->withTimestamps();
    }

    /**
     * Relasi ke Anggota Organisasi
     */
    public function organisasiMemberships(): HasMany
    {
        return $this->hasMany(AnggotaOrganisasi::class, 'anggota_id');
    }

    /**
     * Relasi Many-to-Many ke Organisasi melalui pivot
     */
    public function organisasis(): BelongsToMany
    {
        return $this->belongsToMany(Organisasi::class, 'anggota_organisasi', 'anggota_id', 'organisasi_id')
            ->withPivot('jabatan', 'tahun_masuk', 'tahun_keluar', 'is_active')
            ->withTimestamps();
    }

    /**
     * Relasi ke Struktur Organisasi
     */
    public function strukturOrganisasi(): HasMany
    {
        return $this->hasMany(StrukturOrganisasi::class, 'anggota_id');
    }

    /**
     * Relasi ke Struktur Organisasi yang aktif
     */
    public function strukturOrganisasiAktif(): HasMany
    {
        return $this->strukturOrganisasi()->where('is_active', true);
    }

    /**
     * Relasi ke KTA
     */
    public function ktas(): HasMany
    {
        return $this->hasMany(Kta::class, 'anggota_id');
    }

    /**
     * Relasi ke KTA yang aktif
     */
    public function ktaAktif(): HasOne
    {
        return $this->hasOne(Kta::class, 'anggota_id')->where('is_active', true)->latestOfMany();
    }

    /**
     * Relasi ke Anggota Pelatihan
     */
    public function pelatihanRecords(): HasMany
    {
        return $this->hasMany(AnggotaPelatihan::class, 'anggota_id');
    }

    /**
     * Relasi Many-to-Many ke Pelatihan Detail melalui pivot
     */
    public function pelatihans(): BelongsToMany
    {
        return $this->belongsToMany(PelatihanDetail::class, 'anggota_pelatihan', 'anggota_id', 'pelatihan_detail_id')
            ->withPivot('status_kehadiran', 'skor', 'keterangan', 'sertifikat_nomor', 'sertifikat_path', 'tanggal_terbit_sertifikat')
            ->withTimestamps();
    }

    /**
     * Relasi ke Print Log
     */
    public function printLogs(): HasMany
    {
        return $this->hasMany(PrintLog::class, 'anggota_id');
    }

    /**
     * Scope untuk filter berdasarkan status verifikasi
     */
    public function scopeVerified($query)
    {
        return $query->where('status_verifikasi', 'Diverifikasi');
    }

    /**
     * Scope untuk filter berdasarkan status pending
     */
    public function scopePending($query)
    {
        return $query->where('status_verifikasi', 'Pending');
    }

    /**
     * Scope untuk filter berdasarkan status ditolak
     */
    public function scopeRejected($query)
    {
        return $query->where('status_verifikasi', 'Ditolak');
    }

    /**
     * Accessor untuk mendapatkan alamat lengkap dengan format
     */
    public function getAlamatLengkapFormatAttribute()
    {
        return sprintf(
            '%s, RT %s RW %s, Desa %s, Kecamatan %s',
            $this->alamat_lengkap ?? '-',
            $this->rt,
            $this->rw,
            $this->desa->nama_desa ?? '-',
            $this->kecamatan->nama_kecamatan ?? '-'
        );
    }

    /**
     * Accessor untuk mendapatkan umur
     */
    public function getUmurAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
    }
}
