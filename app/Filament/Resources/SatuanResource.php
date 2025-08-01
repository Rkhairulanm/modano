<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Satuan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LogAktivitas;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\QueryException;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SatuanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SatuanResource\RelationManagers;

class SatuanResource extends Resource
{
    public static function getNavigationLabel(): string
    {
        return 'Satuan';
    }

    public static function getBreadcrumb(): string
    {
        return 'Satuan';
    }

    public static function getModelLabel(): string
    {
        return 'Satuan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Satuan';
    }

    protected static ?string $model = Satuan::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function ($record) {
                        try {
                            $record->delete();

                            LogAktivitas::create([
                                'aksi' => 'delete',
                                'tabel' => 'Satuan',
                                'user_id' => Auth::id(),
                                'keterangan' => "Menghapus Satuan: {$record->name}",
                            ]);
                        } catch (QueryException $e) {
                            if ($e->getCode() == '23000') {
                                Notification::make()
                                    ->title('Gagal Menghapus')
                                    ->body('Satuan ini sedang digunakan oleh data lain dan tidak dapat dihapus.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            throw $e;
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSatuans::route('/'),
            'create' => Pages\CreateSatuan::route('/create'),
            'edit' => Pages\EditSatuan::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest(); // orderBy('created_at', 'desc')
    }
}
