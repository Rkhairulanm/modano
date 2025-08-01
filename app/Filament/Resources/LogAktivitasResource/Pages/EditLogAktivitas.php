<?php

namespace App\Filament\Resources\LogAktivitasResource\Pages;

use App\Filament\Resources\LogAktivitasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLogAktivitas extends EditRecord
{
    protected static string $resource = LogAktivitasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Kembali')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(fn() => static::getResource()::getUrl('index')),
        ];
    }
}
