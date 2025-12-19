<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/materials/{material}/qr', [\App\Http\Controllers\MaterialQRController::class, 'show'])->name('materials.qr');
