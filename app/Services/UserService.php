<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
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

        if (!Storage::disk('public')->exists('avatars')) {
            Storage::disk('public')->makeDirectory('avatars');
        }

        return $this->updateUserImage($user, $file);
    }

    public function deleteUserAvatar(User $user): User
    {
        $oldPath = $user->image;

        $user = $this->deleteUserImage($user);
        $this->deleteUserImageFile($oldPath);

        return $user;
    }

    // ====== Helper Functions ======

    private function generateAvatarName(UploadedFile $file): string
    {
        $generateName =
            "Avatar_" .
            pathinfo(str_replace(' ', '_', $file->getClientOriginalName()), PATHINFO_FILENAME) .
            "_" .
            Str::uuid() .
            "." .
            $file->extension();

        return $generateName;
    }

    private function updateUserImage(User $user, UploadedFile $file): User
    {
        $oldPath = $user->image;

        $newPath = $file->storeAs(
            'avatars',
            $this->generateAvatarName($file),
            'public'
        );

        try {
            DB::transaction(function () use ($user, $newPath) {
                $user->update(['image' => $newPath]);
            });

            $this->deleteUserImageFile($oldPath);

            return $user->fresh();
        } catch (\Throwable $e) {
            $this->deleteUserImageFile($newPath);

            throw new \RuntimeException('Failed to update user avatar. Please try again later.');
        }
    }

    private function deleteUserImage(User $user): User
    {
        $user->update([
            'image' => "default.png",
        ]);
        return $user->fresh();
    }

    private function deleteUserImageFile(?string $imagePath): void
    {
        if ($imagePath && $imagePath !== 'default.png') {
            Storage::disk('public')->delete($imagePath);
        }
    }
}
