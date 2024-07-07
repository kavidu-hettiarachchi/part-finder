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
        Schema::create('smcs_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained('parts')->onDelete('cascade');
            $table->string('code');
            $table->string('description');
            $table->string('language', 10)->default('en');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smcs_codes');
    }
};
