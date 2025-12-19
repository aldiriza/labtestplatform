<?php

namespace App\Imports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class MaterialImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip empty rows
            if (!isset($row['material_name'])) {
                continue;
            }

            // Define lookup keys
            // We use PO Number and Material Name as unique identifier
            $matchAttributes = [
                'po_number' => $row['po_number'] ?? null,
                'material_name' => $row['material_name'],
            ];

            // Define values to update/create
            // We map the excel columns to db columns
            // Assuming Excel headers are snake_case or we rely on the implementation to normalize.
            // WithHeadingRow uses slug version of header.
            $values = [
                'lab_po_number' => $row['lab_po_number'] ?? null,
                'lot_number' => $row['lot_number'] ?? null,
                'supplier' => $row['supplier'] ?? null,
                'country_of_supplier' => $row['country_of_supplier'] ?? null,
                'material_group' => $row['material_group'] ?? null,
                'material_type' => $row['material_type'] ?? null,
                'color' => $row['color'] ?? null,
                'color_key' => $row['color_key'] ?? null,
                'mpn' => $row['mpn'] ?? null,
                'article_style' => $row['article_style'] ?? null,
                'component' => $row['component'] ?? null,
                'qty' => $row['qty'] ?? 0,
                'bm' => $row['bm'] ?? null,
            ];

            // If it doesn't exist, it will be created with default status 'Scheduled' (handled by model boot)
            Material::updateOrCreate($matchAttributes, $values);
        }
    }
}
