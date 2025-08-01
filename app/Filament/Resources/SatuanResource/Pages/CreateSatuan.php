<?php

namespace App\Filament\Resources\SatuanResource\Pages;

use Filament\Actions;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\SatuanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSatuan extends CreateRecord
{
    protected static string $resource = SatuanResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        LogAktivitas::create([
            'aksi' => 'create',
            'tabel' => 'Satuan',
            'user_id' => Auth::id(),
            'keterangan' => "Menambahkan Satuan: {$this->record->name}",
        ]);
    }

}
