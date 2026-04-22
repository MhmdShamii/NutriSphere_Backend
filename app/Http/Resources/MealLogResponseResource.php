<?php

namespace App\Http\Resources;

use App\Models\DailyLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealLogResponseResource extends JsonResource
{
    public function __construct(
        private DailyLog $log,
        private ?array $warning,
    ) {
        parent::__construct($log);
    }

    public function toArray(Request $request): array
    {
        return [
            'logged_meal'    => new DailyLogResource($this->log),
            'health_warning' => $this->warning,
        ];
    }
}
