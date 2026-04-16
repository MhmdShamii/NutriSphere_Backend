<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMealRequest;
use App\Services\MealService;
use Illuminate\Http\JsonResponse;

class MealController extends Controller
{
    public function __construct(
        private MealService $mealService,
    ) {}

    public function store(CreateMealRequest $request): JsonResponse
    {
        $profile = auth()->user()->profile;

        $result = $this->mealService->create(
            $profile,
            $request->validated(),
            $request->file('image')
        );

        return response()->json([
            'status'  => 'normalized',
            'message' => 'Ingredients normalized successfully',
            'data'    => $result,
        ], 200);
    }
}
