<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DailyLogingController;
use App\Http\Controllers\HealthConditionController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
        Route::post('/check-email', [UserController::class, 'checkEmail']);
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
            Route::get('/', [UserController::class, 'show']);
            Route::patch('/', [UserController::class, 'update']);
            Route::post('/complete-main-info', [UserController::class, 'storeMainInfo'])->middleware('ensure.step:main_info');
            Route::post('/complete-basic-info', [UserProfileController::class, 'storeBasicInfo'])->middleware('ensure.step:basic_info');
            Route::post('/complete-targets', [UserProfileController::class, 'storeTargets'])->middleware('ensure.step:targets');
            Route::get('/health-conditions', [HealthConditionController::class, 'userConditions'])->middleware('ensure.step:health_conditions');
            Route::post('/health-conditions', [HealthConditionController::class, 'store'])->middleware('ensure.step:health_conditions');
            Route::delete('/health-conditions/{id}', [HealthConditionController::class, 'destroy'])->middleware('ensure.step:health_conditions');
            Route::post('/complete-health-conditions', [HealthConditionController::class, 'complete'])->middleware('ensure.step:health_conditions');
            Route::post('/avatar', [UserController::class, 'storeAvatar']);
            Route::delete('/avatar', [UserController::class, 'destroyAvatar']);
            Route::post('/cover-image', [UserController::class, 'storeCoverImage']);
            Route::delete('/cover-image', [UserController::class, 'destroyCoverImage']);
            Route::post('/log', [DailyLogingController::class, 'logCustomMeal']);
            Route::post('/log/estimate', [DailyLogingController::class, 'logEstimatedMeal']);
            Route::post('/log/{log}/confirm', [DailyLogingController::class, 'confirmLog'])->middleware('ensure.owns:log');
            Route::post('/log/{meal}', [DailyLogingController::class, 'logMeal'])->middleware('meal.loggable');
            Route::delete('/log/{log}', [DailyLogingController::class, 'removeDailyLog'])->middleware('ensure.owns:log');
        });

        Route::prefix('meals')->group(function () {
            Route::post('/', [MealController::class, 'store']);
            Route::post('/{meal}/confirm', [MealController::class, 'confirm'])->middleware('ensure.owns:meal,user_profile_id');
            Route::post('/{meal}/discard', [MealController::class, 'discard'])->middleware('ensure.owns:meal,user_profile_id');
        });


        Route::prefix('ingredients')->group(function () {
            Route::post('/search', [IngredientController::class, 'search']);
        });
    });
});
