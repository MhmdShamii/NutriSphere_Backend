<?php

namespace App\Http\Resources\Analytics;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaloriesDayResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'date'               => $this['date'],
            'calories_consumed'  => $this['calories_consumed'],
            'calories_target'    => $this['calories_target'],
        ];
    }
}
