<?php

use App\Http\Controllers\Admin\DoctorTaskController;
use App\Http\Controllers\Doctor\ClinicalObservationController;
use App\Http\Controllers\Doctor\VaccinationController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalExamController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', 'App\Http\Controllers\HomeController@index')->name('home.index');

Auth::routes();

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', 'App\Http\Controllers\DashboardController@index')->name('dashboard.index');

    Route::get('/users', 'App\Http\Controllers\Admin\UserController@index')->name('users.index');
    Route::post('/users', 'App\Http\Controllers\Admin\UserController@store')->name('users.store');
    Route::get('/users/{user}/edit', 'App\Http\Controllers\Admin\UserController@edit')->name('users.edit');
    Route::put('/users/{user}', 'App\Http\Controllers\Admin\UserController@update')->name('users.update');
    Route::patch('/users/{user}/toggle-status', 'App\Http\Controllers\Admin\UserController@toggleStatus')->name('users.toggleStatus');
    Route::delete('/users/{user}', 'App\Http\Controllers\Admin\UserController@destroy')->name('users.destroy');
    Route::get('/admin/doctor-tasks', [DoctorTaskController::class, 'index'])->name('tasks.index');
    Route::patch('/admin/doctor-tasks/{task}/status', [DoctorTaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::patch('/doctor/tasks/{task}/status', [DoctorTaskController::class, 'updateOwnStatus'])->name('tasks.updateOwnStatus');
    Route::delete('/admin/doctor-tasks/{task}', [DoctorTaskController::class, 'destroy'])->name('tasks.destroy');

    Route::get('/clients/create', 'App\Http\Controllers\Admin\ClientController@create')->name('clients.create');
    Route::post('/clients', 'App\Http\Controllers\Admin\ClientController@store')->name('clients.store');

    Route::get('/doctor/clients', 'App\Http\Controllers\Doctor\ClientDoctorController@index')->name('clients.index');
    Route::put('/doctor/clients/{client}', [\App\Http\Controllers\Doctor\ClientDoctorController::class, 'update'])->name('doctor.clients.update');
    Route::get('/medical-records', 'App\Http\Controllers\Doctor\MedicalRecordController@index')->name('medical_records.index');
    Route::get('/medical-records/{pet}', 'App\Http\Controllers\Doctor\MedicalRecordController@show')->name('medical_records.show');
    Route::get('/medical-records/{pet}/create', 'App\Http\Controllers\Doctor\MedicalRecordController@create')->name('medical_records.create');
    Route::post('/medical-records/{pet}', 'App\Http\Controllers\Doctor\MedicalRecordController@store')->name('medical_records.store');
    Route::get('/medical-records-edit/{medicalRecord}', 'App\Http\Controllers\Doctor\MedicalRecordController@edit')->name('medical_records.edit');
    Route::put('/medical-records/{medicalRecord}', 'App\Http\Controllers\Doctor\MedicalRecordController@update')->name('medical_records.update');
    Route::delete('/medical-records/{medicalRecord}', 'App\Http\Controllers\Doctor\MedicalRecordController@destroy')->name('medical_records.destroy');
    Route::put('/medical-records/{pet}/update-pet', 'App\Http\Controllers\Doctor\MedicalRecordController@updatePet')->name('medical_records.update_pet');
    Route::post('/medical-records/{medicalRecord}/observations', [ClinicalObservationController::class, 'store'])->name('clinical_observations.store');
    Route::get('/clinical-observations/{clinicalObservation}/edit', [ClinicalObservationController::class, 'edit'])->name('clinical_observations.edit');
    Route::put('/clinical-observations/{clinicalObservation}', [ClinicalObservationController::class, 'update'])->name('clinical_observations.update');
    Route::delete('/clinical-observations/{clinicalObservation}', [ClinicalObservationController::class, 'destroy'])->name('clinical_observations.destroy');
    Route::post('/pets/{pet}/vaccinations', [VaccinationController::class, 'store'])->name('vaccinations.store');
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

    Route::resource('appointments', AppointmentController::class)->except(['create', 'show', 'edit']);

    Route::get('/doctor/appointments', 'App\Http\Controllers\Doctor\AppointmentDoctorController@index')->name('doctor.appointments.index');
    Route::get('/doctor/appointments/events', 'App\Http\Controllers\Doctor\AppointmentDoctorController@events')->name('doctor.appointments.events');

    Route::get('/adoption', 'App\Http\Controllers\AdoptionController@index')->name('adoption.index');
    Route::get('/adoption/{pet}', 'App\Http\Controllers\AdoptionController@show')->name('adoption.show');
    Route::post('/adoption', 'App\Http\Controllers\AdoptionController@store')->name('adoption.store');
    Route::get('/admin/adoption-requests', 'App\Http\Controllers\AdoptionController@adminIndex')->name('adoption.admin.index');
    Route::patch('/admin/adoption-requests/{adoptionRequest}/approve', 'App\Http\Controllers\AdoptionController@approve')->name('adoption.approve');
    Route::patch('/admin/adoption-requests/{adoptionRequest}/reject', 'App\Http\Controllers\AdoptionController@reject')->name('adoption.reject');
    Route::get('/admin/adoptions/create', 'App\Http\Controllers\AdoptionController@create')->name('admin.adoptions.create');
    Route::post('/admin/adoptions', 'App\Http\Controllers\AdoptionController@storePet')->name('admin.adoptions.store');
    Route::get('/admin/adoptions/{pet}/edit', 'App\Http\Controllers\AdoptionController@edit')->name('admin.adoptions.edit');
    Route::put('/admin/adoptions/{pet}', 'App\Http\Controllers\AdoptionController@updatePet')->name('admin.adoptions.update');
});
