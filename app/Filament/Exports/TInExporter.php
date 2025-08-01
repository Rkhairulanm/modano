<?php

namespace App\Filament\Exports;

use App\Models\TIn;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TInExporter extends Exporter
{
    protected static ?string $model = TIn::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nomor'),
            ExportColumn::make('tanggal'),
            ExportColumn::make('surat_jalan'),
            ExportColumn::make('no_po'),
            ExportColumn::make('keterangan'),
            ExportColumn::make('barang.nama')
                ->label('Nama Barang'),
            ExportColumn::make('jumlah'),
            ExportColumn::make('harga'),
            ExportColumn::make('supplier'),
            ExportColumn::make('job_order'),
            ExportColumn::make('total'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export Data Barang Masuk Berhasil! ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
