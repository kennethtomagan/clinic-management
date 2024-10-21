<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvailableDoctorsRequest;
use App\Http\Resources\DoctorResource;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    
    /**
     * Return List of doctors
     * 
     * @param Request $request
     * @return DoctorResource 
     */
    public function index(Request $request)
    {
        $doctors = User::where('type', User::DOCTOR_TYPE)
            ->with('doctorDetail')
            ->whereHas('doctorDetail', function ($query) {
                $query->where('status', 'active');
            })->get();
        return DoctorResource::collection($doctors);
    }

    /**
     * Return List of available doctors
     * 
     * @param Request $request
     * @return DoctorResource 
     */
    public function availableDoctors(AvailableDoctorsRequest $request)
    {
        $date = $request->input('date');
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $availableDoctorIds = Schedule::where('day_of_week', $dayOfWeek)
            ->where('clinic_id', $request->clinic_id)
            ->get()->pluck('doctor_id')->toArray();

        $doctors = User::where('type', User::DOCTOR_TYPE)
            ->with('doctorDetail')
            ->whereIn('id', $availableDoctorIds)
            ->whereHas('doctorDetail', function ($query) {
                $query->where('status', 'active');
            })->get();
        return DoctorResource::collection($doctors);
    }
}
