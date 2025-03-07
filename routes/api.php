<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AssetController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/



// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/public/departments', [DepartmentController::class, 'publicIndex']); // Add this line


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::apiResource('assets', AssetController::class);
    Route::post('/assets/scan/{assetCode}', [AssetController::class, 'scan']);

    Route::get('/assets/counts/condition', [AssetController::class, 'countsByCondition']);
    Route::get('/assets/search', [AssetController::class, 'search']);
    Route::get('/assets/counts/department', [AssetController::class, 'countsByDepartment']);
    Route::get('/assets/counts/total', [AssetController::class, 'totalCount']);


    // Department routes
    Route::apiResource('departments', DepartmentController::class);
    Route::get('/departments/{department}/assets', [DepartmentController::class, 'assets']);
    Route::get('/departments/{department}/users', [DepartmentController::class, 'users']);

    // Other protected API routes will go here
});
