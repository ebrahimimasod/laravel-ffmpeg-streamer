<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MediaController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [MediaController::class, 'index'])->name('dashboard');

    // File Upload
    Route::post('/upload', [MediaController::class, 'upload'])->name('media.upload');

    // File Download
    Route::get('/download/{uuid}', [MediaController::class, 'download'])->name('media.download');

    // File Delete
    Route::delete('/delete/{uuid}', [MediaController::class, 'destroy'])->name('media.destroy');
});

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});



Route::get('/stream/{uuid}/', [MediaController::class, 'stream'])->name('stream');
