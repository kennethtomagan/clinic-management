<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Resources\PatientListResource;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource with pagination, search filter, and sorting.
     */
     public function index(Request $request)
     {
         $query = Patient::query();
     
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
         $patients = $query->paginate($perPage)->appends($request->except('page'));
     
         // Use PatientListResource for transformation
         return PatientListResource::collection($patients);
     }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePatientRequest $request)
    {
        $patient = new Patient();

        $patient->type = User::PATIENT_TYPE;
        $patient->first_name = $request->first_name;
        $patient->last_name = $request->last_name;
        $patient->email = $request->email;
        $patient->phone = $request->phone;
        $patient->address = $request->address;
        $patient->gender = $request->gender;
        $patient->rfid_number = $request->rfid_number;
        $patient->password = Hash::make($request->password);
        
        // Handle avatar upload if provided
        if ($request->hasFile('avatar')) {
            // Delete the old avatar if it exists
            if ($patient->avatar) {
                Storage::delete($patient->avatar);
            }
            
            // Store the new avatar with a unique filename in the 'patient' directory
            $fileName = time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            $path = $request->file('avatar')->storeAs('patient', $fileName, 'public');

            $patient->avatar_url = $path;
        }

        $patient->save();

        return response()->json(['message' => 'Patient created successfully', 'data' => $patient], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        return new PatientListResource($patient);
        // return response()->json($patient);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, string $id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        $patient->first_name = $request->first_name;
        $patient->last_name = $request->last_name;
        $patient->email = $request->email;
        $patient->phone = $request->phone;
        $patient->gender = $request->gender;
        $patient->address = $request->address;
        if ($request->rfid_number) {
            $patient->rfid_number = $request->rfid_number ;
        }
        
        // Handle avatar upload if provided
        if ($request->hasFile('avatar')) {
            // Delete the old avatar if it exists
            if ($patient->avatar) {
                Storage::delete($patient->avatar);
            }
            
            // Store the new avatar with a unique filename in the 'patient' directory
            $fileName = time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            $path = $request->file('avatar')->storeAs('patient', $fileName, 'public');

            // Update the patient model with the new avatar URL
            $patient->avatar_url = $path;
            // // Store the new avatar and update the avatar path
            // $path = $request->file('avatar')->store('patient');
            // $patient->avatar_url = $path;
        }

        $patient->save();

        return response()->json(['message' => 'Patient updated successfully', 'data' => $patient]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        $patient->delete();

        return response()->json(['message' => 'Patient deleted successfully']);
    }

    public function updatePassword(UpdateUserPasswordRequest $request, string $id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }


        // Update the password
        $patient->password = Hash::make($request->new_password);
        $patient->save();

        return response()->json(['message' => 'Patient password updated successfully', 'data' => $patient]);
    }
}
