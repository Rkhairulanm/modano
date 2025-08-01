<?php

namespace App\Filament\Widgets;

use App\Models\TOut;
use App\Models\User;
use App\Models\Barang;
use App\Models\TIn;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class NextWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Barang', Barang::count())
                ->icon('heroicon-o-cube'),
            Stat::make('Total Pemasukan Barang', TIn::count())
                ->icon('heroicon-o-arrow-down-tray'),
            Stat::make('Total Pengeluaran Barang', TOut::count())
                ->icon('heroicon-o-arrow-up-tray'),
        ];
    }
}
