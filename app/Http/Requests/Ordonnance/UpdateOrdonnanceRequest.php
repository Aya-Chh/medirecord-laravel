<?php

namespace App\Http\Requests\Ordonnance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrdonnanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // The user can update their own ordonnance (if they are the patient) or 
        // an admin or medecin (if they are the medic of the ordonnance).
        // We assume that the controller will check the policy for the ordonnance.
        // The policy will handle the authorization.
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
            'consultation_id' => 'sometimes|required|exists:consultations,id',
            'medecin_id' => 'sometimes|required|exists:medecins,id',
            'patient_id' => 'sometimes|required|exists:patients,id',
            'date_emission' => 'sometimes|required|date',
            'date_expiration' => 'sometimes|nullable|date|after_or_equal:date_emission',
            'instructions_generales' => 'sometimes|nullable|string',
            'statut' => 'sometimes|in:active,expiree,annulee',
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
            'consultation_id.required' => 'L\'ID de la consultation est requis.',
            'consultation_id.exists' => 'La consultation spécifiée n\'existe pas.',
            'medecin_id.required' => 'L\'ID du médecin est requis.',
            'medecin_id.exists' => 'Le médecin spécifié n\'existe pas.',
            'patient_id.required' => 'L\'ID du patient est requis.',
            'patient_id.exists' => 'Le patient spécifié n\'existe pas.',
            'date_emission.required' => 'La date d\'émission est requise.',
            'date_emission.date' => 'La date d\'émission doit être une date valide.',
            'date_expiration.date' => 'La date d\'expiration doit être une date valide.',
            'date_expiration.after_or_equal' => 'La date d\'expiration doit être égale ou postérieure à la date d\'émission.',
            'instructions_generales.string' => 'Les instructions générales doivent être une chaîne de caractères.',
            'statut.in' => 'Le statut doit être un des suivants : active, expiree, annulee.',
        ];
    }
}