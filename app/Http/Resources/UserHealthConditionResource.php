<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserHealthConditionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'custom_condition' => $this->custom_condition,
            'condition'        => $this->whenLoaded('condition', fn() => new HealthConditionResource($this->condition)),
        ];
    }
}
