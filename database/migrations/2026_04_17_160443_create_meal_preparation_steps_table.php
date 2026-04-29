<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_preparation_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_post_id')->constrained('meal_posts')->cascadeOnDelete();
            $table->unsignedInteger('step_number');
            $table->text('description');
            $table->timestamps();

            $table->index('meal_post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_preparation_steps');
    }
};
