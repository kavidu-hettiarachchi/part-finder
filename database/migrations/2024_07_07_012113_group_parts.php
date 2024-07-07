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
        Schema::create('group_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained('parts')->onDelete('cascade');
            $table->string('partNumber');
            $table->string('orgCode', 10);
            $table->string('partName');
            $table->string('partLanguage', 10)->default('en');
            $table->string('modifier')->nullable();
            $table->string('modifierLanguage')->default('en');
            $table->boolean('serviceabilityIndicator')->default(false);
            $table->string('alternatePartType')->nullable();
            $table->boolean('hasAlternate')->default(false);
            $table->boolean('isCCRPart')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_parts');
    }
};
