<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            // Add cost_price column after quantity
            if (!Schema::hasColumn('purchase_items', 'cost_price')) {
                $table->decimal('cost_price', 15, 2)->after('quantity')->nullable();
            }
            
            // Optionally, rename unit_price to selling_price for clarity, or keep as is
            // If you want to rename unit_price to selling_price:
            // $table->renameColumn('unit_price', 'selling_price');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_items', 'cost_price')) {
                $table->dropColumn('cost_price');
            }
        });
    }
};