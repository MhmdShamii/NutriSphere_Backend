<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserService
{

    public function returnUser(Request $request): array
    {
        return ['user' => new UserResource($request->user())];
    }
}
