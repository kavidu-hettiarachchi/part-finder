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
        Schema::create('captions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained('parts')->onDelete('cascade');
            $table->text('name');
            $table->string('language', 10)->default('en');
            $table->string('orgCode', 10);
            $table->string('kitsLinkedMediaNumber', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('captions');
    }
};
