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
        Schema::create('verifikasi_anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggotas')->onDelete('cascade');
            $table->foreignId('verifikator_id')->constrained('users')->onDelete('cascade');
            $table->enum('status_sebelumnya', ['Pending', 'Diverifikasi', 'Ditolak']);
            $table->enum('status_sesudahnya', ['Pending', 'Diverifikasi', 'Ditolak']);
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_verifikasi')->useCurrent();
            $table->timestamps();

            $table->index('anggota_id');
            $table->index('verifikator_id');
            $table->index('tanggal_verifikasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi_anggotas');
    }
};
