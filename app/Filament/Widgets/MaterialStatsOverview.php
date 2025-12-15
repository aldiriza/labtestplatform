<?php

namespace App\Filament\Widgets;

use App\Models\Material;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MaterialStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pending (Incoming)', Material::where('status', 'incoming')->count())
                ->description('Waiting for Lab')
                ->descriptionIcon('heroicon-m-arrow-right-circle')
                ->color('gray'),
            Stat::make('In Lab Processing', Material::whereIn('status', ['received_at_lab', 'testing_in_progress'])->count())
                ->description('Currently being tested')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('warning'),
            Stat::make('Completed Tests', Material::where('status', 'completed')->count())
                ->description('Total finished')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
