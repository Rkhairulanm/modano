<?php

namespace App\Filament\Resources;

use App\Filament\Exports\BarangExporter;
use Filament\Forms;
use Filament\Tables;
use App\Models\Barang;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LogAktivitas;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BarangResource\Pages;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Barang';
    protected static ?string $breadcrumb = 'Barang';
    protected static ?string $pluralModelLabel = 'Barang';
    protected static ?string $modelLabel = 'Barang';


    // public static function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $last = Barang::latest('id')->first();
    //     $nextId = $last ? $last->id + 1 : 1;
    //     $data['kode_barang'] = 'BRG-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

    //     return $data;
    // }
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $attempt = 0;
        do {
            $last = Barang::latest('id')->first();
            $nextId = $last ? $last->id + 1 + $attempt : 1 + $attempt;
            $kode = 'BRG-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $attempt++;
        } while (Barang::where('kode_barang', $kode)->exists());

        $data['kode_barang'] = $kode;

        return $data;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Form Barang')
                    ->tabs([
                        Tab::make('📦 Data Barang')->schema([
                            Grid::make(2)->schema([
                                TextInput::make('kode_barang')
                                    ->label('Kode Barang')
                                    ->readOnly()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->rule('regex:/^BRG-\d{4}$/')
                                    ->default(function () {
                                        $last = \App\Models\Barang::latest('id')->first();
                                        $nextId = $last ? $last->id + 1 : 1;
                                        return 'BRG-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
                                    })
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if (!$record && blank($state)) {
                                            $last = \App\Models\Barang::latest('id')->first();
                                            $nextId = $last ? $last->id + 1 : 1;
                                            $component->state('BRG-' . str_pad($nextId, 4, '0', STR_PAD_LEFT));
                                        }
                                    }),
                                    // ->visible(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                                    // ->dehydrated(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),

                                TextInput::make('nama_barang')
                                    ->label('Nama Barang')
                                    ->required(),

                                Select::make('kategoris')
                                    ->multiple()
                                    ->relationship('kategoris', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')->required()->label('Nama Kategori'),
                                    ])
                                    ->required()
                                    ->label('Kategori'),

                                Select::make('satuan_id')
                                    ->relationship('satuan', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')->required()->label('Nama Satuan'),
                                    ])
                                    ->required()
                                    ->label('Satuan'),

                                TextInput::make('harga')
                                    ->label('Harga')
                                    ->numeric()
                                    ->required(),
                                // TextInput::make('stok')
                                //     ->label('Jumlah')
                                //     ->numeric()
                                //     ->required(),
                                TextInput::make('ss')
                                    ->label('Safety Stock')
                                    ->numeric()
                                    ->required(),

                                TextInput::make('rop')
                                    ->label('Reorder Point')
                                    ->numeric()
                                    ->required(),

                                TextInput::make('reff')
                                    ->label('Referensi')
                                    ->nullable(),
                            ]),
                            // Keterangan full span
                            Textarea::make('keterangan')
                                ->label('Keterangan')
                                ->nullable()
                                ->columnSpanFull(),
                        ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_barang')->label('Kode Barang')->searchable(),
                Tables\Columns\TextColumn::make('nama_barang')->label('Nama Barang')->searchable(),

                Tables\Columns\TextColumn::make('kategoris.name')
                    ->label('Kategori')
                    ->badge()
                    ->separator(', ') // biar muncul gabungan kategori kalau banyak
                    ->sortable()
                    ->toggleable(), // Bisa disembunyikan

                Tables\Columns\TextColumn::make('satuan.name')
                    ->label('Satuan')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('stok')
                    ->label('Stok')
                    ->getStateUsing(function ($record) {
                        $stok = DB::selectOne("
                            SELECT
                                (SELECT COALESCE(SUM(jumlah), 0) FROM t_in WHERE barang_id = ?) -
                                (SELECT COALESCE(SUM(jumlah), 0) FROM t_out WHERE barang_id = ?) AS stok
                        ", [$record->id, $record->id]);

                        return $stok->stok ?? 0;
                    }),
                TextColumn::make('rop')->label('ROP'),

                Tables\Columns\TextColumn::make('ss')
                    ->label('Safety Stock')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Default disembunyiin

                Tables\Columns\TextColumn::make('rop')
                    ->label('Reorder Point')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Default disembunyiin
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategoris')
                    ->label('Filter Kategori')
                    ->relationship('kategoris', 'name'),
                Tables\Filters\SelectFilter::make('satuan_id')
                    ->label('Filter Satuan')
                    ->relationship('satuan', 'name'),
            ])
            ->headerActions([
                ExportAction::make()->exporter(BarangExporter::class)
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-on-square')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        LogAktivitas::create([
                            'aksi' => 'delete',
                            'tabel' => 'barang',
                            'user_id' => Auth::id(),
                            'keterangan' => "Menghapus barang: {$record->nomor}",
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest(); // orderBy('created_at', 'desc')
    }

    public static function getRelations(): array
    {
        return [];
    }
}
