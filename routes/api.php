<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// --- Auth Routes ---
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// --- Routes with auth ---
Route::middleware('auth:sanctum')->group(function () {
    // Post data for instaapp
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/name', [PostController::class, 'getByImageName']);

    // like and comments untuk postingan data
    Route::post('/posts/{post}/like', [PostController::class, 'likePost']);
    Route::post('/posts/{post}/comment', [PostController::class, 'commentPost']);
    Route::get('/posts/{post}/comments', [PostController::class, 'getComments']);
    Route::get('/posts/{post}/likes', [PostController::class, 'getLikes']);
});