<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealMacro extends Model
{
    protected $fillable = [
        'fingerprint',
        'calories',
        'protein',
        'carbs',
        'fats',
        'fiber',
    ];

    protected $casts = [
        'calories' => 'decimal:2',
        'protein'  => 'decimal:2',
        'carbs'    => 'decimal:2',
        'fats'     => 'decimal:2',
        'fiber'    => 'decimal:2',
    ];

    // Relations
    public function mealPosts()
    {
        return $this->hasMany(MealPost::class, 'fingerprint', 'fingerprint');
    }
}
