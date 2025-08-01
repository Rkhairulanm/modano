<?php

namespace App\Filament\Resources\TInResource\Pages;

use Filament\Actions;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\TInResource;
use Filament\Resources\Pages\EditRecord;

class EditTIn extends EditRecord
{
    protected static string $resource = TInResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['total'] = $data['jumlah'] * $data['harga'];
        return $data;
    }
    protected function afterSave(): void
    {
        LogAktivitas::create([
            'aksi' => 'update',
            'tabel' => 'Tambah Barang',
            'user_id' => Auth::id(),
            'keterangan' => "Mengubah Data Tambah Barang : {$this->record->nomor}",
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
