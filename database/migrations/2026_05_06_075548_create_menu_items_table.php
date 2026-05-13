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
        // Check if table already exists before trying to create
        if (!Schema::hasTable('menu_items')) {
            Schema::create('menu_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_id')
                    ->constrained('menus')
                    ->onDelete('cascade');
                $table->foreignId('parent_id')
                    ->nullable()
                    ->constrained('menu_items')
                    ->onDelete('cascade');
                $table->string('label', 200);
                $table->string('url', 500)->nullable();
                $table->foreignId('page_id')
                    ->nullable()
                    ->constrained('pages')
                    ->onDelete('set null');
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
                
                // Composite index for ordered item fetch
                $table->index(['menu_id', 'sort_order']);
                // Index for fast child lookup
                $table->index('parent_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};