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
        Schema::create('referenced_caption_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caption_id')->constrained('captions')->onDelete('cascade');
            $table->string('partNumber');
            $table->string('orgCode', 10);
            $table->string('ieSystemControlNumber', 10)->nullable();
            $table->boolean('disambiguation')->default(false);
            $table->string('componentId', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referenced_caption_parts');
    }
};
