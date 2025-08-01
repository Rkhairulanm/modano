<?php

namespace App\Filament\Resources\TInResource\Pages;

use App\Filament\Resources\TInResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTIns extends ListRecords
{
    protected static string $resource = TInResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Data Barang Masuk'),
        ];
    }
}
