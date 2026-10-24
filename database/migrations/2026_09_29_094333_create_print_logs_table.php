<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_cetakan', ['KTA', 'Sertifikat']);
            $table->foreignId('anggota_id')->constrained('anggotas')->cascadeOnDelete();
            $table->foreignId('pelatihan_detail_id')->nullable()->constrained('pelatihan_details')->nullOnDelete();
            $table->foreignId('template_sertifikat_id')->nullable()->constrained('template_sertifikats')->nullOnDelete();
            $table->foreignId('kta_id')->nullable()->constrained('ktas')->nullOnDelete();
            $table->foreignId('dicetak_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_cetak')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_logs');
    }
};