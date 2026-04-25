<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    protected function success(mixed $data = null, string $message = 'OK', string $dataKey = 'data', int $status = 200): JsonResponse
    {
        return response()->json([
            $dataKey => $data,
            'message' => $message,
        ], $status);
    }

    protected function successResource(ResourceCollection $resource, string $message = 'OK', int $status = 200): ResourceCollection
    {
        return $resource->additional([
            'message' => $message,
            'status' => $status
        ]);
    }

    protected function paginated(mixed $data, array $meta, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'data'    => $data,
            'meta'    => $meta,
            'message' => $message,
        ], $status);
    }

    protected function error(string $message = 'Error', int $status = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'data' => null,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
