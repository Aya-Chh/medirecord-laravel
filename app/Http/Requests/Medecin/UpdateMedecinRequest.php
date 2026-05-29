<?php

namespace App\Http\Requests\Medecin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedecinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // The user can update their own medecin record, or an admin can update any medecin record.
        // We assume that the controller will check the policy for the medecin.
        // The policy will handle if the user is the owner (medecin himself) or an admin.
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
            'specialite' => 'sometimes|required|string|max:255',
            'numero_ordre' => 'sometimes|required|string|max:255|unique:medecins,numero_ordre,' . $this->route('medecin'),
            'hopital' => 'sometimes|nullable|string|max:255',
            'bio' => 'sometimes|nullable|string',
            'disponible' => 'sometimes|boolean',
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
            'specialite.string' => 'La spécialité doit être une chaîne de caractères.',
            'specialite.max' => 'La spécialité ne doit pas dépasser 255 caractères.',
            'numero_ordre.string' => 'Le numéro d\'ordre doit être une chaîne de caractères.',
            'numero_ordre.max' => 'Le numéro d\'ordre ne doit pas dépasser 255 caractères.',
            'numero_ordre.unique' => 'Ce numéro d\'ordre est déjà utilisé.',
            'hopital.string' => 'L\'hôpital doit être une chaîne de caractères.',
            'hopital.max' => 'L\'hôpital ne doit pas dépasser 255 caractères.',
            'bio.string' => 'La biographie doit être une chaîne de caractères.',
            'disponible.boolean' => 'Le champ disponible doit être un booléen.',
        ];
    }
}