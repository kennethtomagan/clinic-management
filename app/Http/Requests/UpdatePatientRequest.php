<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Set to true if authorization checks are not required
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the patient's ID from the route
        $patientId = $this->route('patient');
        return [
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $patientId,
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:Male,Female',
            'address' => 'nullable|string|max:255',
        ];
    }
}
