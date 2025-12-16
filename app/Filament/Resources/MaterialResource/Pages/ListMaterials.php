<?php

namespace App\Filament\Resources\MaterialResource\Pages;

use App\Filament\Resources\MaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaterials extends ListRecords
{
    protected static string $resource = MaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\MaterialImporter::class)
                ->visible(fn() => auth()->user()->hasRole('admin') || auth()->user()->hasRole('purchasing')),
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => \Filament\Resources\Components\Tab::make('All Materials'),
            'scheduled' => \Filament\Resources\Components\Tab::make('Scheduled')
                ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'scheduled'))
                ->badge(\App\Models\Material::query()->where('status', 'scheduled')->count())
                ->badgeColor('gray'),
            'incoming' => \Filament\Resources\Components\Tab::make('Incoming')
                ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'arrived'))
                ->badge(\App\Models\Material::query()->where('status', 'arrived')->count())
                ->badgeColor('info'),
            'lab' => \Filament\Resources\Components\Tab::make('In Lab')
                ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->whereIn('status', ['lab_in_progress', 'lab_ready_for_pickup']))
                ->badge(\App\Models\Material::query()->whereIn('status', ['lab_in_progress', 'lab_ready_for_pickup'])->count())
                ->badgeColor('warning'),
            'completed' => \Filament\Resources\Components\Tab::make('Completed')
                ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'completed'))
                ->badge(\App\Models\Material::query()->where('status', 'completed')->count())
                ->badgeColor('success'),
        ];
    }
}
