<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cctv_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cctv_id')->constrained()->onDelete('cascade');
            $table->string('event_type');
            $table->json('event_data')->nullable();
            $table->timestamp('timestamp');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('screenshot_path')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['cctv_id', 'event_type']);
            $table->index('timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cctv_logs');
    }
};