<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailySummary extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'calories_consumed',
        'protein_consumed',
        'carbs_consumed',
        'fats_consumed',
        'fiber_consumed',
        'calories_target',
        'protein_target',
        'carbs_target',
        'fats_target',
        'fiber_target',
        'logs_count',
    ];

    protected $casts = [
        'date'               => 'date',
        'calories_consumed'  => 'decimal:2',
        'protein_consumed'   => 'decimal:2',
        'carbs_consumed'     => 'decimal:2',
        'fats_consumed'      => 'decimal:2',
        'fiber_consumed'     => 'decimal:2',
        'calories_target'    => 'decimal:2',
        'protein_target'     => 'decimal:2',
        'carbs_target'       => 'decimal:2',
        'fats_target'        => 'decimal:2',
        'fiber_target'       => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(DailyLog::class);
    }
}
