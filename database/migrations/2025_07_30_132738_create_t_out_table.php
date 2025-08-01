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
        Schema::create('t_out', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique(); // Nomor keluar
            $table->date('tanggal'); // Tanggal keluar
            $table->string('no_bpb'); // Bisa nomor referensi surat jalan atau lainnya
            $table->text('keterangan')->nullable(); // Keterangan umum
            $table->string('penanggung_jawab')->nullable(); // Orang yang bertanggung jawab

            // Relasi ke barang
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');

            $table->integer('jumlah'); // Jumlah barang keluar
            $table->integer('harga'); // Jumlah barang keluar
            $table->string('pengguna')->nullable(); // Yang menerima atau user akhir

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_out');
    }
};
