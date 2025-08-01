<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        LogAktivitas::create([
            'aksi' => 'create',
            'tabel' => 'User',
            'user_id' => Auth::id(),
            'keterangan' => "Menambahkan User: {$this->record->name}",
        ]);
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $user = static::getModel()::create($data);
        $user->syncRoles([$roles]);

        return $user;
    }

}
