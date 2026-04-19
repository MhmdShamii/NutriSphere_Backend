<?php

namespace App\Models;

use App\Enums\IngredientSource;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'source',
        'verified',
    ];

    protected $casts = [
        'source'  => IngredientSource::class,
        'verified' => 'boolean',
    ];

    // Relations
    public function mealPosts()
    {
        return $this->belongsToMany(MealPost::class, 'meal_ingredients')
            ->withPivot('quantity_g');
    }
}
