<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyLog extends Model
{
    protected $fillable = [
        'user_id',
        'daily_summary_id',
        'logged_at',
        'type',
        'meal_post_id',
        'log_name',
        'fingerprint',
        'description',
        'calories',
        'protein',
        'carbs',
        'fats',
        'fiber',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
        'calories'  => 'decimal:2',
        'protein'   => 'decimal:2',
        'carbs'     => 'decimal:2',
        'fats'      => 'decimal:2',
        'fiber'     => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dailySummary()
    {
        return $this->belongsTo(DailySummary::class);
    }

    public function mealPost()
    {
        return $this->belongsTo(MealPost::class);
    }
}
