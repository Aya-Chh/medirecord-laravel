<?php

namespace App\Http\Requests\DossierMedical;

use Illuminate\Foundation\Http\FormRequest;

class StoreDossierMedicalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin and medecin can create dossiers medicaux (according to spec: POST/api/v1/dossiers -> admin, medecin)
        return $this->user()->can('create', \App\Models\DossierMedical::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'type' => 'required|in:rapport,analyse,imagerie,autre',
            'fichier_path' => 'nullable|string|max:255', // We'll handle file upload separately, but for now just a string
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
            'patient_id.required' => 'L\'ID du patient est requis.',
            'patient_id.exists' => 'Le patient spécifié n\'existe pas.',
            'consultation_id.exists' => 'La consultation spécifiée n\'existe pas.',
            'titre.required' => 'Le titre est requis.',
            'titre.string' => 'Le titre doit être une chaîne de caractères.',
            'titre.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'contenu.required' => 'Le contenu est requis.',
            'contenu.string' => 'Le contenu doit être une chaîne de caractères.',
            'type.required' => 'Le type est requis.',
            'type.in' => 'Le type doit être un des suivants : rapport, analyse, imagerie, autre.',
            'fichier_path.string' => 'Le chemin du fichier doit être une chaîne de caractères.',
            'fichier_path.max' => 'Le chemin du fichier ne doit pas dépasser 255 caractères.',
        ];
    }
}