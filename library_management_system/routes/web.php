<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\ClearanceController;
use App\Http\Controllers\PenaltyController;
use App\Http\Controllers\RenewalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\LibrarianSectionsController;


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
        Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');

        Route::get('/category-management', [CategoryController::class, 'index'])->name('category-management');
        Route::post('/category-management', [CategoryController::class, 'store'])->name('category-management.store');
        Route::put('/category-management/{category}', [CategoryController::class, 'update'])->name('category-management.update');
        Route::delete('/category-management/{category}', [CategoryController::class, 'destroy'])->name('category-management.destroy');

        Route::get('/category-management/genres', [GenreController::class, 'index'])->name('category-management.genres');
        Route::post('/category-management/genres', [GenreController::class, 'store'])->name('category-management.genres.store');
        Route::put('/category-management/genres/{genre}', [GenreController::class, 'update'])->name('category-management.genres.update');
        Route::delete('/category-management/genres/{genre}', [GenreController::class, 'destroy'])->name('category-management.genres.destroy');

        Route::get('/section/borrowing-records', [LibrarianSectionsController::class, 'borrowingRecords'])->name('section.borrowing-records');
        Route::get('/section/reservation-records', [LibrarianSectionsController::class, 'reservationRecords'])->name('section.reservation-records');
        Route::get('/section/penalty-records', [LibrarianSectionsController::class, 'penaltyRecords'])->name('section.penalty-records');
        Route::get('/section/borrowers', [LibrarianSectionsController::class, 'borrowers'])->name('section.borrowers');
        Route::get('/section/personnel-accounts', [LibrarianSectionsController::class, 'personnelAccounts'])->name('section.personnel-accounts');
        Route::get('/section/clearance-management', [ClearanceController::class, 'index'])->name('section.clearance-management');
        
        // Fixed: Removed double naming and incorrect prefixing
        Route::get('/section/semester-management', [SemesterController::class, 'index'])->name('section.semester-management');
        Route::get('/section/activity-logs', [ActivityLogController::class, 'index'])->name('section.activity-logs');

        
        Route::post('/semester-management', [SemesterController::class, 'store'])->name('semester-management.store');
        Route::get('/semester-management/create', [SemesterController::class, 'create'])->name('semester-management.create');
        Route::get('/semester-management/{id}/edit', [SemesterController::class, 'edit'])->name('semester-management.edit');
        Route::put('/semester-management/{id}', [SemesterController::class, 'update'])->name('semester-management.update');
        Route::post('/semester-management/{id}/activate', [SemesterController::class, 'activate'])->name('semester-management.activate');
        Route::post('/semester-management/{id}/deactivate', [SemesterController::class, 'deactivate'])->name('semester-management.deactivate');
        
        
        Route::get('/genres/by-category', [CategoryController::class, 'genresByCategory'])->name('genres.by-category');
        
        // Fixed: Removed 'librarian.' prefix from name to avoid double prefixing
        Route::get('/settings', [SettingsController::class, 'index'])->name('section.settings');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    });

Route::prefix('staff')
    ->middleware(['auth', 'role:staff'])
    ->name('staff.')
    ->group(function () {

        Route::get('/dashboard/members', [StaffDashboardController::class, 'membersList']);
        Route::get('/dashboard/active-borrows', [StaffDashboardController::class, 'activeBorrowsList']);
        Route::get('/dashboard/unpaid-penalties', [StaffDashboardController::class, 'unpaidPenaltiesList']);
        Route::get('/dashboard/queue-reservations', [StaffDashboardController::class, 'queueReservationsList']);

    });


Route::prefix('transaction')
    ->middleware(['auth', 'role:librarian,staff'])
    ->group(function () {

        Route::post('/borrow/validate', [BorrowController::class, 'validateBorrow']);
        Route::post('/borrow/perform', [BorrowController::class, 'performBorrow']);

        Route::post('/return/validate', [ReturnController::class, 'validateReturn']);
        Route::post('/return/perform', [ReturnController::class, 'performReturn']);

        Route::post('/renewal/validate', [RenewalController::class, 'validateRenewal']);
        Route::post('/renewal/perform', [RenewalController::class, 'performRenewal']);

        Route::post('/reservation/validate', [ReservationController::class, 'validateReservation']);
        Route::post('/reservation/perform', [ReservationController::class, 'performReservation']);
        Route::get('/reservation/{user}/book/{book}/available-copies', [ReservationController::class, 'availableCopiesForReservation']);
        Route::post('/reservation/{reservation}/cancel', [ReservationController::class, 'cancelReservation']);

        Route::put('/penalty/{penalty}', [PenaltyController::class, 'processPenalty']);
        Route::post('/{borrower}/penalty/{penalty}/cancel', [PenaltyController::class, 'cancelPenalty']);

        // Additional routes for transaction-related functionalities
        Route::get('/books/selection/{transaction_type}/{member_id}', [BookController::class, 'getBooksForBorrowOrReserve'])->name('books.selection');
        Route::get('/borrower/{user}', [UserController::class, 'borrowerDetails']);
        Route::get('/check-active-semester', [SemesterController::class, 'checkActiveSemester']);
    });

Route::prefix('transaction/clearance')
    ->middleware(['auth', 'role:librarian,staff,student,teacher'])
    ->group(function () {

        Route::post('/{targetUserId}/validate-request/{requestorId}', [ClearanceController::class, 'validateClearanceRequest']);
        Route::post('/{targetUserId}/perform-request/{requestorId}', [ClearanceController::class, 'performClearanceRequest']);
        
        // Updated routes for approval/rejection
        Route::post('/{clearanceId}/approve', [ClearanceController::class, 'approveClearance']);
        Route::post('/{clearanceId}/reject', [ClearanceController::class, 'rejectClearance']);

    });


Route::middleware('auth')->get('/settings', [SettingsController::class, 'allSettings']);


Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
