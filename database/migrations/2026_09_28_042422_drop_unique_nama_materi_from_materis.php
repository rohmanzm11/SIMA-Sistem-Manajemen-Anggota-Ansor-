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
    Schema::table('materis', function (Blueprint $table) {
        $table->dropUnique(['nama_materi']);
    });
}

public function down(): void
{
    Schema::table('materis', function (Blueprint $table) {
        $table->unique('nama_materi');
    });
}
};
