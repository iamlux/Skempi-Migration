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

Route::get('genres', 'MigrationController@migrateGenreFromAudios');
Route::get('albums', 'MigrationController@migrateAlbum');
Route::get('artists', 'MigrationController@migrateArtist');
Route::get('songs', 'MigrationController@migrateSongs');
Route::get('tags', 'MigrationController@migrateTagsFromAudios');
Route::get('fill', 'MigrationController@fillUat');