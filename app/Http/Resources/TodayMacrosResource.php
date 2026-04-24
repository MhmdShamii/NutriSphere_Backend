<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodayMacrosResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isProfile = $this->resource instanceof \App\Models\UserProfile;

        return [
            'source'            => $isProfile ? 'profile' : 'summary',
            'date'              => now()->toDateString(),
            'calories_consumed' => $isProfile ? 0 : (float) $this->calories_consumed,
            'calories_target'   => $isProfile ? $this->floatOrNull($this->daily_calorie_target) : (float) $this->calories_target,
            'protein_consumed'  => $isProfile ? 0 : (float) $this->protein_consumed,
            'protein_target'    => $isProfile ? $this->floatOrNull($this->daily_protein_g)      : (float) $this->protein_target,
            'carbs_consumed'    => $isProfile ? 0 : (float) $this->carbs_consumed,
            'carbs_target'      => $isProfile ? $this->floatOrNull($this->daily_carbs_g)        : (float) $this->carbs_target,
            'fats_consumed'     => $isProfile ? 0 : (float) $this->fats_consumed,
            'fats_target'       => $isProfile ? $this->floatOrNull($this->daily_fat_g)          : (float) $this->fats_target,
            'fiber_consumed'    => $isProfile ? 0 : (float) $this->fiber_consumed,
            'fiber_target'      => null,
        ];
    }

    private function floatOrNull($value): ?float
    {
        return $value !== null ? (float) $value : null;
    }
}
