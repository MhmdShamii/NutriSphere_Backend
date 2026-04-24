<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CountryUsersRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\User\CountryService;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CountryController extends Controller
{
    use ApiResponse;


    protected CountryService $countryService;
    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function index(CountryUsersRequest $request): ResourceCollection
    {
        $users = $this->countryService->getUsersForCountry($request->code);

        return $this->successResource(UserResource::collection($users), 'Users retrieved successfully');
    }
}
