<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealPreparationStep extends Model
{
    protected $fillable = [
        'meal_post_id',
        'step_number',
        'description',
    ];

    public function mealPost()
    {
        return $this->belongsTo(MealPost::class);
    }
}
