<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileFinished
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->profile_finished) {
            return response()->json([
                'message' => 'Profile setup is required before accessing this resource.',
                'code'    => 'PROFILE_INCOMPLETE',
            ], 403);
        }

        return $next($request);
    }
}
