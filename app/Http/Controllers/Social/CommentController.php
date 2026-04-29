<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\Social\CommentRequest;
use App\Http\Resources\Social\CommentResource;
use App\Http\Responses\ApiResponse;
use App\Models\MealPost;
use App\Models\MealPostComment;
use App\Services\Social\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use ApiResponse;

    public function __construct(private CommentService $commentService) {}

    public function index(MealPost $meal): JsonResponse
    {
        $comments = $this->commentService->list($meal);

        return response()->json([
            'data'        => CommentResource::collection($comments->items()),
            'next_cursor' => $comments->nextCursor()?->encode(),
            'prev_cursor' => $comments->previousCursor()?->encode(),
            'per_page'    => $comments->perPage(),
            'message'     => 'Comments retrieved.',
        ]);
    }

    public function replies(MealPost $meal, MealPostComment $comment): JsonResponse
    {
        $replies = $this->commentService->replies($comment);

        return response()->json([
            'data'        => CommentResource::collection($replies->items()),
            'next_cursor' => $replies->nextCursor()?->encode(),
            'prev_cursor' => $replies->previousCursor()?->encode(),
            'per_page'    => $replies->perPage(),
            'message'     => 'Replies retrieved.',
        ]);
    }

    public function store(CommentRequest $request, MealPost $meal): JsonResponse
    {
        $comment = $this->commentService->add(Auth::user(), $meal, $request->body);

        return $this->success(new CommentResource($comment), 'Comment posted.', status: 201);
    }

    public function reply(CommentRequest $request, MealPost $meal, MealPostComment $comment): JsonResponse
    {
        try {
            $reply = $this->commentService->reply(Auth::user(), $meal, $comment, $request->body);

            return $this->success(new CommentResource($reply), 'Reply posted.', status: 201);
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function destroy(MealPost $meal, MealPostComment $comment): JsonResponse
    {
        try {
            $this->commentService->delete(Auth::user(), $comment);

            return $this->success(message: 'Comment deleted.');
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 403);
        }
    }
}
