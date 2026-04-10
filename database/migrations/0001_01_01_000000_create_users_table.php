<?php

use App\Enums\UserOnboardingSteps;
use App\Enums\UserProvider;
use App\Enums\UserRole;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string("last_name")->nullable();
            $table->string('email')->unique();
            $table->enum('provider', UserProvider::cases())->default(UserProvider::LOCAL);
            $table->string("provider_id")->nullable();
            $table->enum('role', UserRole::cases())->default(UserRole::CLIENT);
            $table->enum('onboarding_step', UserOnboardingSteps::cases())->default(UserOnboardingSteps::MAIN_INFO);
            $table->foreignId('country_id')->nullable()->constrained('countries')->restrictOnDelete();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->string("image")->nullable()->default('default.png');
            $table->string('cover_image')->nullable()->default('default_cover.png');
            $table->unique(['provider', 'provider_id']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
