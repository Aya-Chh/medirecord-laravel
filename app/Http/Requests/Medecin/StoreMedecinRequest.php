<?php

namespace App\Http\Requests\Medecin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedecinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin can create a medecin record for any user.
        // However, note that the medecin record is tied to a user.
        // We assume that the user already exists and has the 'medecin' role.
        // The controller will handle setting the user_id from the authenticated user if not admin?
        // But according to the spec, only admin can create medecin records.
        // However, the spec also says: POST /api/v1/medecins -> admin
        // So we check if the user has the admin role.
        return $this->user()->can('create', \App\Models\Medecin::class);
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
            'specialite' => 'required|string|max:255',
            'numero_ordre' => 'required|string|max:255|unique:medecins',
            'hopital' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
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
            'user_id.required' => 'L\'ID de l\'utilisateur est requis.',
            'user_id.exists' => 'L\'utilisateur spécifié n\'existe pas.',
            'specialite.required' => 'La spécialité est requise.',
            'specialite.string' => 'La spécialité doit être une chaîne de caractères.',
            'specialite.max' => 'La spécialité ne doit pas dépasser 255 caractères.',
            'numero_ordre.required' => 'Le numéro d\'ordre est requis.',
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