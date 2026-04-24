<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'type'         => $this->type,
            'log_name'     => $this->log_name,
            'description'  => $this->description,
            'calories'     => $this->calories,
            'protein'      => $this->protein,
            'carbs'        => $this->carbs,
            'fats'         => $this->fats,
            'fiber'        => $this->fiber,
            'logged_at'    => $this->logged_at,
            'confirmed_at' => $this->confirmed_at,
            'meal_post'    => $this->whenLoaded('mealPost', fn() => [
                'id'        => $this->mealPost->id,
                'image_url' => $this->mealPost->image_url,
            ]),
        ];
    }
}
