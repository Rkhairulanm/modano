<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\BarangResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBarang extends CreateRecord
{
    protected function afterCreate(): void
    {
        LogAktivitas::create([
            'aksi' => 'create',
            'tabel' => 'Barang',
            'user_id' => Auth::id(),
            'keterangan' => "Menambahkan Barang: {$this->record->nama_barang}",
        ]);
    }

    protected static string $resource = BarangResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
