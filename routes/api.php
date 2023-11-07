<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TypeGroupController;
use App\Http\Controllers\TypeUserController;
use App\Http\Controllers\MeetingController;
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

    Route::group(['prefix' => 'members'], function () {
        Route::put('/{id}', [MemberController::class, 'update']);
        Route::get('/', [MemberController::class, 'index']);
        Route::get('/{id}', [MemberController::class, 'show']);
    });

    Route::group(['prefix' => '/group'], function () {
        Route::get('/', [GroupController::class, 'index']);
        Route::post('/', [GroupController::class, 'store']);
        Route::get('/{id}', [GroupController::class, 'show']);
        Route::put('/{id}', [GroupController::class, 'update']);
        Route::delete('/{id}', [GroupController::class, 'destroy']);

        Route::group(['prefix' => '{groupId}/members'], function () {
            Route::post('/', [MemberController::class, 'store']);
            Route::delete('/{id}', [MemberController::class, 'destroy']);
        });

        Route::group(['prefix' => '{groupId}/meeting-history'], function () {
            Route::get('/', [MeetingController::class, 'index']);
            Route::post('/', [MeetingController::class, 'store']);
            Route::get('/{id}', [MeetingController::class, 'show']);
            Route::put('/{id}', [MeetingController::class, 'update']);
            Route::delete('/{id}', [MeetingController::class, 'destroy']);
        });
    });
});
