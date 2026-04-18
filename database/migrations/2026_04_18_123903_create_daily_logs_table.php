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
        Schema::create('daily_logs', function (Blueprint $table) {

            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('daily_summary_id')->constrained("daily_summaries")->onDelete('cascade');
            $table->dateTime('logged_at');
            $table->enum('type', ['meal', 'custom', "estimate"]);
            $table->foreignId('meal_post_id')->nullable()->constrained("meal_posts")->nullOnDelete();
            $table->string('log_name')->nullable();
            $table->string('fingerprint')->nullable();
            $table->text('description')->nullable();
            $table->decimal('calories', 8, 2);
            $table->decimal('protein', 8, 2);
            $table->decimal('carbs', 8, 2);
            $table->decimal('fats', 8, 2);
            $table->decimal('fiber', 8, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_logs');
    }
};
