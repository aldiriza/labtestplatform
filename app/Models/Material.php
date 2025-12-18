<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_id',
        'item_description', // Was material_name
        'part_number',
        'specification',
        'brand',
        'category',
        'unit',
        'location',
        'minimum_stock',
        'quantity', // Stock
        'supplier',
        'po_number',
        'lot_number',
        'status', // Keeping for flow
        'test_result',
        'test_remarks',
        'lab_received_at',
        'test_completed_at',
        'result_file_path',
        'sla_due_at',
        'lot_arrival_date', // "Date" in excel?
    ];

    protected $casts = [
        'lab_received_at' => 'datetime',
        'test_completed_at' => 'datetime',
        'sla_due_at' => 'datetime',
        'lot_arrival_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($material) {
            $material->unique_id = (string) \Illuminate\Support\Str::uuid();
        });
    }
}
