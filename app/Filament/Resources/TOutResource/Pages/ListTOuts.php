<?php

namespace App\Filament\Resources\TOutResource\Pages;

use App\Filament\Resources\TOutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTOuts extends ListRecords
{
    protected static string $resource = TOutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Data Keluar Barang'),
        ];
    }
}
