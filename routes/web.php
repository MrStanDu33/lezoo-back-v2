<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/media/{file}', [ function ($file) {
    $path = storage_path('app/public/uploadedFiles/'.$file);
    if (file_exists($path)) {
        return Storage::response('public/uploadedFiles/'.$file);
    }
    abort(404);
}]);
