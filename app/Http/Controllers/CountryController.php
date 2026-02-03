<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryUsersRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\CountryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;


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

        return $this->success(
            $this->paginationFormatter($users),
            "Successful Users For " . $request->code
        );
    }

    // ===== Helper functions =====

    private function paginationFormatter(LengthAwarePaginator $data)
    {
        return [
            'users'        => UserResource::collection($data),
            'currentPage'  => $data->currentPage(),
            'lastPage'     => $data->lastPage(),
            'perPage'      => $data->perPage(),
            'total'        => $data->total(),
            'from'         => $data->firstItem(),
            'to'           => $data->lastItem(),
            'firstPageUrl' => $data->url(1),
            'lastPageUrl'  => $data->url($data->lastPage()),
            'nextPageUrl'  => $data->nextPageUrl(),
            'prevPageUrl'  => $data->previousPageUrl(),
        ];
    }
}
