<?php

namespace App\Filament\Resources\TOutResource\Pages;

use Filament\Actions;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\TOutResource;
use Filament\Resources\Pages\EditRecord;

class EditTOut extends EditRecord
{
    protected static string $resource = TOutResource::class;

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
            'tabel' => 'Keluar Barang',
            'user_id' => Auth::id(),
            'keterangan' => "Mengubah Data Keluar Barang : {$this->record->nomor}",
        ]);
    }
}
