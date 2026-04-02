<?php

use App\Enums\UserActivityLevels;
use App\Enums\UserDietaryPreferences;
use App\Enums\UserGoal;
use App\Enums\UserOnboardingSteps;
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
        Schema::create('user_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->enum('activity_level', UserActivityLevels::cases())->nullable();
            $table->enum('goal', UserGoal::cases())->nullable();
            $table->enum('dietary_preferences', UserDietaryPreferences::cases())->default(UserDietaryPreferences::NONE);
            $table->unsignedInteger('daily_calorie_target')->nullable();
            $table->unsignedInteger('daily_protein_g')->nullable();
            $table->unsignedInteger('daily_carbs_g')->nullable();
            $table->unsignedInteger('daily_fat_g')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profile');
    }
};
