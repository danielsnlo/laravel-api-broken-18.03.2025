<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Models\Comment;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('posts', PostController::class);
Route::apiResource('posts.comments', CommentController::class);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    // Admin routes
    Route::post('create-post', [PostController::class, 'create']);
    Route::delete('delete-post/{id}', [PostController::class, 'delete']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Assign role to a user (Admin-only)
    Route::post('assign-role/{userId}', [AuthController::class, 'assignRole']);
});
