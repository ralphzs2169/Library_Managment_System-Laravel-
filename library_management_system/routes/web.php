<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SemesterController;
use Illuminate\Http\Request;
use App\Models\Genre;

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
        Route::get('/dashboard', [DashboardController::class, 'librarianDashboard'])->name('dashboard');

        Route::get('/books/index', [BookController::class, 'index'])->name('books.index');
        Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
        Route::post('/books/store', [BookController::class, 'store'])->name('books.store');
        Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
        Route::put('/books/update/{book}', [BookController::class, 'update'])->name('books.update');

        Route::get('/category-management', [CategoryController::class, 'index'])->name('category-management');
        Route::post('/category-management', [CategoryController::class, 'store'])->name('category-management.store');
        Route::put('/category-management/{category}', [CategoryController::class, 'update'])->name('category-management.update');
        Route::delete('/category-management/{category}', [CategoryController::class, 'destroy'])->name('category-management.destroy');

        Route::get('/category-management/genres', [GenreController::class, 'index'])->name('category-management.genres');
        Route::post('/category-management/genres', [GenreController::class, 'store'])->name('category-management.genres.store');
        Route::put('/category-management/genres/{genre}', [GenreController::class, 'update'])->name('category-management.genres.update');
        Route::delete('/category-management/genres/{genre}', [GenreController::class, 'destroy'])->name('category-management.genres.destroy');

        Route::get('/semester-management', [SemesterController::class, 'index'])->name('semester-management');
        Route::post('/semester-management', [SemesterController::class, 'store'])->name('semester-management.store');
        Route::get('/semester-management/create', [SemesterController::class, 'create'])->name('semester-management.create');
        Route::get('/semester-management/{id}/edit', [SemesterController::class, 'edit'])->name('semester-management.edit');
        Route::put('/semester-management/{id}', [SemesterController::class, 'update'])->name('semester-management.update');
        Route::post('/semester-management/{id}/activate', [SemesterController::class, 'activate'])->name('semester-management.activate');
        Route::post('/semester-management/{id}/deactivate', [SemesterController::class, 'deactivate'])->name('semester-management.deactivate');

        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');

        Route::get('/genres/by-category', [CategoryController::class, 'genresByCategory'])->name('genres.by-category');
    });

Route::prefix('staff')
    ->middleware(['auth', 'role:staff'])
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'staffDashboard'])->name('dashboard');
        Route::get('/books/available', [BookController::class, 'showAvailableBooks'])->name('books.available');
        Route::get('borrower/{user}', [UserController::class, 'borrowerDetails'])->name('borrower.details');
        Route::get('/check-active-semester', function () {
            $hasActive = \App\Models\Semester::where('status', 'active')->exists();
            return response()->json(['has_active_semester' => $hasActive]);
        });

        Route::post('/borrow-transaction/borrow', [UserController::class, 'borrowBook'])->name('borrow-transaction.borrow');
        Route::post('/borrow-transaction/return', [UserController::class, 'returnBook'])->name('borrow-transaction.return');
    });

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
