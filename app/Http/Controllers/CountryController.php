<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryUsersRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\CountryService;
use Illuminate\Http\JsonResponse;


class CountryController extends Controller
{
    use ApiResponse;


    protected CountryService $countryService;
    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function getCountryUsers(CountryUsersRequest $request): JsonResponse
    {
        $users = $this->countryService->getUsersForCountry($request->code);
        $payload = UserResource::collection($users)->response()->getData(true);

        return $this->success(
            [
                'users' => $payload['data'],
                'links' => $payload['links'],
                'meta' => $payload['meta']
            ],
            "Successful Users For " . $request->code
        );
    }
}
