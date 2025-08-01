<?php

namespace App\Filament\Resources\KategoriResource\Pages;

use Filament\Actions;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\KategoriResource;

class CreateKategori extends CreateRecord
{
    protected static string $resource = KategoriResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function afterCreate(): void
    {
        LogAktivitas::create([
            'aksi' => 'create',
            'tabel' => 'Kategori',
            'user_id' => Auth::id(),
            'keterangan' => "Menambahkan Kategori: {$this->record->name}",
        ]);
    }
}
