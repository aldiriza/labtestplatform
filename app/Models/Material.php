<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_id',
        'material_name',
        'supplier',
        'color',
        'shoe_style',
        'article_no',
        'po_number',
        'lot_number',
        'quantity',
        'status',
        'test_result',
        'test_remarks',
        'lab_received_at',
        'test_completed_at',
        'result_file_path',
        'sla_due_at',
        'lot_arrival_date',
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
