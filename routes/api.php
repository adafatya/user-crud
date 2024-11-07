<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureUserIsLoggedIn;

Route::middleware([EnsureUserIsLoggedIn::class])->group(function () {
    Route::get('/users', [UserController::class, 'getUsersData']);
    Route::get('/users/{id}', [UserController::class, 'getUserDataById']);
    Route::post('/users', [UserController::class, 'addUser']);
    Route::put('/users/{id}', [UserController::class, 'updateUser']);
    Route::delete('/users/{id}', [UserController::class, 'deleteUserById']);
});

Route::post('/register', [UserController::class, 'addUser']);
Route::post('/login', [UserController::class, 'login']);
