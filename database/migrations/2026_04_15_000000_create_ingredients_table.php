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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 255);
            $table->string('name_ar', 255)->nullable();
            $table->enum('source', ['system', 'user'])->default('user');
            $table->boolean('verified')->default(false);
            $table->timestamps();

            $table->index('name_en');
            $table->index('name_ar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
