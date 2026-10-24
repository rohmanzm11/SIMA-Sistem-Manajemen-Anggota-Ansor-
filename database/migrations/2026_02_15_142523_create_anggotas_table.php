<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('anggotas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap', 255);
            $table->string('nik', 16)->unique();
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);

            // Alamat
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->onDelete('restrict');
            $table->foreignId('desa_id')->constrained('desas')->onDelete('restrict');
            $table->string('rt', 10);
            $table->string('rw', 10);
            $table->text('alamat_lengkap')->nullable();

            // Data Fisik
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O', 'Tidak Tahu']);
            $table->integer('tinggi_badan')->comment('dalam cm');
            $table->integer('berat_badan')->comment('dalam kg');

            // Status
            $table->enum('status_pernikahan', ['Belum Menikah', 'Menikah', 'Cerai Hidup', 'Cerai Mati']);

            // NPWP
            $table->boolean('npwp_status')->default(false);
            $table->string('npwp_nomor', 20)->nullable();

            // BPJS
            $table->boolean('bpjs_status')->default(false);
            $table->string('bpjs_nomor', 20)->nullable();

            // Kontak
            $table->string('alamat_email', 255)->unique()->nullable();
            $table->string('nomor_hp', 15);

            // Relasi ke Master
            $table->foreignId('pekerjaan_id')->nullable()->constrained('pekerjaans')->onDelete('set null');
            $table->foreignId('politik_id')->nullable()->constrained('politiks')->onDelete('set null');

            // Status Verifikasi
            $table->enum('status_verifikasi', ['Pending', 'Diverifikasi', 'Ditolak'])->default('Pending');
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->text('catatan_verifikasi')->nullable()->comment('Alasan ditolak atau catatan lainnya');

            // Files
            $table->string('foto', 255)->nullable();
            $table->string('ktp', 255)->nullable();

            $table->timestamps();

            // Indexes
            $table->index('nik');
            $table->index('alamat_email');
            $table->index('nama_lengkap');
            $table->index('kecamatan_id');
            $table->index('desa_id');
            $table->index('status_verifikasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggotas');
    }
};
