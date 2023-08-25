<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TypeUserController;
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



Route::group(['prefix' => '/group'], function () {
     Route::post('type-user', [TypeUserController::class, 'store']);
     Route::get('type-user/{id}', [TypeUserController::class, 'show']);
     Route::put('type-user/{id}', [TypeUserController::class, 'update']);
     Route::delete('type-user/{id}', [TypeUserController::class, 'destroy']);
     Route::get('type-user', [TypeUserController::class, 'index']);
});

    Route::post('user', [UserController::class, 'store']);
    Route::get('user/{id}', [UserController::class, 'show']);
    Route::put('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{id}', [UserController::class, 'destroy']);
    Route::get('users', [UserController::class, 'index']);
    Route::put('user/restore/{id}', [UserController::class, 'restore']);
