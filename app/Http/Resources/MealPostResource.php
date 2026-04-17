<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'visibility'  => $this->visibility,
            'image_url'   => $this->image_url,
            'confirmed'   => $this->confirmed_at !== null,
            'ingredients' => $this->ingredients->map(fn($ingredient) => [
                'id'      => $ingredient->id,
                'name_en' => $ingredient->name_en,
                'name_ar' => $ingredient->name_ar,
                'portion' => $ingredient->pivot->portion,
                'unit'    => $ingredient->pivot->unit,
            ]),
            'macros' => $this->whenLoaded('mealMacro', fn() => [
                'calories' => $this->mealMacro->calories,
                'protein'  => $this->mealMacro->protein,
                'carbs'    => $this->mealMacro->carbs,
                'fats'     => $this->mealMacro->fats,
                'fiber'    => $this->mealMacro->fiber,
            ]),
        ];
    }
}
