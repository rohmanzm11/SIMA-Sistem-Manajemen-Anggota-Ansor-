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
        Schema::create('anggota_organisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggotas')->onDelete('cascade');
            $table->foreignId('organisasi_id')->constrained('organisasis')->onDelete('cascade');
            $table->string('jabatan', 100)->nullable();
            $table->year('tahun_masuk')->nullable();
            $table->year('tahun_keluar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('anggota_id');
            $table->index('organisasi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_organisasis');
    }
};
