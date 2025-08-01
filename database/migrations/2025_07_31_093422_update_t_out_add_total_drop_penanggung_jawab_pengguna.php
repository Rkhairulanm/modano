<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_out', function (Blueprint $table) {
            // Drop kolom lama
            if (Schema::hasColumn('t_out', 'penanggung_jawab')) {
                $table->dropColumn('penanggung_jawab');
            }

            if (Schema::hasColumn('t_out', 'pengguna')) {
                $table->dropColumn('pengguna');
            }

            // Tambah kolom baru
            if (!Schema::hasColumn('t_out', 'total')) {
                $table->integer('total')->after('harga')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('t_out', function (Blueprint $table) {
            // Tambah kolom yang dihapus
            if (!Schema::hasColumn('t_out', 'penanggung_jawab')) {
                $table->string('penanggung_jawab')->nullable()->after('keterangan');
            }

            if (!Schema::hasColumn('t_out', 'pengguna')) {
                $table->string('pengguna')->nullable()->after('harga');
            }

            // Hapus kolom total
            if (Schema::hasColumn('t_out', 'total')) {
                $table->dropColumn('total');
            }
        });
    }
};
