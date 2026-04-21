<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MedicalExamController;
use App\Http\Controllers\Admin\DoctorTaskController;

Route::get('/', 'App\Http\Controllers\HomeController@index')->name('home.index');

Auth::routes();

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', 'App\Http\Controllers\DashboardController@index')->name('dashboard.index');

    Route::get('/users', 'App\Http\Controllers\Admin\UserController@index')->name('users.index');
    Route::post('/users', 'App\Http\Controllers\Admin\UserController@store')->name('users.store');
    Route::patch('/users/{user}/toggle-status', 'App\Http\Controllers\Admin\UserController@toggleStatus')->name('users.toggleStatus');
    Route::delete('/users/{user}', 'App\Http\Controllers\Admin\UserController@destroy')->name('users.destroy');
    Route::get('/admin/doctor-tasks', [DoctorTaskController::class, 'index'])->name('tasks.index');
    Route::patch('/admin/doctor-tasks/{task}/status', [DoctorTaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::patch('/doctor/tasks/{task}/status', [DoctorTaskController::class, 'updateOwnStatus'])->name('tasks.updateOwnStatus');
    Route::delete('/admin/doctor-tasks/{task}', [DoctorTaskController::class, 'destroy'])->name('tasks.destroy');

    Route::get('/clients/create', 'App\Http\Controllers\Admin\ClientController@create')->name('clients.create');
    Route::post('/clients', 'App\Http\Controllers\Admin\ClientController@store')->name('clients.store');

    Route::get('/doctor/clients', 'App\Http\Controllers\Doctor\ClientDoctorController@index')->name('clients.index');
    Route::get('/medical-records', 'App\Http\Controllers\Doctor\MedicalRecordController@index')->name('medical_records.index');
    Route::get('/medical-records/{pet}', 'App\Http\Controllers\Doctor\MedicalRecordController@show')->name('medical_records.show');
    Route::get('/medical-records/{pet}/create', 'App\Http\Controllers\Doctor\MedicalRecordController@create')->name('medical_records.create');
    Route::post('/medical-records/{pet}', 'App\Http\Controllers\Doctor\MedicalRecordController@store')->name('medical_records.store');
    Route::get('/medical-records-edit/{medicalRecord}', 'App\Http\Controllers\Doctor\MedicalRecordController@edit')->name('medical_records.edit');
    Route::put('/medical-records/{medicalRecord}', 'App\Http\Controllers\Doctor\MedicalRecordController@update')->name('medical_records.update');
    Route::delete('/medical-records/{medicalRecord}', 'App\Http\Controllers\Doctor\MedicalRecordController@destroy')->name('medical_records.destroy');
    Route::post('/pets/{pet}/exams', [MedicalExamController::class, 'store'])->name('medical_exams.store');
    Route::get('/medical-exams/{medicalExam}/view', [MedicalExamController::class, 'view'])->name('medical_exams.view');
    Route::get('/medical-exams/{medicalExam}/download', [MedicalExamController::class, 'download'])->name('medical_exams.download');

    Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home.index');
    Route::get('/my-exams', 'App\Http\Controllers\PetController@exams')->name('pets.exams');
    Route::get('/pets', 'App\Http\Controllers\PetController@index')->name('pets.index');
    Route::get('/pets/create', 'App\Http\Controllers\PetController@create')->name('pets.create');
    Route::post('/pets', 'App\Http\Controllers\PetController@store')->name('pets.store');
    Route::get('/pets/{pet}', 'App\Http\Controllers\PetController@show')->name('pets.show');
    Route::get('/pets/{pet}/edit', 'App\Http\Controllers\PetController@edit')->name('pets.edit');
    Route::put('/pets/{pet}', 'App\Http\Controllers\PetController@update')->name('pets.update');
    Route::delete('/pets/{pet}', 'App\Http\Controllers\PetController@destroy')->name('pets.destroy');
});