<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
        Route::post('/check-email', [UserController::class, 'checkEmailExistence']);
        Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['signed'])->name('verification.verify');
        Route::post('/email/resend', [AuthController::class, 'resendVerification']);
        Route::post('/google', [AuthController::class, 'googleLogin']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/logout-all', [AuthController::class, 'logoutFromAllDevices']);
        });

        Route::prefix('me')->group(function () {
            Route::get('/', [UserController::class, 'me']);
            Route::post('/avatar', [UserController::class, 'updateAvatar']);
            Route::delete('/avatar', [UserController::class, 'deleteAvatar']);
            Route::post('/cover-image', [UserController::class, 'updateCoverImage']);
            Route::delete('/cover-image', [UserController::class, 'deleteCoverImage']);
        });
    });
});
