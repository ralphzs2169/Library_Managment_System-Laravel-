<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;


Route::get('api/books', [BookController::class, 'index']);
Route::post('api/librarian/books/create', [BookController::class, 'store']);
