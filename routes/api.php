<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth\AuthAPIUFOPAController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TypeUserController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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
Route::get('/redirect', [AuthAPIUFOPAController::class, 'redirect']);
Route::get('/callback', [AuthAPIUFOPAController::class, 'handleCallback']);
Route::post('/register', [AuthController::class, 'register']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::group(['prefix' => '/type-user'], function () {
        Route::post('/', [TypeUserController::class, 'store']);
        Route::get('/{id}', [TypeUserController::class, 'show']);
        Route::put('/{id}', [TypeUserController::class, 'update']);
        Route::delete('/{id}', [TypeUserController::class, 'destroy']);
        Route::get('/', [TypeUserController::class, 'index']);
    });

    Route::group(['prefix' => '/users'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-ufopa', [AuthAPIUFOPAController::class, 'logoutUser']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::put('/set-manager/{id}', [UserController::class, 'setManager']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::patch('/restore/{id}', [UserController::class, 'restore']);
    });

    Route::group(['prefix' => 'members'], function () {
        Route::get('/{id}', [MemberController::class, 'show']);
    });

    Route::group(['prefix' => 'documents'], function () {
        Route::post('/{id}', [DocumentController::class, 'update']);
        Route::get('/{id}', [DocumentController::class, 'show']);
        Route::get('download/{id}', [DocumentController::class, 'download']);
    });

    Route::group(['prefix' => 'meeting-history'], function () {
        Route::post('/{id}', [MeetingController::class, 'update']);
        Route::get('/{id}', [MeetingController::class, 'show']);
        Route::get('download/{id}', [MeetingController::class, 'download']);
    });

    Route::group(['prefix' => 'activity'], function () {
        Route::put('/{id}', [ActivityController::class, 'update']);
        Route::get('/{id}', [ActivityController::class, 'show']);
    });

    Route::group(['prefix' => 'notes'], function () {
        Route::put('/{id}', [NoteController::class, 'update']);
        Route::get('/{id}', [NoteController::class, 'show']);
    });

    Route::group(['prefix' => '/group'], function () {
        Route::get('/', [GroupController::class, 'index']);
        Route::post('/', [GroupController::class, 'store']);
        Route::get('/{id}', [GroupController::class, 'show']);
        Route::put('/{id}', [GroupController::class, 'update']);
        Route::delete('/{id}', [GroupController::class, 'destroy']);

        Route::group(['prefix' => '{groupId}/members'], function () {
            Route::get('/', [MemberController::class, 'index']);
            Route::post('/', [MemberController::class, 'store']);
            Route::put('/{id}', [MemberController::class, 'update']);
            Route::delete('/{id}', [MemberController::class, 'destroy']);
        });

        Route::group(['prefix' => '{groupId}/documents'], function () {
            Route::get('/', [DocumentController::class, 'index']);
            Route::delete('/{id}', [DocumentController::class, 'destroy']);
            Route::post('/', [DocumentController::class, 'store']);
        });

        Route::group(['prefix' => '{groupId}/meeting-history'], function () {
            Route::get('/', [MeetingController::class, 'index']);
            Route::post('/', [MeetingController::class, 'store']);
            Route::delete('/{id}', [MeetingController::class, 'destroy']);
        });

        Route::group(['prefix' => '{groupId}/activity'], function () {
            Route::get('/', [ActivityController::class, 'index']);
            Route::post('/', [ActivityController::class, 'store']);
            Route::delete('/{id}', [ActivityController::class, 'destroy']);
        });

        Route::group(['prefix' => '{groupId}/notes'], function () {
            Route::get('/', [NoteController::class, 'index']);
            Route::post('/', [NoteController::class, 'store']);
            Route::delete('/{id}', [NoteController::class, 'destroy']);
        });
    });
});
