<?php

namespace Database\Seeders;

use App\Models\TIn;
use App\Models\Barang;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TInSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangIds = Barang::pluck('id')->toArray();

        for ($i = 1; $i <= 50; $i++) {
            $barangId = $barangIds[array_rand($barangIds)];
            $jumlah = rand(1, 50);
            $harga = rand(10000, 100000);
            $total = $jumlah * $harga;

            TIn::create([
                'nomor' => 'TIN' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal' => now()->subDays(rand(0, 30)),
                'surat_jalan' => 'SJ-' . rand(1000, 9999),
                'no_po' => 'PO-' . rand(1000, 9999),
                'keterangan' => 'Barang masuk ke-' . $i,
                'barang_id' => $barangId,
                'jumlah' => $jumlah,
                'harga' => $harga,
                'supplier' => 'Supplier ' . rand(1, 10),
                'job_order' => 'JO-' . strtoupper(Str::random(5)),
                'total' => $total,
            ]);
        }
    }
}
