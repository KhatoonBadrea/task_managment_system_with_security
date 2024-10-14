<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\AtachmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

// Routes for admin: full CRUD access to users
Route::group(['middleware' => ['auth:api', 'admin']], function () {
    Route::apiResource('roles', RoleController::class);
});
Route::apiResource('tasks', TaskController::class);
Route::put('tasks/{task}/assigne', [TaskController::class, 'update_assigned_to']);

Route::apiResource('comments', CommentController::class);

Route::post('/tasks/{taskId}/attachments', [AtachmentController::class, 'store']);
Route::delete('/attachments/{id}', [AtachmentController::class, 'destroy']);

Route::put('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
Route::put('/tasks/{task}/type', [TaskController::class, 'updateType']);

