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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('/albums/count', [AlbumController::class, 'count'])->middleware('auth:api');
Route::apiResource('albums', AlbumController::class)->middleware('auth:api');

Route::get('/artists/count', [ArtistController::class, 'count'])->middleware('auth:api');
Route::apiResource('artists', ArtistController::class)->middleware('auth:api');

Route::get('/photos/count', [PhotoController::class, 'count'])->middleware('auth:api');
Route::apiResource('photos', PhotoController::class)->middleware('auth:api');

Route::get('/residents/count', [ResidentController::class, 'count'])->middleware('auth:api');
Route::apiResource('residents', ResidentController::class)->middleware('auth:api');

Route::get('/styles/count', [StyleController::class, 'count'])->middleware('auth:api');
Route::apiResource('styles', StyleController::class)->middleware('auth:api');

Route::get('/events/count', [EventController::class, 'count'])->middleware('auth:api');
Route::get('/events/next', [EventController::class, 'next'])->middleware('auth:api');
Route::apiResource('events', EventController::class)->middleware('auth:api');
