<?php

namespace App\Filament\Imports;

use App\Models\Material;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class MaterialImporter extends Importer
{
    protected static ?string $model = Material::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('material_name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('supplier')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('color')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('shoe_style')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('article_no')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('po_number')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('lot_number')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('quantity')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:1']),
        ];
    }

    public function resolveRecord(): ?Material
    {
        // return Material::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Material();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your material import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
