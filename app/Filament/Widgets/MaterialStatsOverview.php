<?php

namespace App\Filament\Widgets;

use App\Models\Material;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MaterialStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Scheduled', Material::where('status', \App\Enums\MaterialStatus::Scheduled)->count())
                ->description('Waiting to arrive')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('gray'),
            Stat::make('Arrived', Material::where('status', \App\Enums\MaterialStatus::Arrived)->count())
                ->description('Ready for Lab')
                ->descriptionIcon('heroicon-m-check')
                ->color('info'),
            Stat::make('In Lab', Material::whereIn('status', [\App\Enums\MaterialStatus::LabReceived, \App\Enums\MaterialStatus::InProgress])->count())
                ->description('Testing in progress')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('warning'),
            Stat::make('Lab Out', Material::where('status', \App\Enums\MaterialStatus::Completed)->count())
                ->description('Completed')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
