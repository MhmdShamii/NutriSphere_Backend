<?php

namespace App;

use App\Http\Resources\UserResource;
use Illuminate\Pagination\LengthAwarePaginator;

trait PaginationFormatter
{
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
