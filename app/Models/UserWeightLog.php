<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWeightLog extends Model
{
    protected $fillable = ['user_id', 'weight_kg', 'logged_at', 'note'];

    protected $casts = ['logged_at' => 'date'];
}
