<?php

namespace App\Models;

use App\Enums\MeasurementUnit;
use Illuminate\Database\Eloquent\Model;

class MealPostIngredient extends Model
{
    protected $table = 'meal_ingredients';

    public $incrementing = false;
    public $timestamps   = false;

    // Use one part of the composite key so Eloquent doesn't complain;
    // create() is the only operation needed on this model.
    protected $primaryKey = 'meal_post_id';

    protected $fillable = [
        'meal_post_id',
        'ingredient_id',
        'portion',
        'unit',
    ];

    protected $casts = [
        'unit' => MeasurementUnit::class,
    ];
}
