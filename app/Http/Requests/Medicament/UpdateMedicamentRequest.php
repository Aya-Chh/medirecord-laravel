<?php

namespace App\Http\Requests\Medicament;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicamentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin can update medicaments (according to spec: PUT/api/v1/medicaments/{id} -> admin)
        return $this->user()->can('update', \App\Models\Medicament::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'forme' => 'sometimes|required|in:comprime,sirop,injection,creme,autre',
            'dosage_disponible' => 'sometimes|nullable|json',
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
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'forme.in' => 'La forme doit être un des suivants : comprime, sirop, injection, creme, autre.',
            'dosage_disponible.json' => 'Le dosage disponible doit être un JSON valide.',
        ];
    }
}