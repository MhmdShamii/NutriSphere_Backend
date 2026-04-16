<?php

namespace App\Services;

use App\Models\UserProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MealService
{

    public function create(UserProfile $profile, array $validated, UploadedFile $image): array
    {
        $imageData = $this->uploadImage($image);

        foreach ($validated['ingredients'] as &$ingredient) {
            $ingredient['unit'] = $this->normalizeUnit($ingredient['unit']);
        }
        unset($ingredient);

        $normalizedIngredients = $this->normalizeIngredients($validated['ingredients']);

        return [
            'status'      => 'normalized',
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'visibility'  => $validated['visibility'],
            'image'       => $imageData,
            'ingredients' => $normalizedIngredients,
        ];
    }

    // Helper methods

    private function uploadImage(UploadedFile $file): array
    {
        $path = $file->store('meals', 'public');
        $url  = Storage::disk('public')->url($path);

        return [
            'photo_path' => $path,
            'photo_url'  => $url,
        ];
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

    private function normalizeUnit(string $unit): string
    {
        $unit = strtolower(trim($unit));

        return self::UNIT_MAP[$unit] ?? $unit;
    }
}
