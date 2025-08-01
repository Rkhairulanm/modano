<?php

namespace App\Filament\Exports;

use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class BarangExporter extends Exporter
{
    protected static ?string $model = Barang::class;


    public static function getColumns(): array
    {
        return [
            ExportColumn::make('kode_barang')->label('Kode Barang'),
            ExportColumn::make('nama_barang')->label('Nama Barang'),
            ExportColumn::make('satuan.name')->label('Satuan'),
            ExportColumn::make('rop')->label('ROP'),
            ExportColumn::make('ss')->label('Safety Stock'),

            // Ini kolom stok hasil kalkulasi
            ExportColumn::make('stok')->label('Stok')->state(function ($record) {
                $stok = DB::selectOne("
                    SELECT
                        (SELECT COALESCE(SUM(jumlah), 0) FROM t_in WHERE barang_id = ?) -
                        (SELECT COALESCE(SUM(jumlah), 0) FROM t_out WHERE barang_id = ?) AS stok
                ", [$record->id, $record->id]);

                return $stok->stok ?? 0;
            }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your barang export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
