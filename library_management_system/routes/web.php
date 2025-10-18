<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryGenreController;
use App\Http\Controllers\LibrarianController;
use App\Models\Librarian;

Route::get('/', function () {
    return view('pages.welcome');
});


Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup.form');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
});


Route::prefix('librarian')
    ->middleware(['auth', 'role:librarian'])
    ->name('librarian.')
    ->group(function () {
        Route::get('/categories-genres', [CategoryGenreController::class, 'index'])->name('categories-genres');
    });
// Route::get('/librarian/dashboard', [DashboardController::class, 'index'])->name('librarian.dashboard');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
