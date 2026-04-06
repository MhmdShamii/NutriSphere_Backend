<?php

namespace App\Models;

use App\Enums\HealthConditionSeverity;
use App\Enums\HealthConditionType;
use Illuminate\Database\Eloquent\Model;

class HealthCondition extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'severity',
    ];

    protected $casts = [
        'type'     => HealthConditionType::class,
        'severity' => HealthConditionSeverity::class,
    ];
}
