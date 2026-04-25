<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserPublicProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'first_name'      => $this->first_name,
            'last_name'       => $this->last_name,
            'country'         => [
                'code' => $this->country?->code,
                'name' => $this->country?->name,
            ],
            'image'           => [
                'avatar'      => $this->resolveAvatarUrl(),
                'cover_image' => $this->resolveCoverImageUrl(),
            ],
            'followers_count' => $this->followers_count ?? 0,
            'following_count' => $this->following_count ?? 0,
            'is_following'    => (bool) $this->is_following,
            'follows_you'     => (bool) $this->follows_you,
        ];
    }

    private function resolveAvatarUrl(): string
    {
        if ($this->image === null || $this->image === 'default.png') {
            return asset('storage/avatars/default.png');
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return Storage::disk('public')->url($this->image);
    }

    private function resolveCoverImageUrl(): string
    {
        if ($this->cover_image === null || $this->cover_image === 'default_cover.png') {
            return asset('storage/covers/default_cover.png');
        }

        return Storage::disk('public')->url($this->cover_image);
    }
}
