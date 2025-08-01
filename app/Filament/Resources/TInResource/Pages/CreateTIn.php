<?php

namespace App\Filament\Resources\TInResource\Pages;

use Filament\Actions;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\TInResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTIn extends CreateRecord
{
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['total'] = $data['jumlah'] * $data['harga'];
        return $data;
    }

    protected function afterCreate(): void
    {
        LogAktivitas::create([
            'aksi' => 'create',
            'tabel' => 'Tambah Barang',
            'user_id' => Auth::id(),
            'keterangan' => "Menambahkan Data Tambah Barang : {$this->record->nomor}",
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected static string $resource = TInResource::class;
}
