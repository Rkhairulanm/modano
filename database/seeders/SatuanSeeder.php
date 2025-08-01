<?php

namespace Database\Seeders;

use App\Models\Satuan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $satuans = ['Pcs', 'Box', 'Kg', 'Meter', 'Liter', 'Roll'];

        foreach ($satuans as $satuan) {
            Satuan::create(['name' => $satuan]);
        }
    }
}
