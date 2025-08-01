<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_in', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique();
            $table->date('tanggal');
            $table->string('surat_jalan')->nullable();
            $table->string('no_po')->nullable();
            $table->text('keterangan')->nullable();

            // Relasi ke tabel barang (foreign key)
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');

            $table->integer('jumlah');
            $table->decimal('harga', 12, 2);
            $table->string('supplier')->nullable();
            $table->string('job_order')->nullable();
            $table->decimal('total', 14, 2)->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_in');
    }
};
