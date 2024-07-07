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
        Schema::create('image_identifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained('parts')->onDelete('cascade');
            $table->foreignId('line_item_id')->nullable()->constrained('line_items')->onDelete('cascade');
            $table->string('imageId');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_identifiers');
    }
};
