<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Filament\Resources\BarangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class EditBarang extends EditRecord
{
    protected static string $resource = BarangResource::class;
    protected function afterSave(): void
    {
        LogAktivitas::create([
            'aksi' => 'update',
            'tabel' => 'Barang',
            'user_id' => Auth::id(),
            'keterangan' => "Mengubah Barang: {$this->record->nama_barang}",
        ]);
    }

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
