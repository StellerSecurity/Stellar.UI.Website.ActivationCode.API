<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {

    Route::prefix('activationcontroller')->group(function () {
        Route::controller(\App\Http\Controllers\V1\ActivationController::class)->group(function () {
            Route::post('/verify', 'verify');
        });
    });

    Route::prefix('usercontroller')->group(function () {
        Route::controller(\App\Http\Controllers\V1\UserController::class)->group(function () {
            Route::post('/auth', 'auth');
            Route::post('/create', 'create');

        });
    });



});