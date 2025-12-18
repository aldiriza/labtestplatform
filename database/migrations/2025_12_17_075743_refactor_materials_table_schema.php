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
        Schema::table('materials', function (Blueprint $table) {
            $table->renameColumn('material_name', 'item_description');
            
            $table->string('part_number')->nullable()->after('unique_id')->index();
            $table->string('specification')->nullable()->after('part_number');
            $table->string('brand')->nullable()->after('specification');
            $table->string('category')->nullable()->after('brand')->index();
            $table->string('unit')->nullable()->after('category');
            $table->string('location')->nullable()->after('unit')->index();
            $table->integer('minimum_stock')->default(0)->after('location');

            $table->dropColumn(['color', 'shoe_style', 'article_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->renameColumn('item_description', 'material_name');
            
            $table->dropColumn(['part_number', 'specification', 'brand', 'category', 'unit', 'location', 'minimum_stock']);

            $table->string('color')->nullable();
            $table->string('shoe_style')->nullable();
            $table->string('article_no')->nullable();
        });
    }
};
