<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClinicController;
use App\Http\Controllers\Api\DoctorController;
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
    Route::get('/auth/user', [AuthController::class, 'currentUser'])->middleware('auth:sanctum');
    Route::get('/clinics', [ClinicController::class, 'index']);
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/doctors/available-doctors', [DoctorController::class, 'availableDoctors']);
    Route::get('/time-slot', [TimeSlotController::class, 'index']);
    Route::post('/book-appointment', [AppointmentController::class, 'store']);
});
