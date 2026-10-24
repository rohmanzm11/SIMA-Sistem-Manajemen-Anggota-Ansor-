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
        Schema::create('template_sertifikats', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED AUTO_INCREMENT
            $table->string('nama_batch')->index(); // varchar(255) dengan Index
            $table->date('tanggal_terbit');
            $table->string('image')->nullable(); // varchar(255) dan boleh NULL
            $table->string('nama_ketua');
            $table->string('ttd_ketua');
            $table->string('nama_sekretaris');
            $table->string('ttd_sekretaris');
            $table->tinyInteger('is_active')->default(1)->index(); // tinyint(1) default 1
            $table->timestamps(); // Menghasilkan created_at & updated_at (timestamp NULL)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_sertifikats');
    }
};
