<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained('parts')->onDelete('cascade');
            $table->string('noteCodes', 10)->nullable();
            $table->string('partNumber');
            $table->string('orgCode', 10);
            $table->string('partName')->nullable();
            $table->string('partNameLanguage', 10)->default('en');
            $table->boolean('serviceabilityIndicator')->default(false);
            $table->integer('partSequenceNumber');
            $table->integer('parentage');
            $table->string('quantity')->nullable();
            $table->string('ieSystemControlNumber')->nullable();
            $table->boolean('disambiguation')->default(false);
            $table->string('mediaNumber')->nullable();
            $table->string('componentId')->nullable();
            $table->text('comments')->nullable();
            $table->string('referenceNumber')->nullable();
            $table->string('alternatePartType')->nullable();
            $table->string('modifier')->nullable();
            $table->string('modifierLanguage', 10);
            $table->boolean('isCCRPart')->default(false);
            $table->boolean('hasAlternate')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_items');
    }
};
