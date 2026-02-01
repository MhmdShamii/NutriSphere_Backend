<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class country extends Model
{

    protected $fillable = [
        'code',
        'name',
        'phone_code'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
