<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnership
{
    public function handle(Request $request, Closure $next, string $param, string $column = 'user_id'): Response
    {
        $model = $request->route($param);

        if (! $model instanceof Model) {
            abort(404);
        }

        $ownerId = $column === 'user_profile_id'
            ? $request->user()->profile?->id
            : $request->user()->id;

        if ($model->{$column} !== $ownerId) {
            return response()->json([
                'message' => 'You do not have permission to access this resource.',
                'code'    => 'FORBIDDEN',
            ], 403);
        }

        return $next($request);
    }
}
