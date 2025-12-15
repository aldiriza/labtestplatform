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
}
