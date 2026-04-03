<?php

namespace App\Http\Middleware;

use App\Enums\UserOnboardingSteps;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingStep
{
    public function handle(Request $request, Closure $next, string $step): Response
    {
        $required = UserOnboardingSteps::from($step);

        if ($request->user()?->onboarding_step->order() < $required->order()) {
            return response()->json([
                'message' => 'This action is not allowed at your current onboarding stage.',
                'code'    => 'INVALID_ONBOARDING_STEP',
            ], 403);
        }

        return $next($request);
    }
}
