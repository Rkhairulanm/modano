<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\BarangKategori;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BarangKategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangIds = Barang::pluck('id')->toArray();
        $kategoriIds = Kategori::pluck('id')->toArray();

        foreach ($barangIds as $barangId) {
            // Setiap barang dikasih 1-2 kategori acak
            $randomKategori = array_rand($kategoriIds, rand(1, 2));
            if (!is_array($randomKategori)) {
                $randomKategori = [$randomKategori];
            }

            foreach ($randomKategori as $index) {
                BarangKategori::create([
                    'barang_id' => $barangId,
                    'kategori_id' => $kategoriIds[$index],
                ]);
            }
        }
    }
}
