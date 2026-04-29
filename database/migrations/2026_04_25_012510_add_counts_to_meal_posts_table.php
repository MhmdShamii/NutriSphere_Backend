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
        Schema::table('meal_posts', function (Blueprint $table) {
            $table->unsignedInteger('likes_count')->default(0)->after('confirmed_at');
            $table->unsignedInteger('relogs_count')->default(0)->after('likes_count');
            $table->unsignedInteger('comments_count')->default(0)->after('relogs_count');
        });
    }

    public function down(): void
    {
        Schema::table('meal_posts', function (Blueprint $table) {
            $table->dropColumn(['likes_count', 'relogs_count', 'comments_count']);
        });
    }
};
