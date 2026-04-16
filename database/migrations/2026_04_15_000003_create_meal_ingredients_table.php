<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_ingredients', function (Blueprint $table) {
            $table->string('fingerprint', 64);
            $table->foreignId('ingredient_id')->constrained('ingredients')->restrictOnDelete();
            $table->decimal('quantity_g', 8, 2);

            $table->primary(['fingerprint', 'ingredient_id']);
            $table->foreign('fingerprint')->references('fingerprint')->on('meal_macros')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_ingredients');
    }
};
