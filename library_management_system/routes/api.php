<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register');

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
