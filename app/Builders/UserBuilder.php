<?php

namespace App\Builders;

use App\Models\User;
use App\Enums\UserProvider;

class UserBuilder
{
    private array $data = [];

    public static function make(): self
    {
        return new self();
    }

    public function email(string $email): self
    {
        $this->data['email'] = $email;
        return $this;
    }

    public function password(?string $password): self
    {
        $this->data['password'] = $password;
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

    public function verified(): self
    {
        $this->data['email_verified_at'] = now();
        return $this;
    }

    public function profileIncomplete(): self
    {
        $this->data['profile_finished'] = false;
        return $this;
    }

    public function create(): User
    {
        return User::create($this->data);
    }
}
