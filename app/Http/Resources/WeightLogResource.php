<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeightLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'weight_kg' => $this->weight_kg,
            'note'      => $this->note,
            'logged_at' => $this->logged_at->toDateString(),
        ];
    }
}
