<?php

namespace App\Models;

use App\Enums\UserProvider;
use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'image',
        'first_name',
        'last_name',
        'provider',
        'provider_id',
        'email',
        'role',
        'profile_finished',
        'country_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'provider_id',
        'remember_token',
    ];

    protected $casts = [
        'provider' => UserProvider::class,
        'role' => UserRole::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relations
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // Query scopes

    public function scopeFindByEmail($query, string $email)
    {
        return $query->where('email', $email);
    }
}
