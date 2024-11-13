<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvailableDoctorsRequest;
use App\Http\Requests\CreateDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Doctor::query();
    
        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
               ->orWhere('email', 'like', "%{$search}%");
        }
    
        // Apply sorting
        $orderBy = $request->input('order_by', 'id');
        $orderDirection = $request->input('order_direction', 'asc');
        $orderDirection = in_array(strtolower($orderDirection), ['asc', 'desc']) ? $orderDirection : 'asc';
    
        $query->orderBy($orderBy, $orderDirection);
    
        // Handle pagination
        $perPage = $request->input('per_page', 10);
        $doctors = $query->paginate($perPage)->appends($request->except('page'));
        // Use DoctorResource for transformation
        return DoctorResource::collection($doctors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDoctorRequest $request)
    {
        $doctor = new Doctor();

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $doctor->type = User::DOCTOR_TYPE;
        $doctor->first_name = $request->first_name;
        $doctor->last_name = $request->last_name;
        $doctor->email = $request->email;
        $doctor->phone = $request->phone;
        $doctor->gender = $request->gender;
        $doctor->address = $request->address;
        $doctor->rfid_number = $request->rfid_number;
        $doctor->password = Hash::make($request->password);

        if ($request->rfid_number) {
            $doctor->rfid_number = $request->rfid_number ;
        }
        
        // Handle avatar upload if provided
        if ($request->hasFile('avatar')) {
            // Delete the old avatar if it exists
            if ($doctor->avatar) {
                Storage::delete($doctor->avatar);
            }
            
            // Store the new avatar with a unique filename in the 'doctor' directory
            $fileName = time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            $path = $request->file('avatar')->storeAs('doctor', $fileName, 'public');

            // Update the doctor model with the new avatar URL
            $doctor->avatar_url = $path;
        }

        $doctor->save();
        
        $doctor->doctorDetails()->create([
            'clinic_id' => $request->clinic_id,
            'education' => $request->education,
            'specialization' => $request->specialization,
            'subspecialty' => $request->subspecialty,
            'years_of_experience' => $request->years_of_experience,
            'status' => $request->status,
            'education' => $request->education,
            'profile_description' => $request->profile_description,
        ]);

        return new DoctorResource($doctor);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        return new DoctorResource($doctor);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorRequest $request, string $id)
    {
        
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $doctor->first_name = $request->first_name;
        $doctor->last_name = $request->last_name;
        $doctor->email = $request->email;
        $doctor->phone = $request->phone;
        $doctor->gender = $request->gender;
        $doctor->address = $request->address;
        if ($request->rfid_number) {
            $doctor->rfid_number = $request->rfid_number ;
        }
        
        // Handle avatar upload if provided
        if ($request->hasFile('avatar')) {
            // Delete the old avatar if it exists
            if ($doctor->avatar) {
                Storage::delete($doctor->avatar);
            }
            
            // Store the new avatar with a unique filename in the 'doctor' directory
            $fileName = time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            $path = $request->file('avatar')->storeAs('doctor', $fileName, 'public');

            // Update the doctor model with the new avatar URL
            $doctor->avatar_url = $path;
        }

        $doctorDetails = $doctor->doctorDetails;
        $doctorDetails->clinic_id = $request->clinic_id;
        $doctorDetails->education = $request->education;
        $doctorDetails->specialization = $request->specialization;
        $doctorDetails->subspecialty = $request->subspecialty;
        $doctorDetails->years_of_experience = $request->years_of_experience;
        $doctorDetails->status = $request->status;
        $doctorDetails->profile_description = $request->profile_description;
        $doctorDetails->save();
        $doctor->save();

        return new DoctorResource($doctor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Return List of doctors
     * 
     * @param Request $request
     * @return DoctorResource 
     */
    public function getActiveDoctors(Request $request)
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
