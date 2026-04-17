<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MealPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_profile_id',
        'fingerprint',
        'name',
        'description',
        'visibility',
        'image_url',
        'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    // Relations
    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'meal_ingredients')
            ->withPivot('portion', 'unit');
    }

    public function mealMacro()
    {
        return $this->belongsTo(MealMacro::class, 'fingerprint', 'fingerprint');
    }
}
