<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notification\NotificationResource;
use App\Http\Responses\ApiResponse;
use App\Services\Notification\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use ApiResponse;

    public function __construct(private NotificationService $notificationService) {}

    public function check(): JsonResponse
    {
        return $this->success(
            ['has_new' => $this->notificationService->hasNew(Auth::user())],
            'Notification status retrieved.'
        );
    }

    public function index(): JsonResponse
    {
        $notifications = $this->notificationService->getAndMarkRead(Auth::user());

        return $this->success(
            NotificationResource::collection($notifications),
            'Notifications retrieved.'
        );
    }
}
