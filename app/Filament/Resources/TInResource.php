<?php

namespace App\Filament\Resources;

use App\Models\TIn;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LogAktivitas;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Facades\Auth;
use App\Filament\Exports\TInExporter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TInResource\Pages;

class TInResource extends Resource
{
    protected static ?string $model = TIn::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';
    protected static ?string $navigationLabel = 'Barang Masuk';
    protected static ?string $breadcrumb = 'Barang Masuk';
    protected static ?string $pluralModelLabel = 'Barang Masuk';
    protected static ?string $modelLabel = 'Barang Masuk';

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto generate nomor transaksi unik
        $attempt = 0;
        do {
            $last = TIn::latest('id')->first();
            $nextId = $last ? $last->id + 1 + $attempt : 1 + $attempt;
            $nomor = 'IN-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $attempt++;
        } while (TIn::where('nomor', $nomor)->exists());

        $data['nomor'] = $nomor;

        // Hitung total
        $data['total'] = $data['jumlah'] * $data['harga'];

        return $data;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Form Barang')
                    ->tabs([
                        Tab::make('📥 Data Barang')->schema([
                            Card::make()
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextInput::make('nomor')
                                            ->label('Nomor Transaksi')
                                            ->readOnly()
                                            ->required()
                                            ->default(function () {
                                                $last = \App\Models\TIn::latest('id')->first();
                                                $nextId = $last ? $last->id + 1 : 1;
                                                return 'IN-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
                                            })
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if (!$record && blank($state)) {
                                                    $last = \App\Models\TIn::latest('id')->first();
                                                    $nextId = $last ? $last->id + 1 : 1;
                                                    $component->state('IN-' . str_pad($nextId, 4, '0', STR_PAD_LEFT));
                                                }
                                            })
                                            ->visible(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                                            ->dehydrated(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),


                                        DatePicker::make('tanggal')
                                            ->label('Tanggal Masuk')
                                            ->required(),

                                        Select::make('barang_id')
                                            ->label('Barang')
                                            ->relationship('barang', 'nama_barang')
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                        TextInput::make('jumlah')
                                            ->label('Jumlah')
                                            ->numeric()
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $set('total', $state * $get('harga'));
                                            }),

                                        TextInput::make('harga')
                                            ->label('Harga Satuan')
                                            ->numeric()
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $set('total', $get('jumlah') * $state);
                                            }),

                                        TextInput::make('total')
                                            ->label('Total Harga')
                                            ->numeric()
                                            ->disabled()
                                            ->dehydrated(true)
                                            ->default(0),

                                        TextInput::make('no_po')
                                            ->label('No. PO')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('job_order')
                                            ->label('Job Order')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('surat_jalan')
                                            ->label('No. Surat Jalan')
                                            ->maxLength(255)
                                            ->required(),

                                        TextInput::make('supplier')
                                            ->label('Supplier')
                                            ->maxLength(255)
                                            ->nullable(),
                                    ]),

                                    Textarea::make('keterangan')
                                        ->label('Keterangan')
                                        ->nullable()
                                        ->columnSpanFull(),
                                ])
                        ])
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor')->label('Nomor')->searchable(),
                Tables\Columns\TextColumn::make('tanggal')->label('Tanggal')->date()->sortable(),
                Tables\Columns\TextColumn::make('surat_jalan')->label('Surat Jalan')->searchable(),
                Tables\Columns\TextColumn::make('no_po')->label('No. PO')->searchable(),
                Tables\Columns\TextColumn::make('barang.nama_barang')->label('Nama Barang')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('jumlah')->label('Jumlah')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('harga')->label('Harga')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('total')->label('Total')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('supplier')->label('Supplier')->searchable(),
                Tables\Columns\TextColumn::make('job_order')->label('Job Order')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label('Diperbarui')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tambahkan filter jika perlu
            ])
            ->headerActions([
                ExportAction::make()->exporter(TInExporter::class)
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-on-square')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        LogAktivitas::create([
                            'aksi' => 'delete',
                            'tabel' => 'Barang Masuk',
                            'user_id' => Auth::id(),
                            'keterangan' => "Menghapus Data Barang Masuk : {$record->nomor}",
                        ]);
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
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest(); // orderBy('created_at', 'desc')
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTIns::route('/'),
            'create' => Pages\CreateTIn::route('/create'),
            'edit' => Pages\EditTIn::route('/{record}/edit'),
        ];
    }
}
