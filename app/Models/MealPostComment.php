<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealPostComment extends Model
{
    protected $fillable = ['meal_post_id', 'user_id', 'parent_id', 'body'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mealPost(): BelongsTo
    {
        return $this->belongsTo(MealPost::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(MealPostComment::class, 'parent_id')->oldest();
    }

    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }
}
