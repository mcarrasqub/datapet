<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', 'App\Http\Controllers\HomeController@index')->name('home.index');

Auth::routes();
Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home.index');
Route::get('/pets', 'App\Http\Controllers\PetController@index')->name('pets.index');
Route::get('/pets/create', 'App\Http\Controllers\PetController@create')->name('pets.create');
Route::post('/pets', 'App\Http\Controllers\PetController@store')->name('pets.store');
Route::get('/pets/{pet}', 'App\Http\Controllers\PetController@show')->name('pets.show');
Route::get('/pets/{pet}/edit', 'App\Http\Controllers\PetController@edit')->name('pets.edit');
Route::put('/pets/{pet}', 'App\Http\Controllers\PetController@update')->name('pets.update');
Route::delete('/pets/{pet}', 'App\Http\Controllers\PetController@destroy')->name('pets.destroy');