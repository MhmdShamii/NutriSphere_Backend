<?php

namespace App\Services\Meal;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Collection;

class IngredientService
{
    public function search(string $query): Collection
    {
        $lower = strtolower($query);

        return Ingredient::whereRaw('LOWER(name_en) LIKE ?', ["%{$lower}%"])
            ->orWhereRaw('LOWER(name_ar) LIKE ?', ["%{$lower}%"])
            ->orderByRaw("CASE WHEN LOWER(name_en) LIKE ? THEN 0 ELSE 1 END", ["{$lower}%"])
            ->limit(10)
            ->get();
    }
}
