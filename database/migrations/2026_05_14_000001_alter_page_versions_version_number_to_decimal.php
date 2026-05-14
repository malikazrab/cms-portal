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
        Schema::table('page_versions', function (Blueprint $table) {
            $table->decimal('version_number', 3, 1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_versions', function (Blueprint $table) {
            $table->unsignedSmallInteger('version_number')->change();
        });
    }
};