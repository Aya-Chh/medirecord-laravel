<?php

namespace App\Http\Requests\Consultation;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin and medecin can create consultations (according to spec: POST/api/v1/consultations -> admin, medecin)
        return $this->user()->can('create', \App\Models\Consultation::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'medecin_id' => 'required|exists:medecins,id',
            'patient_id' => 'required|exists:patients,id',
            'date_heure' => 'required|date',
            'motif' => 'required|string',
            'diagnostic' => 'nullable|string',
            'notes' => 'nullable|string',
            'statut' => 'sometimes|in:planifiee,en_cours,terminee,annulee',
            'duree_minutes' => 'sometimes|integer|min:1',
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
            'medecin_id.required' => 'L\'ID du médecin est requis.',
            'medecin_id.exists' => 'Le médecin spécifié n\'existe pas.',
            'patient_id.required' => 'L\'ID du patient est requis.',
            'patient_id.exists' => 'Le patient spécifié n\'existe pas.',
            'date_heure.required' => 'La date et heure sont requises.',
            'date_heure.date' => 'La date et heure doivent être une date valide.',
            'motif.required' => 'Le motif est requis.',
            'motif.string' => 'Le motif doit être une chaîne de caractères.',
            'diagnostic.string' => 'Le diagnostic doit être une chaîne de caractères.',
            'notes.string' => 'Les notes doivent être une chaîne de caractères.',
            'statut.in' => 'Le statut doit être un des suivants : planifiee, en_cours, terminee, annulee.',
            'duree_minutes.integer' => 'La durée doit être un nombre entier.',
            'duree_minutes.min' => 'La durée doit être supérieure à 0.',
        ];
    }
}