<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryUsersRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\CountryService;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CountryController extends Controller
{
    use ApiResponse;


    protected CountryService $countryService;
    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function getCountryUsers(CountryUsersRequest $request): ResourceCollection
    {
        $users = $this->countryService->getUsersForCountry($request->code);

        return $this->successResource(UserResource::collection($users), 'Users retrieved successfully');
    }
}
