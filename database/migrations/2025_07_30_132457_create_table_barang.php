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
        Schema::create('barang', function (Blueprint $table) {
            $table->id(); // ID unik
            $table->string('kode_barang')->unique(); // Kode unik
            $table->string('nama_barang');
            // $table->foreignId('kategori_id')->constrained('kategoris');
            $table->foreignId('satuan_id')->constrained('satuans');
            $table->decimal('harga', 15, 2);
            $table->integer('rop')->nullable(); // Reorder Point
            $table->integer('ss')->nullable();  // Safety Stock
            $table->string('reff')->nullable(); // Referensi tambahan
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
