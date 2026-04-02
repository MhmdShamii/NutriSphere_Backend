<?php

namespace App\Models;

use App\Enums\UserOnboardingSteps;
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
        'cover_image',
        'first_name',
        'last_name',
        'provider',
        'provider_id',
        'email',
        'email_verified_at',
        'role',
        'onboarding_step',
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
        'provider'         => UserProvider::class,
        'role'             => UserRole::class,
        'onboarding_step'  => UserOnboardingSteps::class,
        'email_verified_at' => 'datetime',
    ];

    // Relations
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    // Query scopes

    public function scopeFindByEmail($query, string $email)
    {
        return $query->where('email', $email);
    }
}
