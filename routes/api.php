<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// ------------------------------
// start public Routes ----------
// ------------------------------

Route::post('/send-verfiy-email', [UserController::class, 'sendVerfiyEmail']);
Route::get('/verify-email/{id}', [UserController::class, 'verfiyEmail']);





// -------------------------
//  Auth Routes ------------
// -------------------------

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [UserController::class, 'store']);



// -------------------------
//  google Routes ----------
// -------------------------

Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);


// ------------------------------
// End public Routes ------------
// ------------------------------








// -----------------------------------------
//  Start Protected Auth Routes ------------
// -----------------------------------------


Route::middleware(['auth:sanctum'])->group(function () {

    // -------------------------
    //  Currentuser Routes -----
    // -------------------------

    Route::controller(AuthController::class)->group(function () {
        Route::get('/currentuser', 'getCurrentUser');
        Route::post('/logout', 'logout');
    });





    // -------------------------
    //  Auth  users Routes -----
    // -------------------------

    Route::controller(UserController::class)->group(function () {
        Route::get('/user/{id}', 'show');
        Route::post('/update-user/{id}', 'update');
        Route::post('/check-password-user/{id}', 'checkPassword');
    });
});



// -----------------------------------------
//  End Protected Auth Routes ------------
// -----------------------------------------


// -------------------------
//  Admin  Routes ----------
// -------------------------

Route::middleware(['auth:sanctum', 'checkAdmin'])->group(function () {

    // -------------------------
    // users Routes ------------
    // -------------------------

    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index');
        Route::get('/users-ids', 'getUsersIds');
        Route::get('/users-count', 'getUsersCount');
        Route::get('/search-for-user-by-name', 'searchForUsersByName');
        Route::delete('/delete-user/{id}', 'destroy');
    });
});
