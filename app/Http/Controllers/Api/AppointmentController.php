<?php

namespace App\Http\Controllers\Api;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentLogResource;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $appointment = Appointment::where('patient_id', $request->user()->id)
            ->with('slot', 'doctor', 'clinic')
            ->get();
        return AppointmentResource::collection($appointment);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $appointment = Appointment::where('date', $request->date)
                        ->where('clinic_id', $request->clinic_id)
                        ->where('doctor_id', $request->doctor_id)
                        ->where('slot_id', $request->slot)
                        ->first();
        
        $appointment = Appointment::where('date', $request->date)
        ->where('clinic_id', $request->clinic_id)
        ->where('doctor_id', $request->doctor_id)
        ->where('slot_id', $request->slot)
        ->first();

        if ($appointment) {
            return response()->json([
                'message' => 'The selected Date and time slot is not available.',
                'error' => true
            ], 409);
        }

        $newAppointment = Appointment::create([
            'appointment_id' => 'APT-' . Str::random(8),
            'clinic_id' => $request->clinic_id,
            'doctor_id' => $request->doctor_id,
            'slot_id' => $request->slot,
            'patient_id' => $request->user()->id,
            'date' => $request->date,
            'description' => $request->reason
        ]);
        
        return new AppointmentResource($newAppointment);
    }

    public function cancelAppointment(Request $request, Appointment $appointment)
    {
        $appointment->update([
            'status' => AppointmentStatus::Canceled
        ]);

        return new AppointmentResource($appointment);
    }


    public function logs(Request $request)
    {
        $appointmentIds = Appointment::where('patient_id', $request->user()->id)
            ->get()
            ->pluck('id')
            ->toArray();

        $logs = AppointmentLog::with('appointment')->whereIn('appointment_id', $appointmentIds)->get();

        return AppointmentLogResource::collection($logs);
    }
}
