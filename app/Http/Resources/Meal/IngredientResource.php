<?php

namespace App\Http\Resources\Meal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name_en'  => $this->name_en,
            'name_ar'  => $this->name_ar,
            'verified' => $this->verified,
        ];
    }
}
