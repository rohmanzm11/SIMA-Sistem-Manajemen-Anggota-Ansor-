<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ktas', function (Blueprint $table) {
            // 1. Hapus foreign key dan kolom anggota_id
            $table->dropForeign(['anggota_id']);
            $table->dropColumn('anggota_id');

            // 2. Tambahkan kolom nama_batch
            $table->string('nama_batch')->after('id');
            $table->index('nama_batch');

            // 3. Tambahkan kolom ketua dan sekretaris
            $table->string('nama_ketua')->after('image');
            $table->string('ttd_ketua')->after('nama_ketua');
            $table->string('nama_sekretaris')->after('ttd_ketua');
            $table->string('ttd_sekretaris')->after('nama_sekretaris');
        });
    }

    public function down(): void
    {
        Schema::table('ktas', function (Blueprint $table) {
            // Kembalikan ke struktur semula jika rollback
            $table->foreignId('anggota_id')->constrained('anggotas')->onDelete('cascade');
            $table->dropColumn(['nama_batch', 'nama_ketua', 'ttd_ketua', 'nama_sekretaris', 'ttd_sekretaris']);
        });
    }
};
