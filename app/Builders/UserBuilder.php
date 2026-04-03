<?php

namespace App\Builders;

use App\Models\User;
use App\Enums\UserProvider;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;

class UserBuilder
{
    private array $data = [];

    public static function make(): self
    {
        return new self();
    }

    public function avatar(string $avatarUrl): self
    {
        $this->data['image'] = $avatarUrl;
        return $this;
    }

    public function email(string $email): self
    {
        $this->data['email'] = $email;
        return $this;
    }

    public function password(string $password): self
    {
        $this->data['password'] = Hash::make($password);
        return $this;
    }

    public function google(string $googleId): self
    {
        $this->data['provider'] = UserProvider::GOOGLE;
        $this->data['provider_id'] = $googleId;
        return $this;
    }

    public function local(): self
    {
        $this->data['provider'] = UserProvider::LOCAL;
        return $this;
    }

    public function firstName(string $firstName): self
    {
        $this->data['first_name'] = $firstName;
        return $this;
    }

    public function lastName(string $lastName): self
    {
        $this->data['last_name'] = $lastName;
        return $this;
    }

    public function verified(): self
    {
        $this->data['email_verified_at'] = now();
        return $this;
    }


    public function create(): User
    {
        $user = User::forceCreate($this->data);
        UserProfile::create([
            'user_id' => $user->id,
        ]);
        return $user;
    }
}
