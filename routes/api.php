<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AlbumController;
use App\Http\Controllers\API\ArtistController;
use App\Http\Controllers\API\PhotoController;
use App\Http\Controllers\API\ResidentController;
use App\Http\Controllers\API\StyleController;
use App\Http\Controllers\API\EventController;

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
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
});


Route::get('/albums/count', [AlbumController::class, 'count']);
Route::apiResource('albums', AlbumController::class);

Route::get('/artists/count', [ArtistController::class, 'count']);
Route::apiResource('artists', ArtistController::class);

Route::get('/photos/count', [PhotoController::class, 'count']);
Route::apiResource('photos', PhotoController::class);

Route::get('/residents/count', [ResidentController::class, 'count']);
Route::apiResource('residents', ResidentController::class);

Route::get('/styles/count', [StyleController::class, 'count']);
Route::apiResource('styles', StyleController::class);

Route::get('/events/count', [EventController::class, 'count'])->middleware('auth:api');
Route::get('/events/next', [EventController::class, 'next'])->middleware('auth:api');
Route::apiResource('events', EventController::class)->middleware('auth:api');
