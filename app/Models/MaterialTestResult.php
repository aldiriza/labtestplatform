<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialTestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'test_name',
        'test_method',
        'spec_min',
        'spec_max',
        'actual_value',
        'unit',
        'result',
        'remark',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
