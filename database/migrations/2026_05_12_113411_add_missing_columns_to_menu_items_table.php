<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            // Add title column if missing
            if (!Schema::hasColumn('menu_items', 'title')) {
                $table->string('title')->after('menu_id');
            }
            
            // Add url column if missing
            if (!Schema::hasColumn('menu_items', 'url')) {
                $table->string('url')->nullable()->after('title');
            }
            
            // Add order column if missing
            if (!Schema::hasColumn('menu_items', 'order')) {
                $table->integer('order')->default(0)->after('url');
            }
            
            // Add parent_id column if missing
            if (!Schema::hasColumn('menu_items', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('order');
            }
        });
    }

    public function down(): void
    {
        // We don't want to accidentally drop columns in down()
        // This is a data fix migration
    }
};