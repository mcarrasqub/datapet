<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', 'App\Http\Controllers\HomeController@index')->name('home.index');

Auth::routes();
Route::middleware(['auth'])->group(function () {
    Route::get('/users', 'App\Http\Controllers\Admin\UserController@index')->name('users.index');
    Route::post('/users', 'App\Http\Controllers\Admin\UserController@store')->name('users.store');
    Route::patch('/users/{user}/toggle-status', 'App\Http\Controllers\Admin\UserController@toggleStatus')->name('users.toggleStatus');
    Route::delete('/users/{user}', 'App\Http\Controllers\Admin\UserController@destroy')->name('users.destroy');
    Route::get('/clients/create', 'App\Http\Controllers\Admin\ClientController@create')->name('clients.create');
    Route::post('/clients', 'App\Http\Controllers\Admin\ClientController@store')->name('clients.store');
});

