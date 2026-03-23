<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('supplier_sku')->nullable();
            $table->integer('pack_quantity')->default(1);
            $table->string('pack_unit')->default('piece');
            $table->decimal('pack_price', 15, 2);
            $table->decimal('unit_price', 15, 2);
            $table->integer('minimum_order_quantity')->default(1);
            $table->integer('lead_time_days')->default(7);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
            
            $table->unique(['supplier_id', 'product_id']);
            $table->index(['supplier_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_products');
    }
};