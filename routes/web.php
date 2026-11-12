<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureAuthenticated;
use App\Http\Middleware\RedirectIfLoggedIn;
use Illuminate\Support\Facades\Route;

// Hanya untuk tamu (yang sudah login dialihkan ke Beranda)
Route::middleware(RedirectIfLoggedIn::class)->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

// Hanya untuk pengguna yang sudah login (tamu dilempar ke Login)
Route::middleware(EnsureAuthenticated::class)->group(function () {
    Route::get('/', [FeedController::class, 'index'])->name('feed');
    Route::get('/posts/create', fn () => redirect(route('feed') . '#form-posting'))->name('posts.create');
    Route::post('/posts', [FeedController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{post}', [FeedController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
