<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_id',
        'lab_po_number',
        'po_number',
        'lot_number',
        'supplier',
        'country_of_supplier',
        'material_group',
        'material_type',
        'material_name',
        'color',
        'color_key',
        'mpn',
        'article_style',
        'component',
        'qty',
        'bm',
        'status',
        'date_incoming',
        'lab_received_at',
        'testing_started_at',
        'test_completed_at',
    ];

    protected $casts = [
        'status' => \App\Enums\MaterialStatus::class,
        'date_incoming' => 'date',
        'lab_received_at' => 'datetime',
        'testing_started_at' => 'datetime',
        'test_completed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($material) {
            $material->unique_id = (string) \Illuminate\Support\Str::uuid();
            if (empty($material->status)) {
                $material->status = \App\Enums\MaterialStatus::Scheduled;
            }
        });
    }

    public function testResults()
    {
        return $this->hasMany(MaterialTestResult::class);
    }

    public function testDocuments()
    {
        return $this->hasMany(MaterialTestDocument::class);
    }
}
