<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_ingredients', function (Blueprint $table) {
            $table->foreignId('meal_post_id')->constrained('meal_posts')->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained('ingredients')->restrictOnDelete();
            $table->decimal('quantity_g', 8, 2);

            $table->primary(['meal_post_id', 'ingredient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_ingredients');
    }
};
