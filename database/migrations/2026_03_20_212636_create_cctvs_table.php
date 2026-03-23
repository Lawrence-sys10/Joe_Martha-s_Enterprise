<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cctvs', function (Blueprint $table) {
            $table->id();
            $table->string('camera_name');
            $table->string('camera_ip')->nullable();
            $table->string('camera_location');
            $table->text('stream_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('recording_enabled')->default(true);
            $table->boolean('motion_detection')->default(true);
            $table->timestamp('last_checked_at')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['camera_location', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cctvs');
    }
};