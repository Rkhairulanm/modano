<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LogAktivitas;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    // Ganti nama sidebar & breadcrumb
    protected static ?string $navigationLabel = 'User Control';
    protected static ?string $pluralModelLabel = 'User Control';
    protected static ?string $breadcrumb = 'User Control';

    // Ganti icon ke icon user
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'User Control';
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->required(),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required(),
                ]),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->maxLength(255)
                    ->autocomplete('new-password')
                    ->required(fn($livewire) => $livewire instanceof Pages\CreateUser)
                    ->dehydrated(fn($state) => filled($state))
                    ->visible(fn($livewire) => $livewire instanceof Pages\CreateUser || $livewire instanceof Pages\EditUser),

                Select::make('roles')
                    ->label('Role')
                    ->options(Role::all()->pluck('name', 'name')->mapWithKeys(fn($r) => [$r => ucwords(str_replace('_', ' ', $r))]))
                    ->default(fn($record) => $record?->roles->pluck('name')->first())
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $record) {
                        if ($record) {
                            $record->syncRoles([$state]);
                        }
                    }),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->formatStateUsing(fn($state) => ucwords(str_replace('_', ' ', $state))),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Filter by Role')
                    ->options(
                        Role::all()->pluck('name', 'name')->mapWithKeys(fn($role) => [$role => ucwords(str_replace('_', ' ', $role))])
                    )
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['value'])) {
                            return $query->whereHas('roles', fn($q) => $q->where('name', $data['value']));
                        }

                        return $query;
                    }),
            ])


            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        LogAktivitas::create([
                            'aksi' => 'delete',
                            'tabel' => 'User',
                            'user_id' => Auth::id(),
                            'keterangan' => "Menghapus User: {$record->name}",
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
