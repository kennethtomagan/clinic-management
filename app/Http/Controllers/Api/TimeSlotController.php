<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimeSlotRequest;
use App\Http\Resources\TimeSlotResource;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    
    /**
     * Return List of doctors
     * 
     * @param Request $request
     * @return DoctorResource 
     */
    public function index(TimeSlotRequest $request)
    {
        $date = $request->input('date');
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $slots = null;
        $schedule = Schedule::where('day_of_week', $dayOfWeek)
            ->where('clinic_id', $request->clinic_id)
            ->where('doctor_id', $request->doctor_id)
            ->first();
        if ($schedule) {
            $slots = $schedule->slots;
        }
        return TimeSlotResource::collection($slots);
    }

}
