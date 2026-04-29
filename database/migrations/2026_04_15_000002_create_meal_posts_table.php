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
        Schema::create('meal_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_profile_id')->constrained('user_profile')->restrictOnDelete();
            $table->string('fingerprint', 64)->foreign()->references('fingerprint')->on('meal_macros')->restrictOnDelete();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->string('image_url')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('user_profile_id');
            $table->index('visibility');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_posts');
    }
};
