<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Department;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/signup', function () {
    return view('auth.signup', ['departments' => Department::all()]);
});

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
Route::get('/json-test', function () {
    return response()->json(['message' => 'This is a JSON response from web.php']);
});
