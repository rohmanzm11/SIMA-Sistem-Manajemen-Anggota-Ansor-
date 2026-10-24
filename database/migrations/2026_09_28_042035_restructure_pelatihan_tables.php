<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // pelatihans: tambah tempat & tanggal
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->string('tempat', 255)->nullable()->after('nama_pelatihan');
            $table->date('tanggal')->nullable()->after('tempat');
        });

        // pelatihan_details: hapus tempat & tanggal
        Schema::table('pelatihan_details', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
            $table->dropColumn(['tempat', 'tanggal']);
        });

        // materis: nama materi unik supaya tidak duplikat saat auto-save
        Schema::table('materis', function (Blueprint $table) {
            $table->unique('nama_materi');
        });
    }

    public function down(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            $table->dropUnique(['nama_materi']);
        });

        Schema::table('pelatihan_details', function (Blueprint $table) {
            $table->string('tempat', 255)->nullable();
            $table->date('tanggal')->nullable();
            $table->index('tanggal');
        });

        Schema::table('pelatihans', function (Blueprint $table) {
            $table->dropColumn(['tempat', 'tanggal']);
        });
    }
};