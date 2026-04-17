<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CreateMealService
{
    private ?Collection $cachedIngredients = null;
    private const UNIT_MAP = [
        'grams'       => 'g',
        'gram'        => 'g',
        'gr'          => 'g',
        'grm'         => 'g',
        'milliliter'  => 'ml',
        'millilitre'  => 'ml',
        'ml'          => 'ml',
        'tablespoon'  => 'tbsp',
        'tablespoons' => 'tbsp',
        'tbs'         => 'tbsp',
        'teaspoon'    => 'tsp',
        'teaspoons'   => 'tsp',
        'kilogram'    => 'kg',
        'kilograms'   => 'kg',
        'kilo'        => 'kg',
        'liter'       => 'l',
        'litre'       => 'l',
        'liters'      => 'l',
        'piece'       => 'piece',
        'pieces'      => 'piece',
        'pcs'         => 'piece',
        'pc'          => 'piece',
        'pce'         => 'piece',
        'cup'         => 'cup',
        'cups'        => 'cup',
        'ounce'       => 'oz',
        'ounces'      => 'oz',
        'oz'          => 'oz',
        'pound'       => 'lb',
        'pounds'      => 'lb',
        'lb'          => 'lb',
        'lbs'         => 'lb',
    ];

    private $openAiService;

    public function __construct(private OpenAiService $openAi)
    {
        $this->openAiService = $openAi;
    }

    public function create(UserProfile $profile, array $validated): array
    {
        foreach ($validated['ingredients'] as &$ingredient) {
            $ingredient['unit'] = $this->normalizeUnit($ingredient['unit']);
        }
        unset($ingredient);

        $normalizedIngredients = $this->normalizeIngredients($validated['ingredients']);

        return DB::transaction(function () use ($profile, $validated, $normalizedIngredients) {
            $resolvedIngredients = $this->resolveIngredients($normalizedIngredients);
            dd($resolvedIngredients);
        });
    }

    private function resolveIngredients(array $normalizedIngredients): array
    {
        $resolved   = [];
        $unresolved = [];

        foreach ($normalizedIngredients as $item) {
            $match = $this->fuzzyCheckExistingIngredients($item['name']);
            if ($match) {
                $resolved[] = [
                    'input'      => $item['name'],
                    'ingredient' => $match,
                    'portion'    => $item['portion'],
                    'unit'       => $item['unit'],
                ];
            } else {
                $unresolved[] = $item;
            }
        }

        if (!empty($unresolved)) {
            $resolved = array_merge($resolved, $this->resolveIngrediantsViaOpenAI($unresolved));
        }

        return $resolved;
    }

    private function resolveIngrediantsViaOpenAI(array $unresolved): array
    {
        $names = array_column($unresolved, 'name');
        $items = $this->openAi->resolveIngredientNames($names);


        $unresolvedByName = array_column($unresolved, null, 'name');

        $resolved = [];

        foreach ($items as $item) {
            $original = $unresolvedByName[$item['input']] ?? null;

            $match = $this->fuzzyCheckExistingIngredients($item['name_en']);

            if (!$match) {
                $match = Ingredient::create([
                    'name_en'  => $item['name_en'],
                    'name_ar'  => data_get($item, 'name_ar'),
                    'source'   => 'user',
                    'verified' => false,
                ]);
            }

            $resolved[] = [
                'input'      => $item['input'],
                'ingredient' => $match,
                'portion'    => data_get($original, 'portion', 0),
                'unit'       => data_get($original, 'unit', 'g'),
            ];
        }

        return $resolved;
    }

    private function normalizeUnit(string $unit): string
    {
        $unit = strtolower(trim($unit));

        return self::UNIT_MAP[$unit] ?? $unit;
    }

    private function normalizeIngredients(array $ingredients): array
    {
        foreach ($ingredients as &$ingredient) {
            $name = strtolower(trim($ingredient['name']));
            $name = preg_replace('/\s+/', ' ', $name);
            $name = preg_replace('/[^\p{L}\p{Arabic} ]/u', '', $name);
            $ingredient['name'] = $name;
        }
        unset($ingredient);

        usort($ingredients, fn($a, $b) => strcmp($a['name'], $b['name']));

        return $ingredients;
    }

    private function fuzzyCheckExistingIngredients(string $input): ?Ingredient
    {
        if ($this->cachedIngredients === null) {
            $this->cachedIngredients = Ingredient::all();
        }

        $bestScore      = 0;
        $bestIngredient = null;

        foreach ($this->cachedIngredients as $ingredient) {
            similar_text($input, $ingredient->name_en, $percentEn);
            similar_text($input, $ingredient->name_ar ?? '', $percentAr);

            $score = max($percentEn, $percentAr);

            if ($score > $bestScore) {
                $bestScore      = $score;
                $bestIngredient = $ingredient;
            }
        }
        return $bestScore >= 85 ? $bestIngredient : null;
    }
}
