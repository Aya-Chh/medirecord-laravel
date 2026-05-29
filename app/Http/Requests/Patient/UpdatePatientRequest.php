<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // The user can update their own patient record, or an admin can update any patient record.
        // We assume that the controller will check the policy for the patient.
        // The policy will handle if the user is the owner (patient himself) or an admin.
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
            'groupe_sanguin' => 'sometimes|required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'sometimes|nullable|string',
            'antecedents' => 'sometimes|nullable|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'groupe_sanguin.required' => 'Le groupe sanguin est requis.',
            'groupe_sanguin.in' => 'Le groupe sanguin doit être un des suivants : A+, A-, B+, B-, AB+, AB-, O+, O-.',
            'allergies.string' => 'Les allergies doivent être une chaîne de caractères.',
            'antecedents.string' => 'Les antécédents doivent être une chaîne de caractères.',
        ];
    }
}