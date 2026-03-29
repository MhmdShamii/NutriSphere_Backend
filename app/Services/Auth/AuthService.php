<?php

namespace App\Services\Auth;

use App\Builders\UserBuilder;
use App\Jobs\SendVerificationEmailJob;
use App\Models\User;
use App\Services\CountryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthService
{
    protected CountryService $countryService;
    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }


    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {

            $data['password'] = Hash::make($data['password']);

            $user = UserBuilder::make()
                ->email($data['email'])
                ->password($data["password"])
                ->create();

            SendVerificationEmailJob::dispatch($user);

            return [
                'user' => $user,
                'token' => null,
            ];
        });
    }

    public function verifyEmail(int $id, string $hash)
    {

        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return [
                'message' => 'Email already verified',
                "status" => 404,
            ];
        }

        if (! hash_equals((string) $hash, sha1($user->email))) {
            throw new \Exception('Invalid verification link');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->email_verified_at = now();
            $user->save();
        }

        $token = $this->issueToken($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function resendVerificationEmail(string $email): array
    {
        $user = User::findByEmail($email)->first();

        if ($user->hasVerifiedEmail()) {
            return [
                "status" => 404,
                "message" => "Email already verified",
            ];
        }

        $user->sendEmailVerificationNotification();

        return [
            "status" => 200,
            "message" => "Verification email sent",
        ];
    }

    public function login(array $data): array
    {
        return DB::transaction(function () use ($data) {

            $user = $this->authenticateUser($data);
            $token = $this->issueToken($user, 10);

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    public function logoutFromAllDevices(User $user): void
    {
        $user->tokens()->delete();
    }

    //======== Helper Functions =========//

    private function authenticateUser(array $data): User
    {
        $user = User::findByEmail($data['email'])->first();
        if (!$this->isValidUser($user, $data['password'])) {
            throw new UnauthorizedHttpException('', 'Invalid credentials');
        }

        return $user;
    }

    private function isValidUser(?User $user, string $password): bool
    {
        return $user && Hash::check($password, $user->password);
    }

    private function issueToken(User $user, int $expiresInDays = 30, string $name = 'web'): string
    {
        $newToken = $user->createToken($name);

        $newToken->accessToken->expires_at = now()->addDays($expiresInDays);
        $newToken->accessToken->save();

        return $newToken->plainTextToken;
    }
}
