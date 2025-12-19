<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\LabMaterialsTable;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function canAccess(): bool
    {
        // Purchasing role cannot view dashboard
        if (auth()->check() && auth()->user()->hasRole('purchasing')) {
            return false;
        }

        return true;
    }

    public function getWidgets(): array
    {
        return [
            LabMaterialsTable::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 1; // Full width single column
    }
}
