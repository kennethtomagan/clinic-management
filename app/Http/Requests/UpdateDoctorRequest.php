<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the patient's ID from the route
        $patientId = $this->route('doctor');
        return [
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $patientId,
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:Male,Female',
            'address' => 'nullable|string|max:255',
            'clinic_id' => 'required',
            'education' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'subspecialty' => 'nullable|string|max:255',
            'years_of_experience' => 'required|string|max:255',
            'status' => 'required|string',
            'profile_description' => 'nullable|string',
        ];
    }
}
