<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('page_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('version_number');
            $table->longText('content');
            $table->string('change_note')->nullable();
            $table->foreignId('saved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['page_id', 'version_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('page_versions');
    }
};