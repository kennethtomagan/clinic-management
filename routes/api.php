<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClinicController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TimeSlotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Route::get('/auth/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/auth/user', [AuthController::class, 'currentUser']);
    Route::put('/auth/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/auth/user/update-password', [AuthController::class, 'updatePassword']);
    Route::get('/clinics', [ClinicController::class, 'index']);
    Route::get('/time-slot', [TimeSlotController::class, 'index']);

    // Aappointments
    Route::post('/book-appointment', [AppointmentController::class, 'store']);
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::get('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancelAppointment']);
    Route::get('/appointments/logs', [AppointmentController::class, 'logs']);


    //  *** Admin ***
    // Patients
    Route::apiResource('patients', PatientController::class);
    Route::put('patients/{patient}/update-password', [PatientController::class, 'updatePassword']);

    // Doctors
    Route::get('/doctors/active', [DoctorController::class, 'getActiveDoctors']);
    Route::get('/doctors/available-doctors', [DoctorController::class, 'availableDoctors']);
    Route::apiResource('doctors', DoctorController::class);
    Route::apiResource('products', ProductController::class);

});
