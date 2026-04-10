<?php

namespace App\Http\Middleware;

use App\Enums\UserOnboardingSteps;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileFinished
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->onboarding_step !== UserOnboardingSteps::COMPLETE) {
            return response()->json([
                'message' => 'Profile setup is required before accessing this resource.',
                'code'    => 'PROFILE_INCOMPLETE',
            ], 403);
        }

        return $next($request);
    }
}
