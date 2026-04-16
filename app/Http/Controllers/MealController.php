<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMealRequest;
use Illuminate\Http\Request;

class MealController extends Controller
{
    public function store(CreateMealRequest $request)
    {
        $validated = $request->validated();
        $profile = $request->user();
        dd($profile);

        return response()->json([
            'message' => 'Meal created successfully',
            'meal' => $validated,
        ], 201);
    }
}
