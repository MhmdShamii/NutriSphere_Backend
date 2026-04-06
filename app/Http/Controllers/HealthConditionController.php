<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddHealthConditionRequest;
use App\Http\Resources\HealthConditionResource;
use App\Http\Resources\UserHealthConditionResource;
use App\Http\Responses\ApiResponse;
use App\Services\HealthConditionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HealthConditionController extends Controller
{
    use ApiResponse;

    public function __construct(private HealthConditionService $healthConditionService) {}

    public function index(): JsonResponse
    {
        $conditions = $this->healthConditionService->getAll();

        return $this->success(HealthConditionResource::collection($conditions), dataKey: 'conditions');
    }

    public function userConditions(Request $request): JsonResponse
    {
        $conditions = $this->healthConditionService->getUserConditions($request->user());

        return $this->success(UserHealthConditionResource::collection($conditions), dataKey: 'conditions');
    }

    public function add(AddHealthConditionRequest $request): JsonResponse
    {
        $condition = $this->healthConditionService->addCondition($request->user(), $request->validated());

        return $this->success(new UserHealthConditionResource($condition->load('condition')), 'Condition added successfully', 'condition', 201);
    }

    public function completeHealthConditions(Request $request): JsonResponse
    {
        $this->healthConditionService->completeHealthConditions($request->user());

        return $this->success(message: 'Health conditions step completed successfully');
    }

    public function remove(Request $request, int $id): JsonResponse
    {
        $this->healthConditionService->removeCondition($request->user(), $id);

        return $this->success(message: 'Condition removed successfully');
    }
}
