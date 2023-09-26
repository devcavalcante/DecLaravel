<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TypeGroupController;
use App\Http\Controllers\TypeUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::post('/login', [AuthController::class, 'login']);
Route::post('email/verify', [AuthController::class, 'verify']);
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::group(['prefix' => '/type-user'], function () {
        Route::post('/', [TypeUserController::class, 'store']);
        Route::get('/{id}', [TypeUserController::class, 'show']);
        Route::put('/{id}', [TypeUserController::class, 'update']);
        Route::delete('/{id}', [TypeUserController::class, 'destroy']);
        Route::get('/', [TypeUserController::class, 'index']);
    });

    Route::group(['prefix' => '/type-group'], function () {
        Route::post('/', [TypeGroupController::class, 'store']);
        Route::get('/{id}', [TypeGroupController::class, 'show']);
        Route::put('/{id}', [TypeGroupController::class, 'update']);
        Route::delete('/{id}', [TypeGroupController::class, 'destroy']);
        Route::get('/', [TypeGroupController::class, 'index']);
    });

    Route::group(['prefix' => '/users'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::patch('/restore/{id}', [UserController::class, 'restore']);
    });
});
