<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClientController;

Route::get('/', 'App\Http\Controllers\HomeController@index')->name('home.index');

Auth::routes();
Route::middleware(['auth'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
});


Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home.index');
Route::get('/pets', 'App\Http\Controllers\PetController@index')->name('pets.index');
Route::get('/pets/create', 'App\Http\Controllers\PetController@create')->name('pets.create');
Route::post('/pets', 'App\Http\Controllers\PetController@store')->name('pets.store');
Route::get('/pets/{pet}', 'App\Http\Controllers\PetController@show')->name('pets.show');
Route::get('/pets/{pet}/edit', 'App\Http\Controllers\PetController@edit')->name('pets.edit');
Route::put('/pets/{pet}', 'App\Http\Controllers\PetController@update')->name('pets.update');
Route::delete('/pets/{pet}', 'App\Http\Controllers\PetController@destroy')->name('pets.destroy');