<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 'lab_ready_for_pickup' -> 'arrived'
        \Illuminate\Support\Facades\DB::table('materials')
            ->where('status', 'lab_ready_for_pickup')
            ->update(['status' => 'arrived']);

        // 'lab_in_progress' -> 'in_progress'
        \Illuminate\Support\Facades\DB::table('materials')
            ->where('status', 'lab_in_progress')
            ->update(['status' => 'in_progress']);

        // 'received_at_lab' -> 'lab_received'
        \Illuminate\Support\Facades\DB::table('materials')
            ->where('status', 'received_at_lab')
            ->update(['status' => 'lab_received']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed
    }
};
