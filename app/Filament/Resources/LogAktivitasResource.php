<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LogAktivitasResource\Pages;
use App\Models\LogAktivitas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LogAktivitasResource extends Resource
{
    protected static ?string $model = LogAktivitas::class;
    protected static ?string $navigationGroup = 'User Control';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('admin');
    }
    // Ganti ikon ke yang lebih relevan
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('aksi')->required()->maxLength(255),
                Forms\Components\TextInput::make('tabel')->required()->maxLength(255),
                Forms\Components\Select::make('user_id')->relationship('user', 'name')->default(null),
                Forms\Components\Textarea::make('keterangan')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('aksi')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('tabel'),
                Tables\Columns\TextColumn::make('user.name')->label('User')->searchable(),
                Tables\Columns\TextColumn::make('keterangan')->wrap(),
                Tables\Columns\TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('aksi')
                    ->label('Filter Aksi')
                    ->options([
                        'create' => 'Tambah',
                        'update' => 'Edit',
                        'delete' => 'Hapus',
                    ]),
            ])
            ->actions([]) // Hapus tombol Edit
            ->bulkActions([]); // Hapus semua bulk action
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            // Hanya sediakan halaman index
            'index' => Pages\ListLogAktivitas::route('/'),
            // Halaman create & edit tidak digunakan
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest(); // orderBy('created_at', 'desc')
    }
}
