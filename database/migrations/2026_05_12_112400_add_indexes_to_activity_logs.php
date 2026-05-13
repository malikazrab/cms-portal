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
        // First, check if the activity_logs table exists
        if (!Schema::hasTable('activity_logs')) {
            return;
        }

        Schema::table('activity_logs', function (Blueprint $table) {
            // Check if columns exist before adding indexes
            if (Schema::hasColumn('activity_logs', 'model_type') && Schema::hasColumn('activity_logs', 'model_id')) {
                $table->index(['model_type', 'model_id'], 'activity_logs_model_index');
            }
            
            if (Schema::hasColumn('activity_logs', 'user_id')) {
                $table->index('user_id', 'activity_logs_user_index');
            }
            
            if (Schema::hasColumn('activity_logs', 'action')) {
                $table->index('action', 'activity_logs_action_index');
            }
            
            if (Schema::hasColumn('activity_logs', 'created_at')) {
                $table->index('created_at', 'activity_logs_created_at_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('activity_logs')) {
            return;
        }

        Schema::table('activity_logs', function (Blueprint $table) {
            // Drop indexes if they exist
            if (Schema::hasColumn('activity_logs', 'model_type') && Schema::hasColumn('activity_logs', 'model_id')) {
                $table->dropIndex('activity_logs_model_index');
            }
            
            if (Schema::hasColumn('activity_logs', 'user_id')) {
                $table->dropIndex('activity_logs_user_index');
            }
            
            if (Schema::hasColumn('activity_logs', 'action')) {
                $table->dropIndex('activity_logs_action_index');
            }
            
            if (Schema::hasColumn('activity_logs', 'created_at')) {
                $table->dropIndex('activity_logs_created_at_index');
            }
        });
    }
};