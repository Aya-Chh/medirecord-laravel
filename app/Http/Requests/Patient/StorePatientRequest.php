<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin can create patient records (according to spec: POST/api/v1/patients -> admin)
        return $this->user()->can('create', \App\Models\Patient::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'groupe_sanguin' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string',
            'antecedents' => 'nullable|string',
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
            'user_id.required' => 'L\'ID de l\'utilisateur est requis.',
            'user_id.exists' => 'L\'utilisateur spécifié n\'existe pas.',
            'groupe_sanguin.required' => 'Le groupe sanguin est requis.',
            'groupe_sanguin.in' => 'Le groupe sanguin doit être un des suivants : A+, A-, B+, B-, AB+, AB-, O+, O-.',
            'allergies.string' => 'Les allergies doivent être une chaîne de caractères.',
            'antecedents.string' => 'Les antécédents doivent être une chaîne de caractères.',
        ];
    }
}