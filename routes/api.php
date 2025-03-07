<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Department routes
    Route::apiResource('departments', DepartmentController::class);
    Route::get('/departments/{department}/assets', [DepartmentController::class, 'assets']);
    Route::get('/departments/{department}/users', [DepartmentController::class, 'users']);

    // Other protected API routes will go here
});
