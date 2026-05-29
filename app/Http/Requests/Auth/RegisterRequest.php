<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'nullable|in:patient,client,hopital,medecin',
            // Profile fields
            'date_naissance' => 'nullable|date',
            'genre' => 'nullable|in:homme,femme,autre',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'pays' => 'nullable|string|max:255',
            // Patient specific fields
            'groupe_sanguin' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
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
            'name.required' => 'Le nom est requis.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'email.required' => 'L\'email est requis.',
            'email.string' => 'L\'email doit être une chaîne de caractères.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.max' => 'L\'email ne doit pas dépasser 255 caractères.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est requis.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'phone.string' => 'Le téléphone doit être une chaîne de caractères.',
            'phone.max' => 'Le téléphone ne doit pas dépasser 20 caractères.',
            'date_naissance.required' => 'La date de naissance est requise.',
            'date_naissance.date' => 'La date de naissance doit être une date valide.',
            'genre.required' => 'Le genre est requis.',
            'genre.in' => 'Le genre doit être soit homme, femme ou autre.',
            'adresse.required' => 'L\'adresse est requise.',
            'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',
            'adresse.max' => 'L\'adresse ne doit pas dépasser 255 caractères.',
            'ville.required' => 'La ville est requise.',
            'ville.string' => 'La ville doit être une chaîne de caractères.',
            'ville.max' => 'La ville ne doit pas dépasser 255 caractères.',
            'pays.required' => 'Le pays est requis.',
            'pays.string' => 'Le pays doit être une chaîne de caractères.',
            'pays.max' => 'Le pays ne doit pas dépasser 255 caractères.',
            'groupe_sanguin.required' => 'Le groupe sanguin est requis.',
            'groupe_sanguin.in' => 'Le groupe sanguin doit être un des suivants : A+, A-, B+, B-, AB+, AB-, O+, O-.',
            'allergies.string' => 'Les allergies doivent être une chaîne de caractères.',
            'antecedents.string' => 'Les antécédents doivent être une chaîne de caractères.',
        ];
    }
}
