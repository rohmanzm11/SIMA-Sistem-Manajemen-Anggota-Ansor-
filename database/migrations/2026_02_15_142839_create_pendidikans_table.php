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
        Schema::create('pendidikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggotas')->onDelete('cascade');
            $table->enum('jenjang', ['SD', 'SMP', 'SMA', 'SMK', 'MA', 'Pesantren', 'Diniyyah', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3']);
            $table->string('nama_institusi', 255)->comment('Nama sekolah/pesantren/diniyyah/kampus');
            $table->string('jurusan', 255)->nullable()->comment('Untuk jenjang D1-S3');
            $table->year('tahun_masuk')->nullable();
            $table->year('tahun_lulus')->nullable();
            $table->enum('status', ['Sedang Berjalan', 'Lulus', 'Tidak Lulus', 'Drop Out'])->default('Lulus');
            $table->timestamps();

            $table->index('anggota_id');
            $table->index('jenjang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendidikans');
    }
};
