<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\AuthService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthController extends Controller
{
    use ApiResponse; // methods: success(data, message, dataKey, code), error (message, code, errors)
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $registerRequest): JsonResponse
    {
        $data = $registerRequest->validated();
        $result = $this->authService->register($data);

        return $this->success(
            ['user' => $result['user']],
            'User registered successfully',
            status: 201
        );
    }

    public function verifyEmail(Request $request)
    {
        $result = $this->authService->verifyEmail($request->route('id'), $request->route('hash'));

        if (isset($result['status']) && $result['status'] == 404) {
            return redirect()->to(
                config('app.frontend_url') . '/not-found?message=' . urlencode($result['message'])
            );
        }

        return redirect()->to(
            config('app.frontend_url') . '/auth/verify-success?token=' . $result['token']
        );
    }

    public function resendVerification(Request $request)
    {
        $result = $this->authService->resendVerificationEmail($request["email"]);

        return $result['status'] == 200 ?
            $this->success(null, "Verification email sent", status: $result['status']) :
            $this->success(null, "Email already verified", status: $result['status']);
    }

    public function login(LoginRequest $loginRequest): JsonResponse
    {
        try {
            $result = $this->authService->login($loginRequest->validated());

            return $this->success(
                $this->authResponseData($result['user'], $result['token']),
                'User logged in successfully',
                status: 200
            );
        } catch (UnauthorizedHttpException $e) {
            return $this->error($e->getMessage(), 401);
        }
    }

    public function googleLogin(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->googleLogin($request['id_token']);

            return $this->success(
                $this->authResponseData($result['user'], $result['token']),
                'User logged in successfully',
                status: 200
            );
        } catch (UnauthorizedHttpException $e) {
            return $this->error($e->getMessage(), 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(message: 'Logged out successfully');
    }

    public function logoutFromAllDevices(Request $request): JsonResponse
    {
        $this->authService->logoutFromAllDevices($request->user());

        return $this->success(message: 'Logged out from all devices successfully');
    }

    //======== Helper Functions =========//

    protected function authResponseData(User $user, string $token): array
    {
        return [
            'user'  => new UserResource($user),
            'token' => $token,
        ];
    }
}
