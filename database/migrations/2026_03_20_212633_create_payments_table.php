<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained();
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'mobile_money', 'bank', 'credit']);
            $table->string('reference_number')->nullable();
            $table->datetime('payment_date');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->string('transaction_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['sale_id', 'payment_method']);
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};