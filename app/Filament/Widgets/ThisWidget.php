<?php

namespace App\Filament\Widgets;

use App\Models\TIn;
use App\Models\TOut;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ThisWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Nilai Barang Masuk', 'Rp ' . number_format(TOut::sum('harga'), 0, ',', '.'))
                ->icon('heroicon-m-currency-dollar')
                ->chart($this->getTOutChartData())
                ->description('Total nilai barang masuk selama 7 hari terakhir')
                ->color('success'),

            Stat::make('Total Nilai Barang Keluar', 'Rp ' . number_format(TIn::sum('harga'), 0, ',', '.'))
                ->icon('heroicon-m-currency-dollar')
                ->description('Total nilai barang keluar selama 7 hari terakhir')
                ->chart($this->getTInChartData())
                ->color('info'),
        ];
    }

    protected function getColumns(): int
    {
        return 1;
    }

    // Ambil data TOut per hari (misalnya 7 hari terakhir)
    protected function getTOutChartData(): array
    {
        return $this->getChartData(TOut::class);
    }

    // Ambil data TIn per hari (misalnya 7 hari terakhir)
    protected function getTInChartData(): array
    {
        return $this->getChartData(TIn::class);
    }

    // Fungsi reusable ambil data sum(harga) per tanggal
    protected function getChartData(string $modelClass): array
    {
        $days = collect();

        // Ambil 7 hari terakhir
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $sum = $modelClass::whereDate('created_at', $date)->sum('harga');
            $days->push($sum);
        }

        return $days->toArray(); // misal: [10000, 20000, 0, 30000, ...]
    }
}
