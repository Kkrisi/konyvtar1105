<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LendingController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Admin;
use App\Http\Middleware\Librarian;
use App\Http\Middleware\Warehouseman;
use Illuminate\Http\Request;
use Illuminate\Routing\Route as RoutingRoute;
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
        Route::get('/auth/users', UserController::class, 'show');
        Route::patch('/auth/users', UserController::class, 'update');
        //hany kolcsonzese volt idaig
        Route::get('/lendings-count', [LendingController::class, "lendingCount"]);
        //hány aktív kölcsönzés van
        Route::get('active-lending-count',[LendingController::class,"activeLendingCount"]);
        //kikölcsönzött adatai
        Route::get('lendings-books-data', [LendingController::class, 'lendingsBooksData']);


        // 3.C
        // könyvenként csoportosítsd, csak azokat, amik max 1 példányban van nálam!
        Route::get('lendings-books-max1', [LendingController::class, 'lendingsBooksMax1']);

        //Add meg a keménykötésű példányokat szerzővel és címmel!
        Route::get('lendings-hardcovered-max1', [LendingController::class, 'lendingsHardcoverBooksMax1']);



        Route::get('/lendings-copies', [LendingController::class, "lendingsWithCopies"]);
        Route::get('/user-lendings', [UserController::class, "userLendings"]);
        // Kijelentkezés útvonal
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    });

//admin
Route::middleware(['auth:sanctum',Admin::class])
->group(function () {
    Route::apiResource('/admin/users', UserController::class);
    //Route::get('/admin/users', [UserController::class, 'index']);
    Route::get('/admin/specific-date', [LendingController::class, "dateSpecific"]);
    Route::get('/admin/specific-date/{copy_id}', [LendingController::class, "copySpecific"]);
});




//Librarian
Route::middleware(['auth:sanctum',Librarian::class])
->group(function () {
    Route::get('books-copies', [BookController::class, "booksWithCopies"]);
});



//Warehouseman
Route::middleware(['auth:sanctum',Warehouseman::class])
->group(function () {
    //útvonal
});






