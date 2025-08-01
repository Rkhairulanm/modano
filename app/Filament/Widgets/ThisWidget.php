<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ThisWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Users', User::count())
            ->icon('heroicon-m-users')
            ->description('Newly Join Users')
            ->chart([1, 2, 5, 2, 10])
            ->color('success')
        ];
    }
}
