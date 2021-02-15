<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
// Reading list view
Route::get('/home', 'HomeController@index')->name('home');
// Book details view
Route::get('/book-details/{id}', 'BooksController@show');

// Web API

// Store Book
Route::middleware('auth:web')->post('/web-api/post/book', 'BooksController@store');
// Get User's Book List
Route::middleware('auth:web')->get('/web-api/get/user-books', 'BooksController@getUserBookList');
// Update Book Order
Route::middleware('auth:web')->post('/web-api/update/book/{id}', 'BooksController@update');
// Remove Book From User List
Route::middleware('auth:web')->get('/web-api/destroy/book/{id}', 'BooksController@destroy');
// Check if user has saved this book
Route::middleware('auth:web')->get('/web-api/get/book-status/{id}', 'BooksController@bookStatus');
// Get user book ids
Route::middleware('auth:web')->get('/web-api/get/user-book-ids', 'BooksController@getUserBookIds');
// Destroy by volume id
Route::middleware('auth:web')->get('/web-api/destroy/book-by-volume-id/{id}', 'BooksController@destroyByVolumeId');

