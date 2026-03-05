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
        Schema::create('print', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_cetakan', 20)->comment('KTA, Sertifikat, Kartu Anggota');
            $table->foreignId('anggota_id')->constrained('anggotas')->onDelete('cascade')->comment('nama_lengkap: anggotas.nama_lengkap');
            $table->foreignId('pelatihan_detail_id')->nullable()->constrained('pelatihan_details')->onDelete('set null');
            $table->foreignId('kta_id')->nullable()->constrained('ktas')->onDelete('set null')->comment('Master KTA yang digunakan saat cetak');
            $table->foreignId('template_sertifikat_id')->nullable()->constrained('template_sertifikats')->onDelete('set null')->comment('Master Template Sertifikat yang digunakan saat cetak');
            $table->timestamp('tanggal_cetak')->useCurrent();
            $table->foreignId('dicetak_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('anggota_id');
            $table->index('jenis_cetakan');
            $table->index('tanggal_cetak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_logs');
    }
};
