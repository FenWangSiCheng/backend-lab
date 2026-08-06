<?php

use App\Http\Controllers\Api\GreetingController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);
Route::post('/greet', GreetingController::class);
Route::apiResource('users', UserController::class);
