<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryUsersRequest;
use App\Http\Responses\ApiResponse;
use App\PaginationFormatter;
use App\Services\CountryService;
use Illuminate\Http\JsonResponse;


class CountryController extends Controller
{
    use ApiResponse, PaginationFormatter;


    protected CountryService $countryService;
    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function getCountryUsers(CountryUsersRequest $request): JsonResponse
    {
        $users = $this->countryService->getUsersForCountry($request->code);

        return $this->success(
            $this->paginationFormatter($users),
            "Successful Users For " . $request->code
        );
    }
}
