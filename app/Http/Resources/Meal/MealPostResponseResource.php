<?php

namespace App\Http\Resources\Meal;

use App\Models\MealPost;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealPostResponseResource extends JsonResource
{
    public function __construct(
        private MealPost $meal,
        private ?array $warning,
    ) {
        parent::__construct($meal);
    }

    public function toArray(Request $request): array
    {
        return [
            'meal'           => new MealPostResource($this->meal),
            'health_warning' => $this->warning,
        ];
    }
}
