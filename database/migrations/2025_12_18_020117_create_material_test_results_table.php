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
        Schema::create('material_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->string('test_name', 150)->nullable();
            $table->string('test_method', 150)->nullable();
            $table->string('spec_min', 50)->nullable();
            $table->string('spec_max', 50)->nullable();
            $table->string('actual_value', 100)->nullable();
            $table->string('unit', 50)->nullable();
            $table->enum('result', ['pass', 'fail'])->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_test_results');
    }
};
