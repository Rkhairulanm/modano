<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\TOut;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LogAktivitas;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\TOutResource\Pages;

class TOutResource extends Resource
{
    protected static ?string $model = TOut::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationLabel = 'Barang Keluar';
    protected static ?string $breadcrumb = 'Barang Keluar';
    protected static ?string $pluralModelLabel = 'Barang Keluar';
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $barang = \App\Models\Barang::find($data['barang_id']);

        if (!$barang) {
            throw new \Exception('Barang tidak ditemukan.');
        }

        if ($barang->stok < $data['jumlah']) {
            throw new \Exception('Stok tidak cukup. Sisa stok: ' . $barang->stok);
        }

        // Otomatis generate nomor tanpa konflik
        $attempt = 0;
        do {
            $last = TOut::latest('id')->first();
            $nextId = $last ? $last->id + 1 + $attempt : 1 + $attempt;
            $nomor = 'OUT-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $attempt++;
        } while (TOut::where('nomor', $nomor)->exists());

        $data['nomor'] = $nomor;
        $data['total'] = $data['jumlah'] * $data['harga'];

        return $data;
    }


    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Barang Keluar')->tabs([
                Tab::make('📤 Data Transaksi')->schema([
                    Grid::make(2)->schema([
                        TextInput::make('nomor')
                            ->label('Nomor Transaksi')
                            ->readOnly()
                            ->required()
                            ->default(function () {
                                $last = \App\Models\TOut::latest('id')->first();
                                $nextId = $last ? $last->id + 1 : 1;
                                return 'OUT-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
                            })
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record && blank($state)) {
                                    $last = \App\Models\TOut::latest('id')->first();
                                    $nextId = $last ? $last->id + 1 : 1;
                                    $component->state('OUT-' . str_pad($nextId, 4, '0', STR_PAD_LEFT));
                                }
                            }),


                        DatePicker::make('tanggal')
                            ->label('Tanggal Keluar')
                            ->required(),

                        TextInput::make('no_bpb')
                            ->label('No. BPB')
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
                            })
                            ->rule(function (callable $get) {
                                $barangId = $get('barang_id');
                                $barang = \App\Models\Barang::find($barangId);
                                if ($barang) {
                                    return 'max:' . $barang->stok;
                                }
                                return null;
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
                    ]),

                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->nullable()
                        ->columnSpanFull(),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('nomor')
                ->label('Nomor')
                ->searchable(),

            Tables\Columns\TextColumn::make('tanggal')
                ->label('Tanggal')
                ->date()
                ->sortable(),

            Tables\Columns\TextColumn::make('no_bpb')
                ->label('No. BPB')
                ->searchable(),

            Tables\Columns\TextColumn::make('barang.nama_barang')
                ->label('Barang')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('jumlah')
                ->label('Jumlah')
                ->sortable(),

            Tables\Columns\TextColumn::make('harga')
                ->label('Harga Satuan')
                ->money('IDR', true)
                ->sortable(),

            Tables\Columns\TextColumn::make('total')
                ->label('Total Harga')
                ->money('IDR', true)
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Dibuat')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('updated_at')
                ->label('Diperbarui')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        LogAktivitas::create([
                            'aksi' => 'delete',
                            'tabel' => 'Barang Keluar',
                            'user_id' => Auth::id(),
                            'keterangan' => "Menghapus Data Barang Keluar : {$record->nomor}",
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
            'index' => Pages\ListTOuts::route('/'),
            'create' => Pages\CreateTOut::route('/create'),
            'edit' => Pages\EditTOut::route('/{record}/edit'),
        ];
    }
}
