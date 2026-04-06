<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHealthCondition extends Model
{
    protected $fillable = [
        'user_id',
        'health_condition_id',
        'custom_condition',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function condition()
    {
        return $this->belongsTo(HealthCondition::class, 'health_condition_id');
    }
}
