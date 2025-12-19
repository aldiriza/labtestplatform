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
            // Re-adding/Renaming columns to match exacting requirement
            // If item_description exists, rename it back to material_name or use it. 
            // Previous migration renamed material_name to item_description.
            // Let's assume we want 'material_name' as per spec.
            if (Schema::hasColumn('materials', 'item_description')) {
                $table->renameColumn('item_description', 'material_name');
            }
            
            // Add new master data fields if they don't exist
            $table->string('lab_po_number')->nullable()->after('unique_id');
            // po_number might exist, check
            if (!Schema::hasColumn('materials', 'po_number')) {
                $table->string('po_number')->nullable()->after('lab_po_number');
            }
            if (!Schema::hasColumn('materials', 'lot_number')) {
                $table->string('lot_number')->nullable()->after('po_number');
            }
            if (!Schema::hasColumn('materials', 'supplier')) {
                $table->string('supplier')->nullable()->after('lot_number');
            }

            $table->string('country_of_supplier')->nullable()->after('supplier');
            $table->string('material_group')->nullable()->after('country_of_supplier');
            $table->string('material_type')->nullable()->after('material_group');
            
            // material_name handled above
            
            $table->string('color')->nullable()->after('material_name'); // Was dropped, adding back
            $table->string('color_key')->nullable()->after('color');
            $table->string('mpn')->nullable()->after('color_key'); // Manufacturer Part Number
            $table->string('article_style')->nullable()->after('mpn');
            $table->string('component')->nullable()->after('article_style');
            
            // qty likely maps to quantity or we add new. 
            // Material model has 'quantity', check migration. 
            // Assuming we use 'qty' as per spec or map 'quantity' to 'qty'.
            // Let's add 'qty' distinct or rename. Model said 'quantity'.
            // I will add 'qty' to be precise to requirements, or rename quantity. 
            // Let's rename 'quantity' to 'qty' if it exists to be clean.
            if (Schema::hasColumn('materials', 'quantity')) {
                $table->renameColumn('quantity', 'qty');
            } else {
                $table->integer('qty')->nullable()->after('component');
            }

            $table->string('bm')->nullable()->after('qty'); // basic measure? or business manager?

            // Timing
            $table->date('date_incoming')->nullable();
            $table->time('time_incoming')->nullable();
            
            // lab_received_at, testing_started_at, test_completed_at might exist.
            if (!Schema::hasColumn('materials', 'testing_started_at')) {
                $table->dateTime('testing_started_at')->nullable();
            }
            
            // Update status enum if needed is hard in standard migration without raw SQL.
            // But we can just ensure the column is string/enum.
            // status exists.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            // Simplified down
            $table->dropColumn([
                'lab_po_number', 'country_of_supplier', 'material_group', 
                'material_type', 'color_key', 'mpn', 'article_style', 
                'component', 'bm', 'date_incoming', 'time_incoming', 
                'testing_started_at'
            ]);
            if (Schema::hasColumn('materials', 'qty')) {
                $table->renameColumn('qty', 'quantity');
            }
            // renaming material_name back to item_description is optional for down
        });
    }
};
