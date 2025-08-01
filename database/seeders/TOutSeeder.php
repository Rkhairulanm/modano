<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TOutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangIds = Barang::pluck('id')->toArray();

        for ($i = 1; $i <= 100; $i++) {
            $barangId = $barangIds[array_rand($barangIds)];
            $jumlah = rand(1, 20);
            $harga = rand(10000, 100000);
            $total = $jumlah * $harga;

            DB::table('t_out')->insert([
                'nomor' => 'OUT' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal' => now()->subDays(rand(1, 365)),
                'no_bpb' => 'BPB-' . strtoupper(Str::random(5)),
                'keterangan' => 'Barang keluar ke-' . $i,
                'barang_id' => $barangId,
                'jumlah' => $jumlah,
                'harga' => $harga,
                'total' => $total, // <<< INI DIA YANG DITAMBAH!
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
