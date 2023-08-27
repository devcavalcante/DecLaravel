<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TypeUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('health', function () {
    return response('ok');
});


Route::group(['prefix' => '/group'], function () {
    Route::post('type-user', [TypeUserController::class, 'store']);
    Route::get('type-user/{id}', [TypeUserController::class, 'show']);
    Route::put('type-user/{id}', [TypeUserController::class, 'update']);
    Route::delete('type-user/{id}', [TypeUserController::class, 'destroy']);
    Route::get('type-user', [TypeUserController::class, 'index']);

    Route::post('type-group', [TypeUserController::class, 'store']);
    Route::get('type-group/{id}', [TypeUserController::class, 'show']);
    Route::put('type-group/{id}', [TypeUserController::class, 'update']);
    Route::delete('type-group/{id}', [TypeUserController::class, 'destroy']);
    Route::get('type-group', [TypeUserController::class, 'index']);
});

Route::group(['prefix' => '/group'], function () {
     Route::post('type-user', [TypeUserController::class, 'store']);
     Route::get('type-user/{id}', [TypeUserController::class, 'show']);
     Route::put('type-user/{id}', [TypeUserController::class, 'update']);
     Route::delete('type-user/{id}', [TypeUserController::class, 'destroy']);
     Route::get('type-user', [TypeUserController::class, 'index']);
});

Route::group(['prefix' => '/users'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::get('/', [UserController::class, 'index']);
    Route::put('/restore/{id}', [UserController::class, 'restore']);
});
