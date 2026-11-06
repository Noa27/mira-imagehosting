<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/lang/{locale}', function (string $locale) {
    abort_unless(in_array($locale, SetLocale::LOCALES, true), 404);
    session(['locale' => $locale]);

    return back();
})->name('lang');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/setup', [AuthController::class, 'setup'])->name('setup')->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [ImageController::class, 'index'])->name('home');
    Route::post('/images', [ImageController::class, 'store'])->name('images.store');
    Route::delete('/images/{image}', [ImageController::class, 'destroy'])->name('images.destroy');
    Route::post('/token', [ImageController::class, 'token'])->name('token');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/users', [AdminController::class, 'store'])->name('store');
        Route::put('/users/{user}/password', [AdminController::class, 'password'])->name('password');
        Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('destroy');
    });
});
