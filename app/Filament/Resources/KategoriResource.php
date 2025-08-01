<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Kategori;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LogAktivitas;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\KategoriExporter;
use App\Filament\Resources\KategoriResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KategoriResource\RelationManagers;

class KategoriResource extends Resource
{
    protected static ?string $model = Kategori::class;

    // Label di sidebar
    protected static ?string $navigationLabel = 'Kategori';

    // Label breadcrumb & judul halaman
    protected static ?string $modelLabel = 'Kategori';

    // Ikon di sidebar
    protected static ?string $navigationIcon = 'heroicon-o-tag'; // Ganti dengan ikon yang lebih cocok seperti 'tag'

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
                                'tabel' => 'Kategori',
                                'user_id' => Auth::id(),
                                'keterangan' => "Menghapus Kategori: {$record->name}",
                            ]);
                        } catch (QueryException $e) {
                            if ($e->getCode() == '23000') {
                                Notification::make()
                                    ->title('Gagal Menghapus')
                                    ->body('Kategori ini sedang digunakan oleh data lain dan tidak dapat dihapus.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            throw $e;
                        }
                    }),
            ])
            // ->headerActions([
            //     ExportAction::make()->exporter(KategoriExporter::class)
            // ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKategoris::route('/'),
            'create' => Pages\CreateKategori::route('/create'),
            'edit' => Pages\EditKategori::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest(); // orderBy('created_at', 'desc')
    }
}

