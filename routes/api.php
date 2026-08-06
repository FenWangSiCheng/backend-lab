<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);
Route::post('/login', [AuthController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('login');
Route::post('/users', [UserController::class, 'store'])->name('users.store');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/me', [AuthController::class, 'show'])->name('me');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::apiResource('users', UserController::class)->except('store');
});
