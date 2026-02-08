<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    //Authentication Routes
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('login', 'login');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', 'logout');
        });
    });

    //Tasks Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('tasks', TaskController::class)
            ->only(['index', 'store', 'show', 'update'])
            ->parameters(['tasks' => 'id']);

        Route::prefix('tasks/{id}')->controller(TaskController::class)->group(function () {
            Route::patch('status', 'updateStatus');
            Route::post('dependencies', 'addDependency');
        });
    });
});
