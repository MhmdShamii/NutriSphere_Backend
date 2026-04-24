<?php

namespace App\Http\Resources\Analytics;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MacrosDayResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'date'             => $this['date'],
            'protein_consumed' => $this['protein_consumed'],
            'protein_target'   => $this['protein_target'],
            'carbs_consumed'   => $this['carbs_consumed'],
            'carbs_target'     => $this['carbs_target'],
            'fats_consumed'    => $this['fats_consumed'],
            'fats_target'      => $this['fats_target'],
        ];
    }
}
