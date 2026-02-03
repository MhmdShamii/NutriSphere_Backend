<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserService
{

    public function returnUser(User $user): array
    {
        return ['user' => new UserResource($user)];
    }

    public function updateUserAvatar(User $user, ?UploadedFile $file)
    {
        if ($user->image && !$file) {
            Storage::disk('public')->delete($user->image);
            $user->update([
                'image' => "default.png",
            ]);
            return $this->returnUser($user->fresh());
        }

        if ($user->image && $user->image !== "default.png") {
            Storage::disk('public')->delete($user->image);
        }

        $path = $file->storeAs(
            'avatars',
            $this->generateAvatarName($file),
            'public'
        );

        $user->update([
            'image' => $path,
        ]);

        return $this->returnUser($user->fresh());
    }

    // ====== Helper Functions ======

    private function generateAvatarName(UploadedFile $file)
    {
        $generateName =
            "Avatar_" .
            pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) .
            "_" .
            Str::uuid() .
            "." .
            $file->extension();

        return $generateName;
    }
}
