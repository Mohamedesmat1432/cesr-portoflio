<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LocaleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/set-locale', [LocaleController::class, 'setLocale']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionController::class);
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', 'logout');
        Route::get('/user', 'getUser');
    });
});
