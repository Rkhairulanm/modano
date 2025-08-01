<?php

namespace App\Filament\Resources\TOutResource\Pages;

use Filament\Actions;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\TOutResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTOut extends CreateRecord
{
    protected function afterCreate(): void
    {
        // $record = $this->record;
        // $barang = $record->barang;

        // $barang->stok -= $record->jumlah;
        // $barang->save();

        LogAktivitas::create([
            'aksi' => 'create',
            'tabel' => 'Keluar Barang',
            'user_id' => Auth::id(),
            'keterangan' => "Menambahkan Data Keluar Barang : {$this->record->nomor}",
        ]);
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected static string $resource = TOutResource::class;
}
