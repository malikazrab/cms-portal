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
        Schema::create('page_versions', function (Blueprint $table) {
            // Primary key
            $table->id(); // BIGINT UNSIGNED auto-increment

            // Foreign key to pages table (cascade delete)
            $table->unsignedBigInteger('page_id');
            $table->foreign('page_id')
                  ->references('id')
                  ->on('pages')
                  ->cascadeOnDelete();

            // Version number (increments per page)
            $table->unsignedSmallInteger('version_number');

            // Content snapshot (JSON)
            $table->longText('content');

            // Optional change note
            $table->string('change_note', 255)->nullable();

            // Who saved this version (null if user deleted)
            $table->unsignedBigInteger('saved_by')->nullable();
            $table->foreign('saved_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            // Created timestamp (no updated_at)
            $table->timestamp('created_at');

            // Required indexes
            $table->index(['page_id', 'version_number']);
            $table->index('page_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_versions');
    }
};