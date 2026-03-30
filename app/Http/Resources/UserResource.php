<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'country' => [
                'code'       => $this->country?->code,
                'name'       => $this->country?->name,
            ],
            'image' => [
                'avatar' => $this->image === 'default.png'
                    ? asset('storage/avatars/default.png')
                    : (str_starts_with($this->image, 'http')
                        ? $this->image
                        : Storage::disk('public')->url($this->image)),
            ],
            'verified'   => $this->email_verified_at !== null,
            'role' => $this->role,
            'profile_finished' => $this->profile_finished
        ];
    }
}
