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

Route::get('/home', 'HomeController@index');
Route::get('/logs', 'UsersLogsController@index');

Route::get('/notes', 'UsersNotesController@index');
Route::post('/edit-note', 'UsersNotesController@saveNote');
Route::post('/delete-note', 'UsersNotesController@deleteNote');
Route::post('/create-note', 'UsersNotesController@createNote');