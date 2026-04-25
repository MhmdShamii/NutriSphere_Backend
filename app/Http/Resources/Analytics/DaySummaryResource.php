<?php

namespace App\Http\Resources\Analytics;

use App\Http\Resources\Meal\DailyLogResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DaySummaryResource extends JsonResource
{
    public function __construct($resource, private string $date)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        if ($this->resource === null) {
            return [
                'date'               => $this->date,
                'calories_consumed'  => 0,
                'protein_consumed'   => 0,
                'carbs_consumed'     => 0,
                'fats_consumed'      => 0,
                'fiber_consumed'     => 0,
                'calories_target'    => null,
                'protein_target'     => null,
                'carbs_target'       => null,
                'fats_target'        => null,
                'fiber_target'       => null,
                'logs_count'         => 0,
                'logs'               => [],
            ];
        }

        return [
            'date'               => $this->resource->date->toDateString(),
            'calories_consumed'  => (float) $this->calories_consumed,
            'protein_consumed'   => (float) $this->protein_consumed,
            'carbs_consumed'     => (float) $this->carbs_consumed,
            'fats_consumed'      => (float) $this->fats_consumed,
            'fiber_consumed'     => (float) $this->fiber_consumed,
            'calories_target'    => $this->calories_target  !== null ? (float) $this->calories_target  : null,
            'protein_target'     => $this->protein_target   !== null ? (float) $this->protein_target   : null,
            'carbs_target'       => $this->carbs_target     !== null ? (float) $this->carbs_target     : null,
            'fats_target'        => $this->fats_target      !== null ? (float) $this->fats_target      : null,
            'fiber_target'       => $this->fiber_target     !== null ? (float) $this->fiber_target     : null,
            'logs_count'         => $this->logs_count,
            'logs'               => DailyLogResource::collection($this->whenLoaded('logs')),
        ];
    }
}
