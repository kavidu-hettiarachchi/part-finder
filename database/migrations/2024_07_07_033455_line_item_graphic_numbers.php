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
        Schema::create('line_item_graphic_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_item_id')->constrained('line_items')->onDelete('cascade');
            $table->string('graphicNumber');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_item_graphic_numbers');
    }
};
