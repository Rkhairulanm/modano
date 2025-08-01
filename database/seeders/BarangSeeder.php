<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Satuan;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $satuans = Satuan::pluck('id')->toArray();

        for ($i = 1; $i <= 100; $i++) {
            Barang::create([
                'kode_barang' => 'BRG' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nama_barang' => 'Barang ' . $i,
                'satuan_id' => $satuans[array_rand($satuans)],
                'harga' => rand(10000, 100000),
                'rop' => rand(5, 20),
                'ss' => rand(2, 10),
                'reff' => 'REF-' . strtoupper(Str::random(5)),
                'keterangan' => 'Keterangan barang ke-' . $i,
            ]);
        }
    }
}
