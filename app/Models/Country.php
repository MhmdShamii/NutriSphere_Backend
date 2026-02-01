<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class country extends Model
{

    protected $fillable = [
        'name',
        'code',
        'phone_code'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    //scopes
    public function scopeFindByCode($query, $code)
    {
        return $query->where('code', $code);
    }
}
