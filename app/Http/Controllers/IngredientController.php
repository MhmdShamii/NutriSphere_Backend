<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchIngredientRequest;
use App\Http\Resources\IngredientResource;
use App\Http\Responses\ApiResponse;
use App\Services\IngredientService;
use Illuminate\Http\JsonResponse;

class IngredientController extends Controller
{
    use ApiResponse;

    public function __construct(private IngredientService $ingredientService) {}

    public function search(SearchIngredientRequest $request): JsonResponse
    {
        $ingredients = $this->ingredientService->search($request->input('query'));

        return $this->success(
            IngredientResource::collection($ingredients),
            'Ingredients fetched successfully',
            'ingredients'
        );
    }
}
