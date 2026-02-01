<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryUsersRequest;
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
        return $this->success(
            $this->countryService->getUsersForCountry($request->code),
            "Successful Users For " . $request->code
        );
    }
}
