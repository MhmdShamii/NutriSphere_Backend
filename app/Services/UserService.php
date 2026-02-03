<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UserService
{

    public function returnUser(Request $request): array
    {
        return ['user' => new UserResource($request->user())];
    }

    public function updateUserAvatar(User $user, ?UploadedFile $file)
    {
        $newImageName = "Avatar_" . $file->getClientOriginalName() . "_" .  Str::uuid();

        $path = $file->storeAs(
            'avatars',
            $newImageName,
            'public'
        );

        $user->update([
            'image' => $path,
        ]);

        return new UserResource($user->fresh());
    }
}
