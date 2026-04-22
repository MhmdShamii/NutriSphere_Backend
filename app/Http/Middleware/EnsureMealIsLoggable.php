<?php

namespace App\Http\Middleware;

use App\Enums\MealVisibility;
use App\Models\MealPost;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMealIsLoggable
{
    public function handle(Request $request, Closure $next): Response
    {
        $meal = $request->route('meal');

        if (!$meal instanceof MealPost) {
            return $next($request);
        }

        $userId = $request->user()->id;

        if ($meal->userProfile->user_id !== $userId && $meal->visibility === MealVisibility::PRIVATE) {
            abort(403, 'You do not have permission to log this meal.');
        }

        return $next($request);
    }
}
