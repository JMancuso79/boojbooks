<?php

use Illuminate\Http\Request;

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
// Get Books From Google API
Route::get('/get/books-from-google', 'BooksController@getBooks');
// Get Book From Google API
Route::get('/get/book-from-google/{id}', 'BooksController@getBookByVolumeId');
