<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggotas', function (Blueprint $table) {
            // Kita tambahkan kolom NIA, biasanya unik dan diletakkan setelah kolom ID atau NIK
            $table->string('nia', 50)->unique()->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('anggotas', function (Blueprint $table) {
            $table->dropColumn('nia');
        });
    }
};
