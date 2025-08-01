<?php

namespace App\Filament\Resources\SatuanResource\Pages;

use Filament\Actions;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SatuanResource;

class EditSatuan extends EditRecord
{
    protected static string $resource = SatuanResource::class;

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

    protected function afterSave(): void
    {
        LogAktivitas::create([
            'aksi' => 'update',
            'tabel' => 'Kategori',
            'user_id' => Auth::id(),
            'keterangan' => "Mengubah Kategori: {$this->record->name}",
        ]);
    }
}
