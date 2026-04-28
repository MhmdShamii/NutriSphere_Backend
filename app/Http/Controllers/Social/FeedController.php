<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Http\Resources\Social\FeedPostResource;
use App\Http\Responses\ApiResponse;
use App\Services\Social\FeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    use ApiResponse;

    public function __construct(private FeedService $feedService) {}

    public function index(): JsonResponse
    {
        $posts = $this->feedService->getFeed(Auth::user());

        return response()->json([
            'data'        => FeedPostResource::collection($posts->items()),
            'next_cursor' => $posts->nextCursor()?->encode(),
            'prev_cursor' => $posts->previousCursor()?->encode(),
            'per_page'    => $posts->perPage(),
            'message'     => 'Feed retrieved.',
        ]);
    }

    public function following(): JsonResponse
    {
        $posts = $this->feedService->getFollowingFeed(Auth::user());

        return response()->json([
            'data'        => FeedPostResource::collection($posts->items()),
            'next_cursor' => $posts->nextCursor()?->encode(),
            'prev_cursor' => $posts->previousCursor()?->encode(),
            'per_page'    => $posts->perPage(),
            'message'     => 'Following feed retrieved.',
        ]);
    }
}
