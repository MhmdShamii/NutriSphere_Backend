<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HealthConditionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
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
        Route::get('/health-conditions', [HealthConditionController::class, 'index']);

        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/logout-all', [AuthController::class, 'logoutFromAllDevices']);
        });

        Route::prefix('users/me')->group(function () {
            Route::get('/', [UserController::class, 'me']);
            Route::patch('/', [UserController::class, 'updateUser']);
            Route::post('/complete-main-info', [UserController::class, 'completeMainInfo'])->middleware('ensure.step:main_info');
            Route::post('/complete-basic-info', [UserProfileController::class, 'completeBasicInfo'])->middleware('ensure.step:basic_info');
            Route::post('/complete-targets', [UserProfileController::class, 'completeTargets'])->middleware('ensure.step:targets');
            Route::get('/health-conditions', [HealthConditionController::class, 'getUserConditions'])->middleware('ensure.step:health_conditions');
            Route::post('/health-conditions', [HealthConditionController::class, 'add'])->middleware('ensure.step:health_conditions');
            Route::delete('/health-conditions/{id}', [HealthConditionController::class, 'remove'])->middleware('ensure.step:health_conditions');
            Route::post('/complete-health-conditions', [HealthConditionController::class, 'completeHealthConditions'])->middleware('ensure.step:health_conditions');
            Route::post('/avatar', [UserController::class, 'updateAvatar']);
            Route::delete('/avatar', [UserController::class, 'deleteAvatar']);
            Route::post('/cover-image', [UserController::class, 'updateCoverImage']);
            Route::delete('/cover-image', [UserController::class, 'deleteCoverImage']);
        });
    });
});
