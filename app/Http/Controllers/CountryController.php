<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Services\CountryService;
use Illuminate\Http\Request;

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
}
