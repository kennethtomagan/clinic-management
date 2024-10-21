<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvailableDoctorsRequest extends FormRequest
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
        return [
            'clinic_id' => 'required|exists:clinics,id',
            'date' => 'required|date|after:today',
        ];
    }

    /**
     * Get the custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'clinic_id.required' => 'The clinic ID is required.',
            'clinic_id.exists' => 'The selected clinic ID is invalid.',
            'date.required' => 'The date is required.',
            'date.date' => 'The date must be a valid date.',
            'date.after' => 'The date must be a future date.',
        ];
    }
}
