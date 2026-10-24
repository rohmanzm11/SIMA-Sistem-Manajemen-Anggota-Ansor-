<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->renameColumn('tanggal', 'tanggal_mulai');
        });

        Schema::table('pelatihans', function (Blueprint $table) {
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
        });

        // Data lama: anggap acara satu hari
        DB::table('pelatihans')->update(['tanggal_selesai' => DB::raw('tanggal_mulai')]);
    }

    public function down(): void
    {
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->dropColumn('tanggal_selesai');
        });

        Schema::table('pelatihans', function (Blueprint $table) {
            $table->renameColumn('tanggal_mulai', 'tanggal');
        });
    }
};