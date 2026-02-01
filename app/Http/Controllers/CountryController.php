<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryUsersRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\CountryService;
use Illuminate\Http\Request;

use function Laravel\Prompts\error;

class CountryController extends Controller
{
    use ApiResponse;


    protected CountryService $countryService;
    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function getCountries()
    {
        return $this->success(
            $this->countryService->getCountries(),
            'Countries retrieved successfully'
        );
    }

    public function getCountryUsers(CountryUsersRequest $request)
    {
        $users = $this->countryService->getUsersForCountry($request->code);

        return $this->success(
            UserResource::collection($users),
            "Successful Users For " . $request->code
        );
    }
}
