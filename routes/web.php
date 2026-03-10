<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PetController;

Route::get('/', [HomeController::class, 'index'])->name('home.index');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::resource('pets', PetController::class);
});