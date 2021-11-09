<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AlbumController;
use App\Http\Controllers\API\PhotoController;
use App\Http\Controllers\API\StyleController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\ArtistController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\ResidentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('user')->group(function () {
    Route::post('login', [UserController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
        Route::get('/', [UserController::class, 'index']);
        Route::get('/count', [UserController::class, 'count']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::post('logout', [UserController::class, 'logout']);
        Route::get('/me', function (Request $request) {
            return $request->user();
        });
    });
});


Route::prefix('albums')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [AlbumController::class, 'store']);
        Route::put('/{album}', [AlbumController::class, 'update']);
        Route::delete('/{album}', [AlbumController::class, 'destroy']);
    });
    Route::get('/count', [AlbumController::class, 'count']);
    Route::get('/', [AlbumController::class, 'index']);
    Route::get('/{album}', [AlbumController::class, 'show']);
});


Route::prefix('artists')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [ArtistController::class, 'store']);
        Route::put('/{artist}', [ArtistController::class, 'update']);
        Route::delete('/{artist}', [ArtistController::class, 'destroy']);
    });
    Route::get('/count', [ArtistController::class, 'count']);
    Route::get('/', [ArtistController::class, 'index']);
    Route::get('/{artist}', [ArtistController::class, 'show']);
});


Route::prefix('photos')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [PhotoController::class, 'store']);
        Route::put('/{photo}', [PhotoController::class, 'update']);
        Route::delete('/{photo}', [PhotoController::class, 'destroy']);
    });
    Route::get('/count', [PhotoController::class, 'count']);
    Route::get('/', [PhotoController::class, 'index']);
    Route::get('/{photo}', [PhotoController::class, 'show']);
});


Route::prefix('residents')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [ResidentController::class, 'store']);
        Route::put('/{resident}', [ResidentController::class, 'update']);
        Route::delete('/{resident}', [ResidentController::class, 'destroy']);
    });
    Route::get('/count', [ResidentController::class, 'count']);
    Route::get('/', [ResidentController::class, 'index']);
    Route::get('/{resident}', [ResidentController::class, 'show']);
});


Route::prefix('styles')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [StyleController::class, 'store']);
        Route::put('/{style}', [StyleController::class, 'update']);
        Route::delete('/{style}', [StyleController::class, 'destroy']);
    });
    Route::get('/count', [StyleController::class, 'count']);
    Route::get('/', [StyleController::class, 'index']);
    Route::get('/{style}', [StyleController::class, 'show']);
});


Route::prefix('events')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [EventController::class, 'store']);
        Route::put('/{event}', [EventController::class, 'update']);
        Route::delete('/{event}', [EventController::class, 'destroy']);
    });
    Route::get('/count', [EventController::class, 'count']);
    Route::get('/next', [EventController::class, 'next']);
    Route::get('/', [EventController::class, 'index']);
    Route::get('/{event}', [EventController::class, 'show']);
});


Route::prefix('messages')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [MessageController::class, 'store']);
        Route::put('/{message}', [MessageController::class, 'update']);
        Route::delete('/{message}', [MessageController::class, 'destroy']);
    });
    Route::get('/count', [MessageController::class, 'count']);
    Route::get('/', [MessageController::class, 'index']);
    Route::get('/{message}', [MessageController::class, 'show']);
    Route::get('/active', [MessageController::class, 'active']);
});
