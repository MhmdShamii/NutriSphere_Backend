<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserService
{

    public function returnUser(Request $request): User
    {
        return $request->user();
    }

    public function updateUserAvatar(User $user, UploadedFile $file): User
    {

        $this->deleteUserImageFile($user);
        return $this->updateUserImage($user, $file);
    }

    public function deleteUserAvatar(User $user): User
    {
        $this->deleteUserImageFile($user);
        return $this->deleteUserImage($user);
    }

    // ====== Helper Functions ======

    private function generateAvatarName(UploadedFile $file): string
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

    private function updateUserImage(User $user, UploadedFile $file): User
    {
        $path = $file->storeAs(
            'avatars',
            $this->generateAvatarName($file),
            'public'
        );
        $user->update([
            'image' => $path,
        ]);
        return $user->fresh();
    }

    private function deleteUserImage(User $user): User
    {
        $user->update([
            'image' => "default.png",
        ]);
        return $user->fresh();
    }

    private function deleteUserImageFile(User $user): void
    {
        if ($user->image && $user->image !== 'default.png') {
            Storage::disk('public')->delete($user->image);
        }
    }
}
