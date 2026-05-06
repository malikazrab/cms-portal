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
        Schema::create('header_footer_templates', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['header', 'footer']);
            $table->string('name', 150);
            $table->longText('content');
            $table->tinyInteger('is_default')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Composite index for fast lookup of default header/footer
            $table->index(['type', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_footer_templates');
    }
};
