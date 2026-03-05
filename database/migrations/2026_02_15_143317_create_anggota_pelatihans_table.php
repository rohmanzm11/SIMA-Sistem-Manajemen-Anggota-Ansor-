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
        Schema::create('anggota_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggotas')->onDelete('cascade');
            $table->foreignId('pelatihan_detail_id')->constrained('pelatihan_details')->onDelete('cascade');
            $table->enum('status_kehadiran', ['Hadir', 'Tidak Hadir', 'Izin', 'Sakit'])->default('Hadir');
            $table->decimal('skor', 5, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('sertifikat_nomor', 100)->nullable();
            $table->string('sertifikat_path', 255)->nullable();
            $table->date('tanggal_terbit_sertifikat')->nullable();
            $table->timestamps();

            $table->index('anggota_id');
            $table->index('pelatihan_detail_id');
            $table->unique(['anggota_id', 'pelatihan_detail_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_pelatihans');
    }
};
