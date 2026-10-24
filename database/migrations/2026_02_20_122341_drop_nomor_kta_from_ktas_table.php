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
        Schema::table('ktas', function (Blueprint $table) {
            $table->dropColumn('nomor_kta');
        });
    }

    public function down(): void
    {
        Schema::table('ktas', function (Blueprint $table) {
            $table->string('nomor_kta', 50)->unique()->after('nama_batch');
        });
    }
};
