<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            // Check if batch_uuid column exists, if not add it
            if (!Schema::hasColumn('activity_log', 'batch_uuid')) {
                $table->uuid('batch_uuid')->nullable()->after('causer_type');
            }
            
            // Check if event column exists, if not add it
            if (!Schema::hasColumn('activity_log', 'event')) {
                $table->string('event')->nullable()->after('batch_uuid');
            }
            
            // Check if subject_type column exists, if not add it
            if (!Schema::hasColumn('activity_log', 'subject_type')) {
                $table->string('subject_type')->nullable()->after('event');
            }
            
            // Check if subject_id column exists, if not add it
            if (!Schema::hasColumn('activity_log', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('subject_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropColumn(['batch_uuid', 'event', 'subject_type', 'subject_id']);
        });
    }
};
