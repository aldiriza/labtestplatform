<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

try {
    for ($i = 0; $i < 100; $i++) {
        $material = \App\Models\Material::factory()->create();
        echo "Success: Created Material ID " . $material->id . "\n";
    }
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    // echo "Trace: " . $e->getTraceAsString() . "\n";
}
