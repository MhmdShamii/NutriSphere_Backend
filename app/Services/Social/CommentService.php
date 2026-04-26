<?php

namespace App\Services\Social;

use App\Models\MealPost;
use App\Models\MealPostComment;
use App\Models\User;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Collection;

class CommentService
{
    public function list(MealPost $meal, int $perPage = 20): CursorPaginator
    {
        $comments = $meal->comments()
            ->withCount('replies')
            ->with('user')
            ->cursorPaginate($perPage);

        $this->attachReplyPreview(collect($comments->items()));

        return $comments;
    }

    public function replies(MealPostComment $comment, int $perPage = 20): CursorPaginator
    {
        return $comment->replies()->with('user')->cursorPaginate($perPage);
    }

    public function add(User $user, MealPost $meal, string $body): MealPostComment
    {
        $comment = $meal->comments()->create([
            'user_id' => $user->id,
            'body'    => $body,
        ]);

        $meal->increment('comments_count');

        return $comment->load('user');
    }

    public function reply(User $user, MealPost $meal, MealPostComment $parent, string $body): MealPostComment
    {
        if ($parent->isReply()) {
            throw new \InvalidArgumentException('Cannot reply to a reply.');
        }

        if ($parent->meal_post_id !== $meal->id) {
            throw new \InvalidArgumentException('Comment does not belong to this meal.');
        }

        $reply = MealPostComment::create([
            'meal_post_id' => $meal->id,
            'user_id'      => $user->id,
            'parent_id'    => $parent->id,
            'body'         => $body,
        ]);

        $meal->increment('comments_count');

        return $reply->load('user');
    }

    public function delete(User $user, MealPostComment $comment): void
    {
        if ($comment->user_id !== $user->id) {
            throw new \RuntimeException('You can only delete your own comments.');
        }

        $meal = $comment->mealPost;
        $deletedCount = 1 + $comment->replies()->count();

        $comment->delete();

        $meal->decrement('comments_count', $deletedCount);
    }

    private function attachReplyPreview(Collection $comments): void
    {
        $ids = $comments->pluck('id');

        $previews = MealPostComment::with('user')
            ->whereIn('parent_id', $ids)
            ->oldest()
            ->get()
            ->groupBy('parent_id')
            ->map(fn($group) => $group->first());

        foreach ($comments as $comment) {
            $preview = $previews->get($comment->id);
            $comment->setRelation('replyPreview', $preview ? collect([$preview]) : collect());
        }
    }
}
