<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('material_name');
            $table->string('supplier');
            $table->string('color');
            $table->string('shoe_style');
            $table->string('article_no');
            $table->string('po_number');
            $table->string('lot_number');
            $table->integer('quantity');
            $table->string('status')->default('incoming'); // incoming, received_at_lab, testing_in_progress, completed, rejected
            $table->string('test_result')->nullable(); // pass, fail
            $table->text('test_remarks')->nullable();
            $table->timestamp('lab_received_at')->nullable();
            $table->timestamp('test_completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
