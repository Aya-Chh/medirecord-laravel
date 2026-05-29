<?php

namespace App\Http\Requests\Medicament;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicamentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin can create medicaments (according to spec: POST/api/v1/medicaments -> admin)
        return $this->user()->can('create', \App\Models\Medicament::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'forme' => 'required|in:comprime,sirop,injection,creme,autre',
            'dosage_disponible' => 'nullable|json', // Stored as JSON, so we validate it as JSON
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
            'nom.required' => 'Le nom est requis.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'forme.required' => 'La forme est requise.',
            'forme.in' => 'La forme doit être un des suivants : comprime, sirop, injection, creme, autre.',
            'dosage_disponible.json' => 'Le dosage disponible doit être un JSON valide.',
        ];
    }
}