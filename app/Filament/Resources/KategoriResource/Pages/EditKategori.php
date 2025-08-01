<?php

namespace App\Filament\Resources\KategoriResource\Pages;

use Filament\Actions;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\KategoriResource;

class EditKategori extends EditRecord
{
    protected static string $resource = KategoriResource::class;
    protected function afterSave(): void
    {
        LogAktivitas::create([
            'aksi' => 'update',
            'tabel' => 'Kategori',
            'user_id' => Auth::id(),
            'keterangan' => "Mengubah Kategori: {$this->record->name}",
        ]);
    }
    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\DeleteAction::make(),
    //     ];
    // }
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
