<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected function afterSave(): void
    {
        LogAktivitas::create([
            'aksi' => 'update',
            'tabel' => 'User',
            'user_id' => Auth::id(),
            'keterangan' => "Mengubah User : {$this->record->name}",
        ]);
    }
    protected static string $resource = UserResource::class;
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $record->update($data);
        $record->syncRoles([$roles]);

        return $record;
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
