<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
// use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Web\PostWebController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    // return view('auth.login');
    return redirect()->route('login');
});

// Halaman register
Route::get('register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('register', [RegisterController::class, 'register'])->name('register.post');

// Login
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.post');

// Logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');

Route::middleware('auth')->group(function() {
    // Route::get('/posts', [PostController::class, 'index'])->name('posts.index'); // Halaman posts
    Route::get('/posts', [PostWebController::class, 'index'])->name('posts.index'); // Halaman posts
    Route::get('/posts/create', [PostWebController::class, 'create'])->name('posts.create'); // Form buat posting baru
    Route::post('/posts', [PostWebController::class, 'store'])->name('posts.store'); // Simpan posting

    // like posting
    Route::post('/posts/{post}/like', [PostWebController::class, 'likePost'])->name('posts.like');

    // comment posting
    Route::post('/comments', [PostWebController::class, 'storeComment'])->name('comments.store');

}); 

