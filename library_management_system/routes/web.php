<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GenreController;
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

        Route::get('/category-management', [CategoryController::class, 'index'])->name('category-management');
        Route::post('/category-management', [CategoryController::class, 'store'])->name('category-management.store');
        Route::put('/category-management/{category}', [CategoryController::class, 'update'])->name('category-management.update');
        Route::delete('/category-management/{category}', [CategoryController::class, 'destroy'])->name('category-management.destroy');

        Route::get('/category-management/genres', [GenreController::class, 'index'])->name('category-management.genres');
        Route::post('/category-management/genres', [GenreController::class, 'store'])->name('category-management.genres.store');
        Route::put('/category-management/genres/{genre}', [GenreController::class, 'update'])->name('category-management.genres.update');
        Route::delete('/category-management/genres/{genre}', [GenreController::class, 'destroy'])->name('category-management.genres.destroy');
    });
// Route::get('/librarian/dashboard', [DashboardController::class, 'index'])->name('librarian.dashboard');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
