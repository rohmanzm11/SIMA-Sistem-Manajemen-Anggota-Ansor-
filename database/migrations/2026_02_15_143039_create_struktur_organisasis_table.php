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
        Schema::create('struktur_organisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggotas')->onDelete('cascade');
            $table->foreignId('level_id')->constrained('levels')->onDelete('cascade');
            $table->foreignId('organisasi_id')->nullable()->constrained('organisasis')->nullOnDelete();
            $table->foreignId('jabatan_id')->constrained('jabatans')->onDelete('cascade');
            $table->date('masa_khidmat_mulai');
            $table->date('masa_khidmat_selesai')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('anggota_id');
            $table->index('level_id');
            $table->index('jabatan_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('struktur_organisasis');
    }
};
