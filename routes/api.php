<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LendingController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//bárki által elérhető
Route::post('/register',[RegisteredUserController::class, 'store']);
Route::post('/login',[AuthenticatedSessionController::class, 'store']);



//összes kérés
Route::apiResource('/users', UserController::class);
Route::patch('update-password/{id}', [UserController::class, "updatePassword"]);

//autentikált útvonal
Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::get('lendings-copies', [LendingController::class, "lendingsWithCopies"]);
        Route::get('user-lendings', [UserController::class, "userLendings"]);
        // Kijelentkezés útvonal
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    });

//admin
Route::middleware(['auth:sanctum',Admin::class])
->group(function () {
    Route::get('/admin/users', [UserController::class, 'index']);
    Route::get('specific-date', [LendingController::class, "dateSpecific"]);
    Route::get('specific-date/{copy_id}', [LendingController::class, "copySpecific"]);
});




Route::get('books-copies', [BookController::class, "booksWithCopies"]);




