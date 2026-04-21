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
        Schema::create('daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');

            $table->decimal('calories_consumed', 8, 2)->default(0);
            $table->decimal('protein_consumed', 8, 2)->default(0);
            $table->decimal('carbs_consumed', 8, 2)->default(0);
            $table->decimal('fats_consumed', 8, 2)->default(0);
            $table->decimal('fiber_consumed', 8, 2)->default(0);
            $table->decimal('calories_target', 8, 2)->default(0);
            $table->decimal('protein_target', 8, 2)->default(0);
            $table->decimal('carbs_target', 8, 2)->default(0);
            $table->decimal('fats_target', 8, 2)->default(0);
            $table->decimal('fiber_target', 8, 2)->default(0);
            $table->unsignedInteger('logs_count')->default(0);


            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_summaries');
    }
};
