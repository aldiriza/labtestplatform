<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialQRController extends Controller
{
    public function show(Material $material)
    {
        return view('materials.qr', compact('material'));
    }
}
